<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 12/11/14
 * Time: 12:36
 */

namespace Apprecie\Library\Model;

use Apprecie\Library\Adapters\HTMLEncodeAdapter;
use Apprecie\Library\Adapters\HTMLEntityAdapter;
use Apprecie\Library\Collections\CanRegister;
use Apprecie\Library\Collections\IsRegistry;
use Apprecie\Library\Collections\Registry;
use Apprecie\Library\DBConnection;
use Apprecie\Library\Messaging\MessageQueue;
use Apprecie\Library\Search\SearchFilter;
use Apprecie\Library\Security\EncryptionManager;
use Apprecie\Library\Security\EncryptionProvider;
use Apprecie\Library\Tracing\ActivityTraceTrait;
use Apprecie\Library\Utility\UtilityTrait;
use Phalcon\Db\RawValue;
use Phalcon\DI;
use Phalcon\Exception;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Message;
use Phalcon\Mvc\Model\Transaction\Manager;
use Phalcon\Security;

/**
 * Provides additional functionality to extending classes and works around some know Phalcon issues.
 * It is expected that Apprecie application models will extend this base class.
 *
 * Provides field level encryption.
 * mysql / pdo / phalcon ORM  - bit as string overflow issue work around
 * phalcon / pdo  mysql default values workaround
 *
 * In the case of the bit as string issue, the handling and work around is automatic.
 * Simply ensure to use bool :
 * <code>
 * $myObject->yesNoValue = true;
 * </code>
 *
 * For encrypted fields pass the names of the fields to be encrypted to setEncryptedFields()
 * Beyond this encryption and decryption of these fields is now automatic on the assumption that
 * in process is decrypted and at rest is encrypted.
 *
 * For default fields, set the fields to assume defaults on create using setDefaultFields() and you will
 * be able to leave this fields with no value on a create action, and the db defined value will be assumed.
 *
 * For find and findFirst operations use findFirstBy and findBy for automatic encryption handling.
 * This means that queries must be exact (not LIKE) and will compare encrypted form to encrypted form.
 *
 * Class ApprecieModelBase
 * @package Apprecie\Library\Model
 */
abstract class ApprecieModelBase extends Model implements CanRegister
{
    use ActivityTraceTrait;
    use DBConnection;
    use UtilityTrait;

    protected $_defaultFields = array();
    protected $_encryptedFields = array();
    protected $_bitFields = array();
    protected $_isDecrypted = true;
    protected $_encryptionProvider = null;
    protected $_hash = null;
    protected $_parentIsTableBase = false;
    protected $_parentTransaction = null;
    protected $_foreignKeyField = null;
    protected $_languageId = null;
    protected $_indirectContentFields = array();
    protected $_contentFieldMacros = array();
    protected $_htmlEncodeAuto = false;
    protected $_htmlEntitiesAuto = false;
    protected $_ident = null;


    public function setAutoHtmlEncode($value)
    {
        $this->_htmlEncodeAuto = $value;
    }

    protected function setIndirectContentFields(array $fields)
    {
        if (!is_array($fields)) {
            $fields = array($fields);
        }

        $this->_indirectContentFields = $fields;
    }

    public function writeToLog($subject, $detail)
    {
        $log = DI::getDefault()->get('activitylog');
        $detail = '[From ' . get_called_class() . '] ' . $detail;
        $log->logActivity($subject, $detail);
    }

    public function onValidationFails()
    {
        $this->writeToLog('Model Validation failure', _ms($this->getMessages()));
    }

    protected function getIndirectContentFields()
    {
        return $this->_indirectContentFields;
    }

    /**
     * Any existing content macros will be copied out and stored and replaced with their languageId actual content.
     * Any content from indirect fields that does not have a valid macro will be created
     *
     * @todo   This is non performant, as it called a lot,  it would be a good improvement to cache the read results
     * and to prevent double processing in inherited models.   Look at these improvements if performance becomes an issue.
     * i.e prime to a ready for use state
     */
    protected function primeIndirectContent()
    {
        foreach ($this->_indirectContentFields as $field) {
            $resolver = $this->getDI()->get('contentresolver');

            if (!array_key_exists($field, $this->_contentFieldMacros)) {
                $this->_contentFieldMacros[$field] = null;
            }

            if ($resolver->isMacro($this->$field)) {
                $this->_contentFieldMacros[$field] = $this->$field;
            } elseif (isset($this->$field)) {
                $macro = $resolver->resolveWrite(
                    $this->$field,
                    $this->getLanguageId(),
                    $this->_contentFieldMacros[$field],
                    null,
                    $this->getIdentity() . '_' . $field
                );

                if ($macro === false) {
                    $this->appendMessageEx($resolver);
                } else {
                    $this->_contentFieldMacros[$field] = $macro;
                }
            }

            $this->$field = _c($this->_contentFieldMacros[$field], $this->getLanguageId(), true);

            /*if($this->getIdent() == 'Event_1') {
                if($field == 'description') {
                    _ep('description value is ' . $this->$field . ';');
                }
            }*/
        }
    }

    /**
     * Restores the macros to the fields
     * i.e.  returns to a resting storage state.
     */
    protected function preSaveIndirectContent()
    {
        foreach ($this->_indirectContentFields as $field) {
            if (array_key_exists($field, $this->_contentFieldMacros)) {
                if ($this->getDI()->get('contentresolver')->isMacro($this->_contentFieldMacros[$field])) {
                    $this->$field = $this->_contentFieldMacros[$field];
                }
            }
        }
    }

    public function changeIndirectMacroFormat()
    {
        $indirections = $this->getIndirectContentFields();

        foreach($indirections as $field) {
            $this->$field = str_replace('{c:', '', $this->$field);
            $this->$field = str_replace('}', '', $this->$field);
        }
    }

    public function getHasIndirectContent()
    {
        return count($this->_indirectContentFields) > 0;
    }

    /**
     * Deletes all related indirect content in current language,  pass true to destroy, to purge all languages
     * @param bool $destroyAll
     * @return bool
     */
    protected function purgeIndirectContent($destroyAll = false)
    {
        if ($this->getHasIndirectContent()) {
            $this->getDbAdapter()->begin();
            foreach ($this->_indirectContentFields as $field) {
                $macro = $this->_contentFieldMacros[$field];
                $this->getDI()->get('contentresolver')->deleteContent(
                    $macro,
                    $destroyAll ? -1 : $this->getLanguageId()
                );
                $this->$field = '';
            }

            if (!$this->getDbAdapter()->commit()) {
                $this->appendMessageEx('Failed to commit purge transactions');
                return false;
            }
        }
        return true;
    }

    public function getLanguageId()
    {
        if ($this->_languageId == null) {
            return _l();
        }

        return $this->_languageId;
    }

    /**
     * Note that if you do not set a specific language id, the model will operate in the current UI language,  this
     * could be an issue if you opened for English content on a French UI,  if you did not provide the the explicit ID
     * the model would save content as French matching the UI language.
     *
     * @param $languageId The id of the language for this models dynamic content fields
     */
    public function setLanguageId($languageId)
    {
        $this->_languageId = $languageId;
        $this->preSaveIndirectContent();
        $this->primeIndirectContent();
    }

    public function setParentIsTableBase($value)
    {
        $this->_parentIsTableBase = $value;
    }

    public function getParentIsTableBase()
    {
        return $this->_parentIsTableBase;
    }

    #region properties / getters / setters

    protected function getForeignKeyField()
    {
        if ($this->_foreignKeyField == null) {
            $this->calculateForeignKeyField();
        }

        return $this->_foreignKeyField;
    }

    protected function getForeignKey()
    {
        if (!$this->_parentIsTableBase) {
            return null;
        }

        $field = $this->getForeignKeyField();

        return $this->$field;
    }

    public function setForeignKey($value)
    {
        if ($this->_parentIsTableBase) {
            $field = $this->getForeignKeyField();
            $this->$field = $value;
        }
    }

    public function getHash(Registry $registry = null)
    {
        if ($this->_hash == null) {
            $this->_hash = uniqid(spl_object_hash($this), true);
        }

        return $this->_hash;
    }

    /**
     * @return boolean true if all fields on this object are decrypted false if any are encrypted.
     */
    public function getIsDecrypted()
    {
        return $this->_isDecrypted;
    }

    /**
     * Phalcon ORM will currently insist on values for db defaults that are not nullable,
     * if you would rather specify an item to take the database assigned default on creation then
     * pass the name of the field, or an array of such fields.
     *
     * Call in onConstruct()  and always call parent::onConstruct() or simply before create() save()
     * @param string|array $default The fields or fields on this model that should assume db defaults on insert
     */
    public function setDefaultFields($default)
    {
        if (!is_array($default)) {
            $default = array($default);
        }
        $this->_defaultFields = $default;
    }

    /**
     * Set the fields of this model that should be encrypted.
     * These fields will automatically undergo encryption before storage, and decryption after fetch.
     *
     * See setEncryptionProvider()  and getEncryptionKey()
     *
     * @param string|array $encrypted The field or fields in this model that should be encrypted
     */
    public function setEncryptedFields($encrypted)
    {
        if (!is_array($encrypted)) {
            $encrypted = array($encrypted);
        }
        $this->_encryptedFields = $encrypted;
    }

    /**
     * @return array the name sof the fields that are set as encrypted on this model
     */
    public function getEncryptedFields()
    {
        return $this->_encryptedFields;
    }

    /**
     * @return array The names of the fields of this model set to accept database defaults
     */
    public function getDefaultFields()
    {
        return $this->_defaultFields;
    }

    /**
     * Encryption for a model defaults to the facilities of the Encryption class which currently uses
     * a MCRYPT_RIJNDAEL_256 cipher (AES 256)
     * If this is appropriate then you are not required to give an explicit encryption provider.
     *
     * These decisions should be made at model design time.
     * DO NOT CHANGE ENCRYPTION PROVIDER WITH LIVE ENCRYPTED DATA
     * @param EncryptionProvider $provider An instance of an EncryptionProvider
     */
    public function setEncryptionProvider(EncryptionProvider $provider)
    {
        $this->getDI()->get('encRegistry')->setInstance($this, $provider);
    }

    /**
     * @return EncryptionProvider Returns either the explicitly set, or default EncryptionProvider for this model
     */
    public function getEncryptionProvider()
    {
        return $this->getDI()->get('encRegistry')->getInstance($this);
    }

    /**
     * override this for custom provider
     * @return EncryptionProvider
     */
    public function createEncryptionProvider()
    {
        return EncryptionManager::get($this->getEncryptionKey());
    }

    /**
     * Override this getter to provide a custom key.  Currently returns
     * portal guid + ??
     * @return string return the encryption key to be used in encryption operations
     */
    protected function getEncryptionKey()
    {
        $key1 = $this->getDI()->get('portalid');
        $key2 = $this->getDI()->get('fieldkey');
        return $key1 . $key2;
    }

    #endregion

    #region public methods

    public function register(IsRegistry $register, $key, $name)
    {
        if ($name == 'modelencryption') {
            $register->setInstance($key, $this->createEncryptionProvider());
        }
    }

    /**
     * Returns true if $field is marked for encryption in this model.  Note that this method does not indicate
     * the encrypted / decrypted state, please see getIsDecrypted() for that purpose.
     * <code>
     * $isCurrentlyEncrypted = $obj->isEncryptionField('myfield') && !$obj->getIsDecrypted()
     * </code>
     *
     * @param $field string The name of the field to check
     * @return bool true if the field is marked as encrypted else false
     */
    public function isEncryptionField($field)
    {
        return in_array($field, $this->getEncryptedFields());
    }

    /**
     * Encrypts fields indicated as encrypted, see setEncryptedFields().
     * Uses the EncryptionProvider provided by getEncryptionProvider() and the key provided by getEncryptionKey()
     * Sensible defaults are provided.
     *
     * This method is called implicitly during update and create actions.
     *
     * Note that after an update or create or validation action the object will be left in an
     * unencrypted state, it will not be returned to its former state.
     * An encrypted object will not double encrypt or double decrypt, this method has no effect on an encrypted
     * object.
     * Check the current encryption state by calling getIsDecrypted()
     */
    public function encryptFields()
    {
        if (count($this->_encryptedFields) > 0 && $this->_isDecrypted) {

            $encrypt = $this->getEncryptionProvider();

            foreach ($this->_encryptedFields as $field) {
                if (isset($this->$field) && is_string($this->$field)) {
                    $this->$field = $encrypt->encrypt($this->$field);
                }
            }

            $this->_isDecrypted = false;
        }
    }

    /**
     * Decrypts fields indicated as encrypted, see setEncryptedFields().
     * Uses the EncryptionProvider provided by getEncryptionProvider() and the key provided by getEncryptionKey()
     * Sensible defaults are provided.
     *
     * This method is called implicitly before validation and after fetch operations
     *
     * Note that after an update or create or validation action the object will be left in an
     * unencrypted state, it will not be returned to its former state.
     * An encrypted object will not double encrypt or double decrypt, this method has no effect on an encrypted
     * object.
     * Check the current encryption state by calling getIsDecrypted()
     */
    public function decryptFields()
    {
        if (count($this->_encryptedFields) > 0 && !$this->_isDecrypted) {

            $encrypt = $this->getEncryptionProvider();

            foreach ($this->_encryptedFields as $field) {
                if (isset($this->$field) && is_string($this->$field)) {
                    $this->$field = mb_convert_encoding($encrypt->decrypt($this->$field), 'UTF-8', 'UTF-8');
                }
            }

            $this->_isDecrypted = true;
        }
    }

    #endregion

    #region protected methods
    /**
     * Loops on set default fields, and sets them to \Phalcon\Db\RawValue('default') rather than raw value.
     * Default fields can be set through setDefaultFields() a default field is a field that will take a db
     * defined default value on record creation.
     *
     * Is called implicitly during create actions in beforeValidationOnCreate()
     *
     * @param bool $onlyIfNull Default true - only setup default fields to take a default value if currently no value
     */
    protected function processDefaultValues($onlyIfNull = true)
    {
        foreach ($this->_defaultFields as $field) {
            $setDefault = !$onlyIfNull ? true : is_null($this->$field);
            if ($setDefault) {
                $this->$field = new RawValue('default');
            }
        }
    }

    /**
     * Converts bool true and false to RawValue(1 | 0) to overcome truncation issue
     * when pdo sends a string.  Operates on changed fields only.
     * Called implicitly before update and create actions.
     * Undone by calling unsetBitValues() also called implicitly after update or fetch
     */
    protected function processBitValues()
    {
        $fields = $this->getModelsMetaData()->getAttributes($this);
        $types = $this->getModelsMetaData()->getDataTypes($this);

        foreach ($fields as $field) { // = The record doesn't have a valid data snapshot at
            if ($types[$field] == 8) { //seems that bits are set as strings (type 8) this should help.
                if ($this->$field == '1') {
                    $this->$field = true;
                } elseif ($this->$field == '0') {
                    $this->$field = false;
                }
            }

            if (isset($this->$field) && is_bool($this->$field)) {
                $value = $this->$field === true ? 1 : 0;
                $this->$field = new RawValue("{$value}");
                $this->_bitFields[] = $field;
            }
        }
    }

    /**
     * Returns RawValue() bit fields back into an actual int number 1 / 0
     * called implicitly after update or fetch
     */
    protected function unsetBitValues()
    {
        foreach ($this->_bitFields as $field) {
            if ($this->$field instanceof RawValue) {
                $val = $this->$field->getValue();
                if (is_numeric($val)) {
                    if (is_int($val)) {
                        $this->$field = (int)$val;
                    } elseif (is_float($val)) {
                        $this->$field = (float)$val;
                    }
                } else {
                    $this->$field = $val;
                }
            }
        }

        $this->_bitFields = array();
    }

    /**
     * Extracts properties using a get method for each $field.
     * Returns an associative array.  Like toArray()  but deeper.
     *
     * @param array $fields
     * @return array
     */
    public function getExtractToArray(array $fields)
    {
        $dataPackage = [];

        foreach ($fields as $field) { //toArray does not return the protected parent field data, so we extract
            $getMethod = 'get' . ucfirst($field);
            $dataPackage[$field] = $this->$getMethod();
        }

        return $dataPackage;
    }

    protected function updateForeignKey(ApprecieModelBase $parentInstance)
    {
        $getMethod = 'get' . ucfirst($this->getForeignKeyField());
        $this->setForeignKey($parentInstance->$getMethod());
    }

    protected function getParentInstance()
    {
        if (!$this->_parentIsTableBase) {
            return null;
        }

        $parent = get_parent_class(get_called_class());

        $instance = $parent::findFirstBy($this->getForeignKeyField(), $this->getForeignKey());

        if ($instance == null) {
            throw new \LogicException($this->getForeignKeyField()
                . 'The underlying base record or type '
                . $parent . ' could not be found using key field '
                . ' and key ' . $this->getForeignKey());
        }

        return $instance;
    }

    protected function calculateForeignKeyField()
    {
        if ($this->_parentIsTableBase && $this->_foreignKeyField == null) {
            $parent = get_parent_class(get_called_class());
            $parentInstance = new $parent();

            //find the foreign key
            $key = $parentInstance->getModelsMetaData()->getPrimaryKeyAttributes($parentInstance);

            if (count($key) != 1) {
                throw new Exception('TPT Inheritance only works with tables with a single primary key and a matching foreign key');
            }

            $this->_foreignKeyField = $key[0];
        }
    }

    protected function updateFromParent()
    {
        if ($this->_parentIsTableBase) {
            $parent = $this->getParentInstance();


            foreach($parent->toArray() as $field=>$val) {
                $this->$field = $val;
            }

            //$this->assign($parent->toArray(), null);
        }
    }

    public function assignEx($values)
    {
        foreach($values as $field=>$val) {
            $this->$field = $val;
        }
    }

    /**
     * Note that this method ignored the models setAutoHTMLEncode() state and operates via the htmlEncode param in the
     * signature.
     *
     * @param array $forceFields
     * @param bool $htmlEncode
     * @param bool $htmlEntities
     * @return array
     */
    public function toArrayEx(Array $forceFields = null, $htmlEncode = false, $htmlEntities = false)
    {
        $array = [];

        if ($this->_parentIsTableBase) {
            $parent = $this->getParentInstance();
            $array = $parent->toArray();
        }

        if ($forceFields != null) {
            foreach ($forceFields as $field) {
                $array[$field] = $this->$field;
            }
        }

        $array = array_merge($array, $this->toArray());

        if($htmlEncode) {
            foreach($array as $field => &$value) {
                $value = _eh($value);
            }
        }

        if($htmlEntities) {
            foreach($array as $field => &$value) {
                $value = _s($value);
            }
        }

        return $array;
    }

    public function toArray($columns = null)
    {
        $array = parent::toArray($columns);


        if($this->_htmlEncodeAuto) {
            foreach($array as $field => &$value) {
                $value = _eh($value);
            }
        }

        if($this->_htmlEntitiesAuto) {
            foreach($array as $field => &$value) {
                $value = _s($value);
            }
        }

        return $array;
    }

    public function getHTMLEncodeAdapter($excludes = null)
    {
        $adapter = new HTMLEncodeAdapter($this, $excludes);
        return $adapter;
    }

    public function getHTMLEntitiesAdapter($excludes = null)
    {
        $adapter = new HTMLEntityAdapter($this, $excludes);
        return $adapter;
    }

    #endregion

    #region event handlers / overrides

    public function onConstruct()
    {
        $this->useDynamicUpdate(true);
    }

    public function beforeValidationOnCreate()
    {
        $this->primeIndirectContent();

        if ($this->_parentIsTableBase) {

            $this->_parentTransaction = (new Manager())->get();

            $parent = get_parent_class(get_called_class());
            $parentInstance = new $parent();

            $parentInstance->setTransaction($this->_parentTransaction);

            try {
                $data = $this->getExtractToArray($parentInstance->getModelsMetaData()->getAttributes($parentInstance));

                if (!$parentInstance->create($data)) {
                    $this->appendMessageEx($parentInstance);
                    return false;
                }

                $this->updateForeignKey($parentInstance);
            } catch (Exception $ex) {
                $this->appendMessageEx($ex);
                return false;
            }
        }

        $this->decryptFields();
        $this->processDefaultValues();

        return true;
    }

    public function beforeValidationOnUpdate()
    {
        $this->decryptFields();
        $this->primeIndirectContent();
    }

    public function beforeUpdate()
    {
        $this->encryptFields();
        $this->processBitValues();

        if ($this->_parentIsTableBase) {
            $this->_parentTransaction = (new Manager())->get();
            $parentInstance = $this->getParentInstance();

            $parentInstance->setTransaction($this->_parentTransaction);

            try {
                $data = $this->getExtractToArray($parentInstance->getModelsMetaData()->getAttributes($parentInstance));

                if (!$parentInstance->update($data)) {
                    $this->appendMessageEx($parentInstance);
                    return false;
                }
            } catch (Exception $ex) {
                $this->appendMessageEx($ex);
                return false;
            }

            $this->setTransaction($this->_parentTransaction);
        }

        $this->preSaveIndirectContent();

        return true;
    }

    public function beforeCreate()
    {
        $this->encryptFields();
        $this->preSaveIndirectContent();
        $this->processBitValues();


        if ($this->_parentIsTableBase) {
            $this->setTransaction($this->_parentTransaction);
        }
    }

    public function afterCreate()
    {
        if ($this->_parentIsTableBase) {
            try {
                $this->_parentTransaction->commit();
                $this->updateFromParent();
            } catch (Exception $ex) {
                $this->appendMessageEx($ex);
                $this->_parentTransaction->rollback('The entire transaction could not completed');
            }
        }

        $this->primeIndirectContent();
    }

    public function beforeDelete()
    {
        if ($this->_parentIsTableBase) {
            $this->_parentTransaction = (new Manager())->get();

            try {
                $parent = $this->getParentInstance();
            } catch(\Exception $ex) { //parent already deleted

            }

            if ($parent != null) {
                $parent->setTransaction($this->_parentTransaction);
                if (!$parent->delete()) {
                    $this->appendMessageEx($parent);
                    return false;
                }
            }

            $this->setTransaction($this->_parentTransaction);
        }

        $this->purgeIndirectContent(true);

        return true;

    }

    public function afterDelete()
    {
        if ($this->_parentIsTableBase && $this->_parentTransaction != null) {
            try {
                $this->_parentTransaction->commit();
            } catch (Exception $ex) {
                $this->appendMessageEx($ex);
                $this->_parentTransaction->rollback('The entire transaction could not completed.');
            }
        }
    }

    public function afterFetch()
    {
        $this->_hash = null;
        if (count($this->_encryptedFields) > 0) {
            $this->_isDecrypted = false;
        }

        $this->unsetBitValues();
        $this->primeIndirectContent();
        $this->decryptFields();

        $this->updateFromParent();
    }

    public function afterUpdate()
    {
        $this->unsetBitValues();
        $this->decryptFields();

        if ($this->_parentIsTableBase && $this->_parentTransaction != null) {
            try {
                $this->_parentTransaction->commit();
                $this->updateFromParent();
            } catch (Exception $ex) {
                $this->appendMessageEx($ex);
                $this->_parentTransaction->rollback('The entire transaction could not completed');
            }
        }

        $this->primeIndirectContent();
    }

    /**
     * @todo Gav - NOTE THAT FOR PHALCON 2.x compatability must add Model\MessageInterface as type to $message in sig
     * @param Model\MessageInterface $message
     */
    public function appendMessageEx($message)
    {
        if (is_array($message) || $message instanceof \ArrayAccess) {
            foreach ($message as $msg) {
                $this->appendMessageEx($msg); //recursion
            }
        } elseif ($message instanceof MessageQueue) {
            foreach ($message->getMessages() as $message) {
                $this->appendMessageEx($message); //recursion
            }
        } elseif ($message instanceof Model) {
            $this->appendMessageEx($message->getMessages());
        } else {
            if (is_string($message)) {
                $message = new Message($message);
            } elseif ($message instanceof Exception) {
                $message = new Message($message->getMessage());
            }

            parent::appendMessage($message);
        }
    }

    #endregion

    #region static methods


    /*
        public static function cloneResultMap(
            $base,
            Array $data,
            $columnMap,
            $dirtyState = null,
            $keepSnapshots = null
        ) {
            _m();
            //_ep(get_called_class());
            if (get_called_class() == 'Event') {
                $parent = get_parent_class(get_called_class());
                if (class_exists($parent)) {
                    $base = new $parent();

                    //_m();
                }
            }
            return parent::cloneResultMap(
                $base,
                $data,
                $columnMap,
                $dirtyState,
                $keepSnapshots
            );
        } */

    /**
     * returns the first matching result on $field given $values
     * wraps the ORM findFirst() method but is encryption aware, and will or encrypt $values
     * if the query field should be encrypted.
     *
     * @param $field string The name of the single field to search on
     * @param $values string|array A scalar or array of values to find a match for
     * @param ApprecieModelBase $instance
     * @return mixed
     */
    public static function findFirstBy(
        $field,
        $values,
        ApprecieModelBase $instance = null
    ) {
        $class = get_called_class();
        $values = $class::prepareSearchValues($field, $values, $instance);

        $count = 0;

        foreach($values as $val) {
            $conditions = $field . ' = ?' . $count . ' ';
            $count++;
        }

        $params = [
            'columns'    => '*',
            'conditions' => $conditions,
            'bind'       => $values
        ];

        return static::findFirst($params);
    }

    /**
     * returns the matching results on $field given $values
     * wraps the ORM findFirst() method but is encryption aware, and will or encrypt $values
     * if the query field should be encrypted.
     *
     * @param $field string The name of the single field to search on
     * @param $values string|array A scalar or array of values to find a match for
     * @param null $orderBy
     * @param ApprecieModelBase $instance
     * @return mixed
     */
    public static function findBy(
        $field,
        $values,
        $orderBy = null,
        ApprecieModelBase $instance = null
    ) {
        $class = get_called_class();
        $values = $class::prepareSearchValues($field, $values, $instance);
        $count = 0;

        foreach($values as $val) {
            $conditions = $field . ' = ?' . $count . ' ';
            $count++;
        }

        $params = [
            'columns'    => '*',
            'conditions' => $conditions,
            'bind'       => $values
        ];

        if($orderBy != null) {
            $params['order'] = $orderBy;
        }

        return static::find($params);
    }

    public static function findByFilter(SearchFilter $filter, $orderBy = null, $groupBy = null, $limit = null)
    {
        $queryBuilder = static::getQueryBuilderFromFilter($filter, $orderBy, $groupBy, $limit);
        $content = $queryBuilder->getQuery()->execute();

        return $content;
    }

    public static function getQueryBuilderFromFilter(SearchFilter $filter, $orderBy = null, $groupBy = null, $limit = null)
    {
        $filters = $filter->getFilters();
        $joins = $filter->getJoins();

        $class = get_called_class();
        $instance = new \User();
        $first = true;


        $query = $instance->getModelsManager()->createBuilder()->addFrom($class);
        $query->distinct(true);

        foreach ($joins as $join) {
            list($type, $model, $condition, $alias) = $join;

            if ($type == 'inner') {
                $query->innerJoin($model, $condition, $alias);
            } elseif ($type == 'left') {
                $query->leftJoin($model, $condition, $alias);
            } elseif ($type == 'right') {
                $query->rightJoin($model, $condition, $alias);
            } else {
                throw new \Exception('non recognised join type ' . $type);
            }
        }

        $paramCount = 0;
        if(! is_array($filters)) {
            $filters = [];
        }
        foreach ($filters as $filter) {
            list($relation, $originalField, $operator, $value, $alias, $negate) = $filter;

            if ($operator != 'is null') {
                if ($alias == null) {
                    $value = $class::prepareSearchValues($originalField, $value, $instance);
                } else {
                    $value = $alias::prepareSearchValues($originalField, $value);
                }
            }

            if (is_array($value) && count($value) == 1) {
                $value = $value[0];
            }

            $field = $originalField;
            $originalField = $originalField . $paramCount;

            if ($alias != '') {
                $field = $alias . '.' . $field;
            }

            if ($operator == 'is null' && $negate) {
                $operator = 'is not null';
            } elseif ($negate && $operator != 'in') {
                $operator .= ' not ' . $operator;
            }

            if ($first) {
                if ($operator == 'is null' || $operator == 'is not null') {
                    $query->where("{$field} {$operator}");
                } elseif ($operator == 'in') {
                    $negate ? $query->notInWhere($field, $value) : $query->inWhere($field, $value);
                } else {
                    $query->where("{$field} {$operator} :{$originalField}:", [$originalField => $value]);
                }

                $first = false;
            } else {
                if ($relation == 'and') {
                    if ($operator == 'is null' || $operator == 'is not null') {
                        $query->andWhere("{$field} {$operator}");
                    } else {
                        $query->andWhere("{$field} {$operator} :{$originalField}:", [$originalField => $value]);
                    }
                } else {
                    if ($relation == 'or') {
                        if ($operator == 'is null' || $operator == 'is not null') {
                            $query->orWhere("{$field} {$operator}");
                        } else {
                            $query->orWhere("{$field} {$operator} :{$originalField}:", [$originalField => $value]);
                        }
                    } else {
                        if ($relation == 'in') {
                            $negate ? $query->notInWhere($field, $value) : $query->inWhere($field, $value);
                        } else {
                            throw new \Exception('Unknown relation ' . $relation . ' in findByFilter()');
                        }
                    }
                }
            }

            $paramCount++;
        }

        if ($orderBy != null) {
            $query->orderBy($orderBy);
        }
        if ($groupBy != null) {
            $query->groupBy($groupBy);
        }
        if ($limit != null) {
            $query->limit($limit);
        }
        return $query;
    }

    /**
     * will encrypt $values if $field is marked for encryption.
     * Use for preparing values before running a query.
     *
     * @param $field string The name of the field
     * @param $values string|array  The value or an array of values
     * @param ApprecieModelBase $instance
     * @return array The values as passed, or encrypted if required
     */
    public static function prepareSearchValues($field, $values, ApprecieModelBase $instance = null)
    {
        if (!is_array($values)) {
            $values = array($values);
        }

        if ($instance == null) {
            $class = get_called_class();
            $instance = new $class();
        }

        foreach ($values as &$val) {
            if ($instance->isEncryptionField($field)) { //encrypt the values before query
                $val = $instance->getEncryptionProvider()->encrypt($val);
            }
        }

        return $values;
    }

    /**
     * useful for resolving an id or object to an actual Object.
     *
     * In base form resolves if $param is an instance of this model, if so returns it, else
     * checks if $param could be the id of this model, and will return the look up.
     *
     * @param $param mixed|ApprecieModelBase an instance of a model or the id for a record of this model
     * @param bool $throw
     * @param \Apprecie\Library\Model\ApprecieModelBase|null $instance If not provided a default of instance of get_called_class() will be used for model meta
     * @throws \InvalidArgumentException
     * @throws \Phalcon\Exception
     * @return object|null return if possible the actual object referenced, else null
     */
    public static function resolve($param, $throw = true, ApprecieModelBase $instance = null)
    {
        $class = get_called_class();
        if ($param instanceof $class) {
            return $param;
        }

        if ($instance == null) {
            $instance = new $class();
        }

        $key = $instance->getModelsMetaData()->getPrimaryKeyAttributes($instance);
        if (count(
                $key
            ) != 1
        ) {
            throw new Exception('Composit key models and non primary key not implemented for resolution in model base.  Please implement');
        }

        $item = $class::findFirstBy($key[0], $param, $instance);

        if ($item == null && $throw) {
            throw new \InvalidArgumentException('The requested ' . $class . ' could not be resolved to a record or object');
        }

        return $item;
    }

    public static function findAll($orderBy = null)
    {
        $class = get_called_class();
        return $class::find(['order'=>$orderBy]);
    }

    /**
     * @param string $conditions something = ? and somethingelse = ?
     * @param null $params array of params in condition order
     * @return Model\Resultset\Simple
     */
    public static function findBySql($conditions, $params = null, $cacheKey = null, $lifeSpan = 3600)
    {
        $cache = DI::getDefault()->get('cache');
        $content = null;

        if ($cacheKey != null && $lifeSpan == 0) {
            $cache->delete($cacheKey);
        } elseif ($cacheKey != null && is_numeric($lifeSpan)) {
            $content = $cache->get($cacheKey);
        }

        if ($content == null) {
            $class = get_called_class();
            $instance = new $class();
            $sql = 'SELECT * FROM ' . $instance->getSource() . " WHERE {$conditions}";

            $content = new Model\Resultset\Simple(null, $instance, $instance->getReadConnection()->query($sql, $params));

            if ($cacheKey != null && is_int($lifeSpan) && $lifeSpan > 0) {
                $cache->save($cacheKey, $content, $lifeSpan);
            }
        }

        return $content;
    }

    public function hasMessages()
    {
        return count($this->getMessages()) > 0;
    }

    public function getIdentity($seperator = '_') {
        if($this->_ident == null) {
            $class = get_called_class();
            $instance = new $class();
            $keys = $instance->getModelsMetaData()->getPrimaryKeyAttributes($instance);

            $ident = get_called_class();

            foreach ($keys as $key) {
                $ident .= $seperator . $this->$key;
            }

            $this->_ident = $ident;
        }

        return $this->_ident;
    }

    #endregion
}
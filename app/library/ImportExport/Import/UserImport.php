<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 11/02/15
 * Time: 15:11
 */

namespace Apprecie\Library\ImportExport\Import;

use Apprecie\Library\DBConnection;
use Apprecie\Library\Security\Authentication;
use Apprecie\Library\Users\UserEx;
use Phalcon\Db\RawValue;
use Phalcon\Exception;

class UserImport extends FileImport
{
    use DBConnection;

    protected static $_fields = [
        'reference',
        'title',
        'firstname',
        'lastname',
        'emailaddress',
        'gender'
    ]; /* set fields in here */
    protected $_signupEmails;
    protected $_failedSignups = array();
    protected $_generateRegistrationHash;

    public function __construct($csvFilePath, $sendSignupEmails = false, $generateRegistrationHash = false)
    {
        parent::__construct($csvFilePath);
        $this->_signupEmails = $sendSignupEmails;
        $this->_generateRegistrationHash = $generateRegistrationHash;
    }

    /**
     * Call after an import in which you choose to send out signup emails.
     *
     * The result is an array of ['User'=>userId, 'failReason'=>reason]
     *
     * @return array
     */
    public function getSignupFailures()
    {
        return $this->_failedSignups;
    }

    public function validateImport()
    {
        if ($this->hasMessages()) {
            return false;
        }

        $rowCount = 0;
        $headers = true;
        $fieldCount = count(static::$_fields);

        foreach ($this->_rows as $row) {
            if (!$headers) {
                if (count($row) != $fieldCount) {
                    $this->appendMessageEx(
                        _g(
                            'The row at line {rowCount} has an incorrect number of fields, expected {correctNumber} found {actualNumber}',
                            ['rowCount' => $rowCount, 'correctNumber' => $fieldCount, 'actualNumber' => count($row)]
                        )
                    );
                } else {
                    if (!empty($row['emailaddress']) && !filter_var($row['emailaddress'], FILTER_VALIDATE_EMAIL)) {
                        $this->appendMessageEx(
                            _g(
                                'The row at line {rowCount} has an invalid email address, email address if provided must be a valid email',
                                ['rowCount' => $rowCount]
                            )
                        );
                    }

                    if ((empty($row['firstname']) || empty($row['lastname'])) && empty($row['reference'])) {
                        $this->appendMessageEx(
                            _g(
                                'The row at line {rowCount} requires either a reference or first name and last name',
                                ['rowCount' => $rowCount]
                            )
                        );
                    }

                    if (!empty($row['gender'])) {
                        $gender = strtolower($row['gender']);
                        if ($gender != 'male' && $gender != 'female') {
                            $this->appendMessageEx(
                                _g(
                                    'The row at line {rowCount} has a gender field, but the date is not male or female',
                                    ['rowCount' => $rowCount]
                                )
                            );
                        }
                    }
                }
            } else {
                $headers = false;
            }

            $rowCount++;
        }

        $this->_hasValidated = true;

        return !$this->hasMessages();
    }

    public function commitImport()
    {
        if ($this->hasMessages()) {
            throw new Exception('The import contains errors - check messages');
        }

        if ($this->_hasValidated == false) {
            throw new Exception('Please call validateImport before commit');
        }

        $rowCount = 0;
        $headers = true;
        $fieldCount = count(static::$_fields);
        $userEx = new UserEx();
        $users = array();

        $this->getDbAdapter()->getInternalHandler()->beginTransaction();

        foreach ($this->_rows as $row) {
            if (!$headers) {
                if (count($row) != $fieldCount) {
                    $this->appendMessageEx(
                        _g(
                            'The row at line {rowCount} has an incorrect number of fields, expected {correctNumber} found {actualNumber}',
                            ['rowCount' => $rowCount, 'correctNumber' => $fieldCount, 'actualNumber' => count($row)]
                        )
                    );
                } else {
                    $user = $userEx->createUserWithProfileAndLogin
                        (
                            !empty($row['emailaddress']) ? $row['emailaddress'] : null,
                            null,
                            !empty($row['firstname']) ? $row['firstname'] : '',
                            !empty($row['lastname']) ? $row['lastname'] : '',
                            !empty($row['title']) ? $row['title'] : new RawValue('default'),
                            null,
                            !empty($row['reference']) ? $row['reference'] : '',
                            null,
                            null,
                            true
                        );

                    if ($user !== false) {
                        $user->setTier(-1);

                        $contactPreferences = $user->getUserContactPreferences();
                        $contactPreferences->setAlertsAndNotifications(true);
                        $contactPreferences->setInvitations(true);
                        $contactPreferences->setSuggestions(true);
                        $contactPreferences->setPartnerCommunications(true);
                        $contactPreferences->setUpdatesAndNewsletters(true);
                        $contactPreferences->save();

                        $user->setCreatingUser($this->getDI()->get('auth')->getAuthenticatedUser()->getUserId());
                        $user->setChildOf($this->getDI()->get('auth')->getAuthenticatedUser());
                        $user->setOrganisationId(
                            $this->getDI()->get('auth')->getAuthenticatedUser()->getOrganisationId()
                        );
                    }

                    if ($user === false || !$user->addRole('Client') || !$user->update()) {
                        $this->appendMessageEx($userEx);
                        $this->appendMessageEx($user);
                        break;
                    }

                    $portalUser = $user->getPortalUser();
                    $portalUser->setIsAnonymous(empty($row['firstname']));

                    if ($this->_generateRegistrationHash === true) {
                        $portalUser->setRegistrationHash(
                            (new Authentication())->generateRegistrationToken()
                        );

                        $organisation = $user->getUser()->getOrganisation();
                        $quotas = $organisation->getQuotas();
                        if ($quotas->getMemberTotal() > $quotas->getMemberUsed()) {
                            $quotas->consumeMemberQuota(1);
                            if (!$quotas->update()) {
                                $this->appendMessageEx($quotas);
                                break;
                            }
                        } else {
                            $this->appendMessageEx(_g('Not enough quota is available to process line {rowCount}', ['rowCount' => $rowCount]));
                            break;
                        }
                    }


                    if (!$portalUser->save()) {
                        $this->appendMessageEx($portalUser);
                        break;
                    }

                    $profile = $user->getUserProfile();

                    if (!empty($row['gender'])) {
                        $profile->setGender($row['gender']);
                    }

                    if (!$profile->save()) {
                        $this->appendMessageEx($profile);
                        break;
                    }

                    $users[] = $user->getUserId();
                }
            } else {
                $headers = false;
            }

            $rowCount++;
        }

        if ($this->hasMessages()) {
            $this->appendMessageEx(
                _g('Processing halted at line {line}. All imports have been reverted', ['line' => $rowCount])
            );
            $this->getDbAdapter()->getInternalHandler()->rollBack();
        } else {
            try {
                if (!$this->getDbAdapter()->getInternalHandler()->commit()) {
                    $this->appendMessageEx('Commit Failed ' . $this->getDbAdapter()->getInternalHandler()->errorInfo());
                }

                if ($this->_signupEmails == true && $this->_generateRegistrationHash == true) {
                    foreach ($users as $userId) {
                        $user = \User::resolve($userId);

                        if ($user->getUserProfile()->getEmail() != null) {
                            if (!$user->getPortalUser()->sendRegistrationEmail()) {
                                $this->_failedSignups[] = ['User' => $user->getUserId(), 'failReason' => _ms($user)];
                            }
                        } else {
                            $this->_failedSignups[] = [
                                'User' => $user->getUserId(),
                                'failReason' => _g('This user does not have an email address')
                            ];
                        }
                    }
                }
            } catch (Exception $ex) {
                $this->appendMessageEx($ex);
            }
        }

        return !$this->hasMessages();
    }
}
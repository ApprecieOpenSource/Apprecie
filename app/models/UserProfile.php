<?php

/**
 * The UserProfile entity, sits in the private portal tables and contains the personal details
 * of a given user.  not all users will have a userProfile
 *
 * To obtain the UserProfile execute the ->getProfile() method of any user entity.
 *
 * Class UserProfile
 */
class UserProfile extends \Apprecie\Library\Users\ApprecieUserBase
{
    protected $profileId;
    public $homeAddressId, $workAddressId, $deliveryAddressId, $firstname, $lastname, $title, $email, $phone, $mobile, $birthday, $gender, $occupationId;

    #region properties

    public function getFullName($title = false, $referenceIfNoName = true)
    {
        if($this->getFirstname() == '' && $referenceIfNoName) {
            return $this->getUserReference();
        }

        $name = $this->getFirstname() . ' ' . $this->getLastname();
        if ($this->getTitle() != '' && $title) {
            $name = $title . ' ' . $name;
        }

        return $name;
    }

    /**
     * @param mixed $birthday
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }

    /**
     * @return mixed
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param mixed $deliveryAddressId
     */
    public function setDeliveryAddressId($deliveryAddressId)
    {
        $this->deliveryAddressId = $deliveryAddressId;
    }

    /**
     * @return mixed
     */
    public function getDeliveryAddressId()
    {
        return $this->deliveryAddressId;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return mixed
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param mixed $homeAddressId
     */
    public function setHomeAddressId($homeAddressId)
    {
        $this->homeAddressId = $homeAddressId;
    }

    /**
     * @return mixed
     */
    public function getHomeAddressId()
    {
        return $this->homeAddressId;
    }

    /**
     * @param mixed $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param mixed $mobile
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     * @return mixed
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param mixed $occupationId
     */
    public function setOccupationId($occupationId)
    {
        $this->occupationId = $occupationId;
    }

    /**
     * @return mixed
     */
    public function getOccupationId()
    {
        return $this->occupationId;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return mixed
     */
    public function getProfileId()
    {
        return $this->profileId;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $workAddressId
     */
    public function setWorkAddressId($workAddressId)
    {
        $this->workAddressId = $workAddressId;
    }

    /**
     * @return mixed
     */
    public function getWorkAddressId()
    {
        return $this->workAddressId;
    }

    #endregion

    public function getSource()
    {
        return '_' . static::getSourcePrefix() . '_userprofiles';
    }

    public function validation()
    {
        if ($this->email != null) {
            $this->validate(
                new \Phalcon\Mvc\Model\Validator\Uniqueness(
                    array('field' => 'email')
                )
            );
        }

        return ($this->validationHasFailed() != true);
    }

    public function initialize()
    {
        $this->belongsTo('profileId', 'PortalUser', 'profileId');
        $this->hasOne('homeAddressId', 'Address', 'addressId', ['alias' => 'homeaddress']);
        $this->hasOne('workAddressId', 'Address', 'addressId', ['alias' => 'workaddress']);
        $this->hasOne('deliveryAddressId', 'Address', 'addressId', ['alias' => 'deliveryaddress']);
    }

    public function onConstruct()
    {
        parent::onConstruct();
        $this->setEncryptedFields(['firstname', 'lastname', 'phone', 'mobile']);
    }

    /**
     * @return Address
     */
    public function getHomeAddress($options = null)
    {
        return $this->getRelated('homeaddress', $options);
    }

    /**
     * @return Address
     */
    public function getWorkAddress($options = null)
    {
        return $this->getRelated('workaddress', $options);
    }

    /**
     * @return Address
     */
    public function getDeliveryAddress($options = null)
    {
        return $this->getRelated('deliveryaddress', $options);
    }

    public function getAge(){
        $dob =  new DateTime($this->getBirthday());
        $today =  new DateTime("now");
        $years = $today->diff($dob)->y;
        return $years;
    }
}
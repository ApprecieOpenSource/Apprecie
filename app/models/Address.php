<?php

use Phalcon\Db\RawValue;

class Address extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $addressId, $id, $domesticId, $language, $languageAlternatives, $department, $company, $subBuilding, $buildingNumber, $buildingName, $secondaryStreet, $street, $block, $neighbourhood, $district, $city, $line1, $line2, $line3, $line4, $line5, $adminAreaName, $adminAreaCode, $province, $provinceName, $provinceCode, $postalCode, $countryName, $countryIso2, $countryIso3, $countryIsoNumber, $sortingNumber1, $sortingNumber2, $barcode, $poBoxNumber, $label, $type, $dataLevel, $userProvided, $latitude, $longitude;

    /**
     * @param mixed $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param mixed $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param mixed $addressId
     */
    public function setAddressId($addressId)
    {
        $this->addressId = $addressId;
    }

    /**
     * @return mixed
     */
    public function getAddressId()
    {
        return $this->addressId;
    }

    /**
     * @param mixed $adminAreaCode
     */
    public function setAdminAreaCode($adminAreaCode)
    {
        $this->adminAreaCode = $adminAreaCode;
    }

    /**
     * @return mixed
     */
    public function getAdminAreaCode()
    {
        return $this->adminAreaCode;
    }

    /**
     * @param mixed $adminAreaName
     */
    public function setAdminAreaName($adminAreaName)
    {
        $this->adminAreaName = $adminAreaName;
    }

    /**
     * @return mixed
     */
    public function getAdminAreaName()
    {
        return $this->adminAreaName;
    }

    /**
     * @param mixed $barcode
     */
    public function setBarcode($barcode)
    {
        $this->barcode = $barcode;
    }

    /**
     * @return mixed
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * @param mixed $block
     */
    public function setBlock($block)
    {
        $this->block = $block;
    }

    /**
     * @return mixed
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * @param mixed $buildingName
     */
    public function setBuildingName($buildingName)
    {
        $this->buildingName = $buildingName;
    }

    /**
     * @return mixed
     */
    public function getBuildingName()
    {
        return $this->buildingName;
    }

    /**
     * @param mixed $buildingNumber
     */
    public function setBuildingNumber($buildingNumber)
    {
        $this->buildingNumber = $buildingNumber;
    }

    /**
     * @return mixed
     */
    public function getBuildingNumber()
    {
        return $this->buildingNumber;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param mixed $countryIso2
     */
    public function setCountryIso2($countryIso2)
    {
        $this->countryIso2 = $countryIso2;
    }

    /**
     * @return mixed
     */
    public function getCountryIso2()
    {
        return $this->countryIso2;
    }

    /**
     * @param mixed $countryIso3
     */
    public function setCountryIso3($countryIso3)
    {
        $this->countryIso3 = $countryIso3;
    }

    /**
     * @return mixed
     */
    public function getCountryIso3()
    {
        return $this->countryIso3;
    }

    /**
     * @param mixed $countryIsoNumber
     */
    public function setCountryIsoNumber($countryIsoNumber)
    {
        $this->countryIsoNumber = $countryIsoNumber;
    }

    /**
     * @return mixed
     */
    public function getCountryIsoNumber()
    {
        return $this->countryIsoNumber;
    }

    /**
     * @param mixed $countryName
     */
    public function setCountryName($countryName)
    {
        $this->countryName = $countryName;
    }

    /**
     * @return mixed
     */
    public function getCountryName()
    {
        return $this->countryName;
    }

    /**
     * @param mixed $dataLevel
     */
    public function setDataLevel($dataLevel)
    {
        $this->dataLevel = $dataLevel;
    }

    /**
     * @return mixed
     */
    public function getDataLevel()
    {
        return $this->dataLevel;
    }

    /**
     * @param mixed $department
     */
    public function setDepartment($department)
    {
        $this->department = $department;
    }

    /**
     * @return mixed
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * @param mixed $district
     */
    public function setDistrict($district)
    {
        $this->district = $district;
    }

    /**
     * @return mixed
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * @param mixed $domesticId
     */
    public function setDomesticId($domesticId)
    {
        $this->domesticId = $domesticId;
    }

    /**
     * @return mixed
     */
    public function getDomesticId()
    {
        return $this->domesticId;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param mixed $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return mixed
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param mixed $languageAlternatives
     */
    public function setLanguageAlternatives($languageAlternatives)
    {
        $this->languageAlternatives = $languageAlternatives;
    }

    /**
     * @return mixed
     */
    public function getLanguageAlternatives()
    {
        return $this->languageAlternatives;
    }

    /**
     * @param mixed $line1
     */
    public function setLine1($line1)
    {
        $this->line1 = $line1;
    }

    /**
     * @return mixed
     */
    public function getLine1()
    {
        return $this->line1;
    }

    /**
     * @param mixed $line2
     */
    public function setLine2($line2)
    {
        $this->line2 = $line2;
    }

    /**
     * @return mixed
     */
    public function getLine2()
    {
        return $this->line2;
    }

    /**
     * @param mixed $line3
     */
    public function setLine3($line3)
    {
        $this->line3 = $line3;
    }

    /**
     * @return mixed
     */
    public function getLine3()
    {
        return $this->line3;
    }

    /**
     * @param mixed $line4
     */
    public function setLine4($line4)
    {
        $this->line4 = $line4;
    }

    /**
     * @return mixed
     */
    public function getLine4()
    {
        return $this->line4;
    }

    /**
     * @param mixed $line5
     */
    public function setLine5($line5)
    {
        $this->line5 = $line5;
    }

    /**
     * @return mixed
     */
    public function getLine5()
    {
        return $this->line5;
    }

    /**
     * @param mixed $neighbourhood
     */
    public function setNeighbourhood($neighbourhood)
    {
        $this->neighbourhood = $neighbourhood;
    }

    /**
     * @return mixed
     */
    public function getNeighbourhood()
    {
        return $this->neighbourhood;
    }

    /**
     * @param mixed $poBoxNumber
     */
    public function setPoBoxNumber($poBoxNumber)
    {
        $this->poBoxNumber = $poBoxNumber;
    }

    /**
     * @return mixed
     */
    public function getPoBoxNumber()
    {
        return $this->poBoxNumber;
    }

    /**
     * @param mixed $postalCode
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
    }

    /**
     * @return mixed
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @param mixed $province
     */
    public function setProvince($province)
    {
        $this->province = $province;
    }

    /**
     * @return mixed
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * @param mixed $provinceCode
     */
    public function setProvinceCode($provinceCode)
    {
        $this->provinceCode = $provinceCode;
    }

    /**
     * @return mixed
     */
    public function getProvinceCode()
    {
        return $this->provinceCode;
    }

    /**
     * @param mixed $provinceName
     */
    public function setProvinceName($provinceName)
    {
        $this->provinceName = $provinceName;
    }

    /**
     * @return mixed
     */
    public function getProvinceName()
    {
        return $this->provinceName;
    }

    /**
     * @param mixed $secondaryStreet
     */
    public function setSecondaryStreet($secondaryStreet)
    {
        $this->secondaryStreet = $secondaryStreet;
    }

    /**
     * @return mixed
     */
    public function getSecondaryStreet()
    {
        return $this->secondaryStreet;
    }

    /**
     * @param mixed $sortingNumber1
     */
    public function setSortingNumber1($sortingNumber1)
    {
        $this->sortingNumber1 = $sortingNumber1;
    }

    /**
     * @return mixed
     */
    public function getSortingNumber1()
    {
        return $this->sortingNumber1;
    }

    /**
     * @param mixed $sortingNumber2
     */
    public function setSortingNumber2($sortingNumber2)
    {
        $this->sortingNumber2 = $sortingNumber2;
    }

    /**
     * @return mixed
     */
    public function getSortingNumber2()
    {
        return $this->sortingNumber2;
    }

    /**
     * @param mixed $street
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * @return mixed
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param mixed $subBuilding
     */
    public function setSubBuilding($subBuilding)
    {
        $this->subBuilding = $subBuilding;
    }

    /**
     * @return mixed
     */
    public function getSubBuilding()
    {
        return $this->subBuilding;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $userProvided
     */
    public function setUserProvided($userProvided)
    {
        $this->userProvided = $userProvided;
    }

    /**
     * @return mixed
     */
    public function getUserProvided()
    {
        return $this->userProvided;
    }


    public function getSource()
    {
        return 'address';
    }

    public function initialize()
    {
        $this->hasMany('addressId', 'Contact', 'addressId');
        $this->hasMany('addressId', 'UserProfile', 'homeAddressId', ['alias' => 'homeaddress', 'reusable' => true]);
        $this->hasMany('addressId', 'UserProfile', 'workAddressId', ['alias' => 'workaddress', 'reusable' => true]);
        $this->hasMany(
            'addressId',
            'UserProfile',
            'deliveryAddressId',
            ['alias' => 'deliveryaddress', 'reusable' => true]
        );
    }

    public function onConstruct()
    {
        static::setCachingMode(\Apprecie\library\Cache\CachingMode::Persistent);
    }

    public function beforeCreate()
    {
        if ($this->id == null) {
            $this->userProvided = new RawValue("1");
        }

        parent::beforeCreate();
    }

    public function create($data = null, $whiteList = null)
    {
        //reformat postcode anywhere fields
        $reformattedArray = array();
        if ($data != null) {
            foreach ($data as $k => $d) {
                if ($k == 'POBoxNumber') {
                    $k = 'poBoxNumber';
                }

                $reformattedArray[lcfirst($k)] = $d;
            }
        }

        return parent::create(count($reformattedArray) > 0 ? $reformattedArray : null, $whiteList);
    }

    public function getUserHome($options = null)
    {
        return $this->getRelated('homeaddress', $options);
    }

    public function getUserWork($options = null)
    {
        return $this->getRelated('workaddress', $options);
    }

    public function getUserDelivery($options = null)
    {
        return $this->getRelated('deliveryaddress', $options);
    }

    public function getContacts($options = null)
    {
        return $this->getRelated('Contact', $options);
    }
} 
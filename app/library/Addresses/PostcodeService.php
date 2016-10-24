<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 28/09/2015
 * Time: 13:17
 */

namespace Apprecie\Library\Addresses;

use Apprecie\Library\Messaging\PrivateMessageQueue;

class PostcodeService extends PrivateMessageQueue
{
    public function findAddressByPostcode($postcode)
    {
        $url = "https://services.postcodeanywhere.co.uk/PostcodeAnywhere/Interactive/Find/v1.10/json3.ws?";
        $url .= "&Key=" . urlencode($this->config->postcodeAnywhere->apiKey);
        $url .= "&SearchTerm=" . urlencode($postcode);
        return file_get_contents($url);
    }

    public function getAddressByAddressId($id)
    {
        $url = "https://services.postcodeanywhere.co.uk/CapturePlus/Interactive/Retrieve/v2.10/json3.ws?";
        $url .= "&Key=" . urlencode($this->config->postcodeAnywhere->apiKey);
        $url .= "&Id=" . urlencode($id);
        $data = file_get_contents($url);
        return $data;
    }

    public function getPostcodeGeocode($postcode, $countryCode)
    {
        $url = "https://services.postcodeanywhere.co.uk/Geocoding/International/Geocode/v1.10/json3.ws?";
        $url .= "&Key=" . urlencode($this->config->postcodeAnywhere->apiKey);
        $url .= "&Location=" . urlencode($postcode);
        $url .= "&Country=" . urlencode($countryCode);
        $data = file_get_contents($url);
        return $data;
    }
}
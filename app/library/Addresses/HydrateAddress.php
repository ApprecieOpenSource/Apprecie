<?php
/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 08/12/14
 * Time: 16:55
 */
namespace Apprecie\Library\Addresses;

use Address;
use Phalcon\DI;

class HydrateAddress extends DI
{
    public function addManualAddressFromPost()
    {
        $request = $this->getDefault()->get('request');

        $address = new Address();
        $address->setLine1($request->getPost('address1'));
        $address->setLine2($request->getPost('address2'));
        $address->setLine3($request->getPost('address3'));
        $address->setPostalCode($request->getPost('postcode'));
        $address->setCity($request->getPost('city'));
        $address->setProvince($request->getPost('province'));
        $address->setCountryIso3($request->getPost('country'));
        $address->setUserProvided(true);

        $label = $address->getLine1() . ' ' . $address->getLine2() . ' ' . $address->getLine3() . ' '
            . $address->getCity() . ' ' . $address->getPostalCode() . ' ' . $address->getProvince() . ' '
            . $address->getCountryIsoNumber();

        $address->setLabel(str_replace('  ', ' ', $label));

        $address->create();
        return $address->getAddressId();
    }

    public function addByRequestId($id)
    {
        $request = $this->getDefault()->get('request');
        if($request->getPost('addressType') == 'manual') {
            return $this->addManualAddressFromPost();
        }

        $address = new Address();
        $refAddress = $address->findBy('id', $id);
        if (count($refAddress) == 0) {
            $service = new PostcodeService();
            $data = $service->getAddressByAddressId($id);
            $addressData = json_decode($data);
            $addressData = (array)$addressData->Items[0];

            $newAddress = new Address();
            $newAddress->setUserProvided(false);
            $newAddress->create($addressData);

            $data = $service->getPostcodeGeocode($newAddress->getPostalCode(), $newAddress->getCountryIso2());
            $geo = json_decode($data);
            if (isset($geo->Items[0])) {
                $geo = (array)$geo->Items[0];
                $newAddress->setLatitude($geo['Latitude']);
                $newAddress->setLongitude($geo['Longitude']);
            }
            $newAddress->save();

            return $newAddress->getAddressId();
        } else {
            foreach ($refAddress as $address) {
                return $address->getAddressId();
            }
        }

        return false;
    }

    public static function getAddressByAddressIdAction($id)
    {
        $service = new PostcodeService();
        return $service->getAddressByAddressId($id);
    }

    public static function getPostcodeGeocode($postcode, $countryCode)
    {
        $service = new PostcodeService();
        return $service->getPostcodeGeocode($postcode, $countryCode);
    }
}


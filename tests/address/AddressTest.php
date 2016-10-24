<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 06/12/14
 * Time: 17:49
 */

class AddressTest extends UnitTestCase
{
    public function testAddress()
    {
        $address = new Address();
        $address->setLabel('test address');
        $address->setPostalCode('test');
        $this->assertTrue($address->create());
        _ep(_ms($address));

        $id = $address->getAddressId();

        $address->delete();
        $this->assertTrue(Address::findBy('addressId', $id)->count() == 0);
    }
} 
<?php
/**
 * Created by PhpStorm.
 * User: huwang
 * Date: 20/06/2015
 * Time: 21:00
 */

namespace Apprecie\library\Collections;


interface LocalisedEnum {
    public function getText();
    public function getTextByName($name);
}
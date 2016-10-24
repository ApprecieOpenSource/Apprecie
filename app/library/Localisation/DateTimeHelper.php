<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 08/05/15
 * Time: 12:17
 */

namespace Apprecie\Library\Localisation;

use Phalcon\DI;

class DateTimeHelper
{
    public static function getDateFromMySQLDateTimeString($dateTime, $specialCasesAsNull = false)
    {
        if ($dateTime == null || $dateTime == 'TBC') {
            return $specialCasesAsNull ? null : _g('TBC');
        }

        $date = new \DateTime($dateTime);

        if ($date->getTimestamp() >= DI::getDefault()->get('config')->environment->timestampmax) {
            return $specialCasesAsNull ? null : _g('No booking end date');
        }

        return $date->format('d-m-Y');
    }

    public static function getHumanDateTimeFromMySQLDateTimeString($dateTime, $specialCasesAsNull = false)
    {
        if ($dateTime == null || $dateTime == 'TBC') {
            return $specialCasesAsNull ? null : _g('TBC');
        }

        $date = new \DateTime($dateTime);

        if ($date->getTimestamp() >= DI::getDefault()->get('config')->environment->timestampmax) {
            return $specialCasesAsNull ? null : _g('No booking end date');
        }

        return _g(
            '{datepart} at {timepart} ({timezone})',
            [
                'datepart' => $date->format('l, d-M-Y'),
                'timepart' => $date->format('H:i'),
                'timezone' => $date->format('T')
            ]
        );
    }

    public static function getHumanDateFromMySQLDateTimeString($dateTime, $specialCasesAsNull = false)
    {
        if ($dateTime == null || $dateTime == 'TBC') {
            return $specialCasesAsNull ? null : _g('TBC');
        }

        $date = new \DateTime($dateTime);

        if ($date->getTimestamp() >= DI::getDefault()->get('config')->environment->timestampmax) {
            return $specialCasesAsNull ? null : _g('No booking end date');
        }

        return $date->format('l, d-M-Y');
    }

    public static function getTimeFromMySQLDateTimeString($dateTime, $specialCasesAsNull = false)
    {
        if ($dateTime == null || $dateTime == 'TBC') {
            return $specialCasesAsNull ? null : _g('TBC');
        }

        $date = new \DateTime($dateTime);

        if ($date->getTimestamp() >= DI::getDefault()->get('config')->environment->timestampmax) {
            return $specialCasesAsNull ? null : _g('No booking end date');
        }

        return $date->format('H:i');
    }

    public static function getDateTimeFromMySQLDateTimeString($dateTime, $specialCasesAsNull = false)
    {
        if ($dateTime == null || $dateTime == 'TBC') {
            return $specialCasesAsNull ? null : _g('TBC');
        }

        $date = new \DateTime($dateTime);

        if ($date->getTimestamp() >= DI::getDefault()->get('config')->environment->timestampmax) {
            return $specialCasesAsNull ? null : _g('No booking end date');
        }

        return $date->format('d-m-Y H:i');
    }

    public static function getMySQLDateFromDateString($dateString, $format = 'd-m-Y')
    {
        $date = \DateTime::createFromFormat($format, $dateString);

        return $date->format("Y-m-d H:i:s");
    }

    public static function getTimeStamp($dateString, $format = 'd-m-Y')
    {
        if ($dateString == null) {
            return 0;
        }

        $date = \DateTime::createFromFormat($format, $dateString);

        return $date->getTimestamp();
    }
} 
<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 27/04/15
 * Time: 09:37
 */

namespace Apprecie\Library\FileIO;

use Apprecie\Library\Messaging\PrivateMessageQueue;

class Folder extends PrivateMessageQueue
{
    function Copy($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->Copy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }

        closedir($dir);
    }
} 
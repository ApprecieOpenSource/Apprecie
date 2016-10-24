<?php
namespace External\Vendor\PdfCrowd;
class PdfCrowdException extends \Exception
{
    // custom string representation of object
    public function __toString()
    {
        if ($this->code) {
            return "[{$this->code}] {$this->message}\n";
        } else {
            return "{$this->message}\n";
        }
    }
}
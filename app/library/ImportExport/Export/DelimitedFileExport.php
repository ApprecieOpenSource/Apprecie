<?php
/**
 * Created by PhpStorm.
 * User: hu86
 * Date: 16/09/2015
 * Time: 15:30
 */

namespace Apprecie\Library\ImportExport\Export;


class DelimitedFileExport
{
    protected $_array;
    protected $_headings;
    protected $_file;
    protected $_delimited;
    protected $_delimiter;
    protected $_extension;
    protected $_encloseAll;
    protected $_bom;
    protected $_encoding;

    function __construct($array, $headings = null, $delimiter = ',', $extension = 'csv', $encloseAll = false, $bom = false, $encoding = 'UTF-8')
    {
        $this->_array = $array;
        $this->_headings = $headings;

        $this->setOptions($delimiter, $extension, $encloseAll, $bom, $encoding);
    }

    protected function setOptions($delimiter, $extension, $encloseAll, $bom, $encoding)
    {
        $this->_delimiter = $delimiter;
        $this->_extension = $extension;
        $this->_encloseAll = $encloseAll;
        $this->_bom = $bom;
        $this->_encoding = $encoding;
    }

    public function setKeysAsHeadings()
    {
        $this->_headings = array_keys($this->_array[0]);
    }

    protected function arrayToDelimited()
    {
        $this->_file = fopen('php://temp', 'w');

        if ($this->_headings != null) {
            array_unshift($this->_array, $this->_headings);
        }

        foreach ($this->_array as $row) {
            if ($this->_encloseAll) {
                $first = true;
                foreach ($row as $field) {
                    if (!$first) {
                        fwrite($this->_file, $this->_delimiter);
                    } else {
                        $first = false;
                    }
                    fwrite($this->_file, '"' . $field . '"');
                }
                fwrite($this->_file, "\r\n");
            } else {
                fputcsv($this->_file, $row, $this->_delimiter);
            }
        }

        fseek($this->_file, 0);

        $this->_delimited = fread($this->_file, fstat($this->_file)['size']);

        if ($this->_encoding === 'UTF-16LE') {
            $this->_delimited = iconv('UTF-8', 'UTF-16LE', $this->_delimited);
        }

        if ($this->_bom && $this->_encoding === 'UTF-8') {
            $this->_delimited = chr(0xEF) . chr(0xBB) . chr(0xBF) . $this->_delimited;
        } elseif ($this->_bom && $this->_encoding === 'UTF-16LE') {
            $this->_delimited = chr(255) . chr(254) . $this->_delimited;
        }

        fclose($this->_file);
    }

    public function download($filename)
    {
        if (!$this->_delimited) {
            $this->arrayToDelimited();
        }

        if ($this->_encoding === 'UTF-16LE') {
            header('Content-Type: text/csv; charset=utf-16;');
        } else {
            header('Content-Type: text/csv; charset=utf-8;');
        }
        header('Content-Disposition: attachment; filename="' . $filename . '.' . $this->_extension . '";');

        echo $this->_delimited;
    }

    public function getString()
    {
        if (!$this->_delimited) {
            $this->arrayToDelimited();
        }

        return $this->_delimited;
    }
}
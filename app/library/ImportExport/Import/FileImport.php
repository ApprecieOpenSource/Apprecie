<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 11/02/15
 * Time: 14:09
 */

namespace Apprecie\Library\ImportExport\Import;

use Apprecie\Library\ImportExport\ExcelFile;
use Apprecie\Library\ImportExport\Export\DelimitedFileExport;
use Phalcon\Exception;

abstract class FileImport extends Import
{
    protected $_rows = array();
    protected static $_fields = array(); /*  set fields in here */

    public function __construct($filePath)
    {
        if (!is_file($filePath)) {
            throw new Exception('I could not access the file');
        }

        parent::__construct($filePath);
    }

    public function getFields()
    {
        return static::$_fields;
    }

    public function prepareData()
    {
        $fileExtension = pathinfo($this->_importData)['extension'];

        if (in_array($fileExtension, array('xls', 'xlsx'))) {
            $this->_rows = ExcelFile::getArrayFromFile($this->_importData);
        } elseif (in_array($fileExtension, array('csv'))) {

            $lines = file($this->_importData);

            // strip BOM
            if (substr( $lines[0], 0, 3 ) == pack( "CCC", 0xef, 0xbb, 0xbf )) {
                $lines[0] = substr( $lines[0], 3 );
            }

            $headings = null;

            foreach ($lines as $row) {

                $row = mb_convert_encoding($row, 'UTF-8', 'UTF-8');

                if (!$headings) {
                    $headings = str_getcsv($row);
                    $this->_rows[] = $headings;
                } else {
                    $items = str_getcsv($row);
                    $processedItems = array();
                    foreach ($items as $key => $item) {
                        if (isset($headings[$key])) {
                            $processedItems[$headings[$key]] = $item;
                        } else {
                            $processedItems[] = $item;
                        }
                    }

                    $this->_rows[] = $processedItems;
                }
            }
        } else {
            $this->appendMessageEx(_g('We only accept CSV (.csv) and Excel (.xls, .xlsx) formats'));
            return false;
        }

        if (count($this->_rows) < 2) {
            $this->appendMessageEx(_g('The file contains no data - the first row should contain field names'));
            return false;
        }

        $headings = $this->_rows[0];
        if (count($headings) !== count(static::$_fields)) {
            $this->appendMessageEx(_g('The file does not contain the correct number of columns.  please use the template'));
            return false;
        }

        foreach (static::$_fields as $key => $field) {
            if ($field !== $headings[$key]) {
                $this->appendMessageEx(_g('The provided data is not in the correct format.  The first row should contain header fields in the order of the template'));
                return false;
            }
        }

        return true;
    }

    public static function getTemplate()
    {
        return join(',', static::$_fields);
    }

    public static function downloadTemplate($filename, $type)
    {
        if ($type === 'excel') {
            $excel = new ExcelFile();
            $excel->setActiveSheetCells(array(static::$_fields));
            $excel->download($filename);
        } else {
            $csv = new DelimitedFileExport(array(), static::$_fields, ',', 'csv', false, true);
            $csv->download($filename);
        }
    }
}
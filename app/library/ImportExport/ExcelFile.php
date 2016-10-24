<?php
/**
 * Created by PhpStorm.
 * User: hu86
 * Date: 25/01/2016
 * Time: 16:55
 */

namespace Apprecie\Library\ImportExport;

class ExcelFile extends \PHPExcel
{
    function __construct()
    {
        parent::__construct();

        $this->setActiveSheetIndex(0);
    }

    public static function getArrayFromFile($filename, $firstRowIsHeadings = true)
    {
        $reader = null;

        $filename_parts = pathinfo($filename);

        switch ($filename_parts['extension']) {
            case 'xls':
                $reader = new \PHPExcel_Reader_Excel5();
                break;
            case 'xlsx':
            default:
                $reader = new \PHPExcel_Reader_Excel2007();
                break;
        }

        $PHPExcel = $reader->load($filename);
        $PHPExcel->setActiveSheetIndex(0);
        $worksheet = $PHPExcel->getActiveSheet();

        $array = array();
        $headings = array();
        foreach ($worksheet->getRowIterator() as $row) {
            if (!$headings && $firstRowIsHeadings) {
                foreach ($row->getCellIterator() as $cell) {
                    $headings[] = $cell->getValue();
                }
                $array[] = $headings;
            } else {
                $rowArray = array();
                foreach ($row->getCellIterator() as $cell) {
                    $headingKey = \PHPExcel_Cell::columnIndexFromString($cell->getColumn())-1;
                    if ($firstRowIsHeadings && isset($headings[$headingKey])) {
                        $rowArray[$headings[$headingKey]] = $cell->getValue();
                    } else {
                        $rowArray[] = $cell->getValue();
                    }
                }
                $array[] = $rowArray;
            }
        }

        return $array;
    }

    public function setActiveSheetCells (array $data)
    {
        $worksheet = $this->getActiveSheet();

        foreach ($data as $rowNumber => $rowData) {
            $rowNumber++; //row number starts at 1
            foreach ($rowData as $columnNumber => $cellValue) {
                $worksheet->setCellValueByColumnAndRow($columnNumber, $rowNumber, $cellValue);
            }
        }
    }

    public function download($fileName = 'file')
    {
        $writer = new \PHPExcel_Writer_Excel2007($this);

        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=\"" . $fileName . ".xlsx\"");
        header("Cache-Control: max-age=0");

        ob_clean();
        $writer->save("php://output");
    }
}
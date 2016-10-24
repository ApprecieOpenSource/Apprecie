<?php
/**
 * Created by PhpStorm.
 * User: hu86
 * Date: 16/09/2015
 * Time: 15:38
 */

namespace Apprecie\Library\ImportExport\Export;


class ResultsExport extends DelimitedFileExport
{
    protected $_results;

    function __construct($results, $includeHeadings = true, $delimiter = ',', $extension = 'csv', $encloseAll = false, $bom = false, $encoding = 'UTF-8')
    {
        $this->_results = $results;
        $this->resultsToArray();

        if ($includeHeadings) {
            $this->setKeysAsHeadings();
        }

        $this->setOptions($delimiter, $extension, $encloseAll, $bom, $encoding);
    }

    protected function resultsToArray()
    {
        foreach ($this->_results as $record) {
            $recordArray = $record->toArray();
            $this->_array[] = $recordArray;
        }
    }
}
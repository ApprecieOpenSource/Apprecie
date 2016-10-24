<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 06/01/15
 * Time: 12:34
 */

namespace Apprecie\Library\Translation;

use Apprecie\Library\FileIO\FileSearchIterator;
use Apprecie\Library\SourceParsing\PHPFunctionCallParserAggregator;
use Phalcon\DI;
use Phalcon\DI\Injectable;

class AssetParser extends Injectable
{
    protected $_dbAdapter = null;

    /**
     * @return \Phalcon\Db\Adapter\Pdo\Mysql
     */
    public function getDbAdapter()
    {
        return $this->_dbAdapter;
    }

    public function __construct($dbAdapter = null)
    {
        if ($dbAdapter == null) {
            $dbAdapter = DI::getDefault()->get('db');
        }

        $this->_dbAdapter = $dbAdapter;
    }


    public function discover($purgePrevious = true)
    {
        $fileScanner = new PHPFunctionCallParserAggregator([
            '_g' // add additional calls here
        ]);

        $fileScanner->addIterator(new FileSearchIterator(APPLICATION_ROOT, true, ['php', 'inc', 'volt']));

        $parseResults = $fileScanner->parse(true);

        if ($purgePrevious) {
            $this->getDbAdapter()->execute('DELETE FROM assetscanresults');
        }

        foreach ($parseResults as $functionCall) {
            $args = $functionCall->getArguments();
            $assetID = null;

            switch ($functionCall->getFunctionName()) {
                case '_g' :
                {
                    $assetID = $args[0];
                    break;
                }

                //any other asset functions to be resolved here
            }

            if (is_string($assetID)) {
                $this->getDbAdapter()->execute(
                    'INSERT INTO assetscanresults VALUES (?, ?, ?, ?, ?)',
                    [
                        null,
                        $assetID,
                        $functionCall->getSourceFile(),
                        $functionCall->getLineNumber(),
                        $functionCall->getFunctionName()
                    ]
                );
            }
        }

        return $parseResults->count();
    }
} 
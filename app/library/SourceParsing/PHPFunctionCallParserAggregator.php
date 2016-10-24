<?php
namespace Apprecie\Library\SourceParsing;

use Apprecie\Library\Collections\Collection;

class PHPFunctionCallParserAggregator extends SourceParserAggregator
{
    private $_functionNames;
    private $_parseFails = array();

    /**
     * @return array
     */
    public function getParseFails()
    {
        return $this->_parseFails;
    }

    /**
     * Creates a new parser aggregator, pass in an array of the functions to parse for, such as ['_g',...]
     *
     * Use addIterator() to add FileSearchIterator 's
     * call parse and the combined results be stored in the internal collection.
     * @param $functionNames
     * @throws \InvalidArgumentException
     */
    public function __construct($functionNames)
    {
        if ($functionNames == null) {
            throw new \InvalidArgumentException('functionNames must be set');
        }
        $this->_functionNames = $functionNames;
        parent::__construct();
    }


    /**
     * @param bool $verbose
     * @return Collection
     */
    public function parse($verbose = false)
    {
        $functionCalls = new Collection('Apprecie\Library\SourceParsing\FunctionCallMeta');

        foreach ($this as $file) {
            if ($verbose) {
                _ep('looking at ' . $file);
            }

            try {
                $parsedCalls = new PHPFunctionCallParser($file, $this->_functionNames);
                $parsedCalls->parse();
                $functionCalls->AppendArray($parsedCalls->getArray());

                if ($verbose) {
                    _ep('found ' . count($parsedCalls) . ' function calls');
                }
            } catch (\Exception $ex) {
                $this->_parseFails[] = $file;
                if ($verbose) {
                    _ep('Parse failed ' . _ms($ex));
                }
            }
        }

        return $functionCalls;
    }
}
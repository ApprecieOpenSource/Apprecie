<?php
namespace Apprecie\Library\SourceParsing;

/**
 * Describes a function call in terms of its name, originating file, and arguments.
 */
class FunctionCallMeta
{
    private $_arguments;
    private $_functionName;
    private $_lineNumber;
    private $_sourceFile;

    /**
     * @return array
     */
    public function getArguments()
    {
        return $this->_arguments;
    }

    /**
     * @return mixed
     */
    public function getFunctionName()
    {
        return $this->_functionName;
    }

    /**
     * @return mixed
     */
    public function getLineNumber()
    {
        return $this->_lineNumber;
    }

    /**
     * @return mixed
     */
    public function getSourceFile()
    {
        return $this->_sourceFile;
    }

    /**
     * Creates a simple structure to store the details of a function call.
     *
     * @param $functionName
     * @param array $arguments
     * @param null $lineNumber
     * @param null $sourceFile
     * @throws \InvalidArgumentException
     */
    public function __construct($functionName, $arguments = array(), $lineNumber = null, $sourceFile = null)
    {
        if ($functionName == null) {
            throw new \InvalidArgumentException('functionName must have a value');
        }

        $this->_arguments = $arguments;
        $this->_functionName = $functionName;
        $this->_lineNumber = $lineNumber;
        $this->_sourceFile = $sourceFile;
    }
}
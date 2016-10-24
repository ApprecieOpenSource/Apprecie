<?php
namespace Apprecie\Library\SourceParsing;

/**
 * Provides Parsing for a php file using the token_get_all() method
 */
abstract class SourceFileParser extends SourceParser
{
    protected $_filePath;

    /**
     * @return string The path to file that is the subject of parsing
     */
    public function getFilePath()
    {
        return $this->_filePath;
    }

    /**
     * Builds a token list from a php file ($filePath)
     *
     * @param $filePath
     * @param string $memberType
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function __construct($filePath, $memberType = 'stdClass')
    {
        try {
            if ($filePath == null) {
                throw new \InvalidArgumentException("filePath is required");
            }
            if (($content = file_get_contents($filePath)) == false) {
                throw new \InvalidArgumentException("The argument filepath [$filePath] could not be read as a file");
            }

            $this->_filePath = $filePath;

            parent::__construct($content, $memberType);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
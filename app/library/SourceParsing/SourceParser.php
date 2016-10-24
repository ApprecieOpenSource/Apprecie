<?php
namespace Apprecie\Library\SourceParsing;

use Apprecie\Library\Collections\Collection;

/**
 * Allows a string to be parsed using an abstract parse function, the accepted results are
 * provided as an itterable collection.
 */
abstract class SourceParser extends Collection
{
    protected $_content;
    protected $_tokens;

    /**
     * @param $content
     * @param string $memberType
     * @throws \Exception
     */
    public function __construct($content, $memberType = 'stdClass')
    {
        $this->_content = $content;

        parent::__construct($memberType);

        try {
            $this->tokenise();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /*
     * Provides the content for $_tokens.
     *
     * The default functionality is to call token_get_all on $this->_content.
     * Override for different functionality.
     */
    protected function tokenise()
    {
        $this->clear();
        $this->_tokens = token_get_all($this->_content);
    }

    /**
     * Call to populate the internal Collection with accepted results
     */
    abstract function parse();
}
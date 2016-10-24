<?php
namespace Apprecie\Library\SourceParsing;

use Apprecie\Library\Collections\Collection;
use Apprecie\Library\FileIO\FileSearchIterator;

/**
 * Class SourceParserFixture
 */
abstract class SourceParserAggregator extends Collection
{
    public function __construct()
    {
        parent::__construct('string');
    }

    public function addIterator(FileSearchIterator $iterator)
    {
        foreach ($iterator as $file) {
            $this->add($file->getPathname());
        }
    }

    abstract public function parse();
}
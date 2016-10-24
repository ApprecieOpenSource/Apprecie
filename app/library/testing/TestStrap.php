<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 24/11/14
 * Time: 09:30
 */

namespace Apprecie\Library\Testing;

use Apprecie\Library\Collections\Collection;
use Phalcon\DI;

require_once(DI::getDefault()->get('config')->application->testLib . '/unit_tester.php');
require_once(DI::getDefault()->get('config')->application->testLib . '/reporter.php');

/**
 * Provides a collection of unit tests apparant within the new cms section of the aplication.
 */
class TestStrap extends Collection
{
    protected $_locations;

    /**
     * The constructor requires an array of filesystem locations in which to look for tests.
     *
     * To be clear all files in any of these locations that end in Test will be treated as such, so we now must be careful to name
     * our files correctly.  A Test file must inherit from UnitTestCase.
     *
     * The TestStrap will not automatically enumerate sub folders unless a * is passed after the path.
     *
     * c:\somefolder\     TestStrap will enumerate the entire folder looking for files that end in Test
     * c:\somefolder\*   Teststrap will enumerate all child folders of somefilder looking for tests files.
     *
     * Each folder will be reported as a seperate tests category and each file as a seperate tests whithin than category.
     *
     * Test files should end in the .inc file extension and should contain only a single class inheriting from UnitTestCase.
     *
     * @param array|Collection $locations A collection of the locations to be enumerated for tests
     * @throws \InvalidArgumentException
     * @return TestStrap
     */
    function __construct($locations)
    {
        parent::__construct('TestSuite');

        if (!is_array($locations)) {
            $locations = array($locations);
        }

        //fix any missing  / from folders
        foreach ($locations as &$location) {
            if ((substr_compare($location, '\\', -1, 1) <> 0) && (substr_compare(
                        $location,
                        '*',
                        -1,
                        1
                    ) <> 0)
            ) {
                $location .= '\\';
            }
            $location = str_replace('//', '\\', $location);
            $location = str_replace('/', '\\', $location);
            $location = str_replace('\\\\', '\\', $location);
        }

        $this->_locations = $locations;
        $this->StrapTests();
    }

    /**
     * @param int $index
     * @return \TestSuite
     */
    public function get($index)
    {
        return parent::get($index);
    }

    /**
     * Cycles through the locations provided in the constructor and builds a TestGroup for each storing in the
     * member _testGroups collection.
     * @throws \DomainException
     */
    protected function StrapTests()
    {
        //build a set of GroupTest objects
        foreach ($this->_locations as $location) {
            $isWildCard = strpos($location, '*', strlen($location) - 1) != false ? true : false; //if path ends in *
            if ($isWildCard) {
                $location = substr($location, 0, strlen($location) - 1);
            } //remove the * to make path valid

            if (!is_dir(
                $location
            )
            ) {
                throw new \DomainException("$location : is not a valid folder accessible to this process");
            }

            $groupName = basename($location);

            if (!$isWildCard) { //this folder is the group
                $gt = new \TestSuite($groupName);

                foreach (new TestFileIterator($location) as $file) {
                    $gt->addFile($file->getRealPath());
                }
                $this->add($gt);
            } else { //each sub folder in the current folder will be a group.
                foreach (new \DirectoryIterator($location) as $info) { //we need to scan each sub directory
                    if ($info->isDir() && !$info->isDot()) {
                        $subGroupName = basename($info->getRealPath());
                        if ($subGroupName[0] !== '.') //avoid hidden folders.
                        {
                            $gt = new \TestSuite($groupName . ':' . $subGroupName);

                            foreach (new TestFileIterator($info->getRealPath()) as $file) {
                                $gt->addFile($file->getRealPath());
                            }

                            $this->add($gt);
                        }
                    }
                }
            }
        }
    }
}
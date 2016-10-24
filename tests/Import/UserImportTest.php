<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 11/02/15
 * Time: 16:13
 */

class UserImportTwoLoginTest extends  \Apprecie\Library\Testing\ApprecieTwoLoginTestBase
{
    public function testCSVImoport()
    {
        _ep(\Apprecie\Library\ImportExport\Import\UserImport::getTemplate());

        $csv = new \Apprecie\Library\ImportExport\Import\UserImport(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'baduser.csv');
        $this->assertFalse($csv->validateImport());
        _epm($csv);

        $csv = new \Apprecie\Library\ImportExport\Import\UserImport(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'badshapeuser.csv');
        $this->assertFalse($csv->validateImport());
        _epm($csv);

        $csv = new \Apprecie\Library\ImportExport\Import\UserImport(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'gooduser.csv');
        $this->assertTrue($csv->validateImport());
        _epm($csv);

        $this->assertTrue($csv->commitImport());
        _epm($csv);

    }
} 
<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    public function setUp()
    {
        parent::setUp();
        
        Artisan::call('migrate');
    }

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    public function assertDatabaseEquals(array $expectedTables, array $tables = array())
    {
        if(empty($tables))
            $tables = $this->getTables();

        foreach ($tables as $tbl) 
        {
            $this->assertDatabaseTableEquals($expectedTables[$tbl], $tbl);
        }
    }

    public function assertDatabaseTableEquals(array $expectedTable, $tableName, $excluded = array())
    {
        //$columns = array_keys(array_except((array)$expectedTable[0], $excluded));

        $cnt = DB::table($tableName)->count();

        $this->assertEquals(count($expectedTable), $cnt);

        foreach ($expectedTable as $key => $expected) 
        {
            $this->seeInDatabase($tableName, array_except((array) $expected, $excluded));
            //$this->assertEquals(array_except((array) $expected, $excluded), (array) $result[$key]);
        }
    }

    public function getDatabaseContent()
    {
        $dbContent = array();

        $tables = $this->getTables();
        
        foreach ($tables as $tbl) 
        {
            $dbContent[$tbl] = DB::table($tbl)->get();
        }

        return $dbContent;
    }

    public function getTables()
    {
        //sqlite
        $tables = DB::getPdo()->query("select distinct tbl_name from sqlite_master where type = 'table'")->fetchAll();
        $tables = array_pluck($tables, 'tbl_name');

        //mysql
        //$tables = DB::getPdo()->query("show tables")->fetchAll();
        
        return $tables;
    }

    public function jsonFileToArray($file)
    {
        $string = file_get_contents($file);
        
        return json_decode($string, true);
    }
}

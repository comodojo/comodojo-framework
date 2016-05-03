<?php namespace Comodojo\Test;

use \Comodojo\Components\ConfigurationTrait;
use \Comodojo\Components\DatabaseTrait;
use \PHPUnit_Framework_TestCase;

class Base extends PHPUnit_Framework_TestCase {

    use ConfigurationTrait;
    use DatabaseTrait;

    protected $std_test_config = array(
        'database-model' => 'MYSQLI',
        'database-host' => 'localhost',
        'database-port' => '3306',
        'database-name' => 'comodojo',
        'database-user' => 'root',
        'database-password' => 'comodojo',
        'database-prefix' => 'cmdj_'
    );

    public function setUp() {

        $this->initConfiguration($this->std_test_config);
        $this->initDatabase($this->configuration);

    }

}

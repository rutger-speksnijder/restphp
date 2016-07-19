<?php
namespace Test;

use \RestPHP\Configuration;

class ConfigurationTest extends \PHPUnit_Framework_TestCase {

    public function testCanLoadFromFile() {
        $c = new Configuration();
        $c->loadFromFile('../src/RestPHP/config.php');
    }

    public function testCanNotLoadFromInvalidFile() {
        $this->setExpectedException('Exception');
        $c = new Configuration();
        $c->loadFromFile('non_existing_configuration_file.php');
    }
}

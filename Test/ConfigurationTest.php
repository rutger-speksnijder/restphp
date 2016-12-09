<?php
namespace Test;

use \RestPHP\Configuration;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testCanCreateFromFile()
    {
        $c = new Configuration();
        $c->createFromFile(dirname(__DIR__) . '/src/RestPHP/config.php');
    }

    public function testCanNotCreateFromInvalidFile()
    {
        $this->setExpectedException('Exception');
        $c = new Configuration();
        $c->createFromFile('non_existing_configuration_file.php');
    }
}

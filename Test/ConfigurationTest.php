<?php
namespace Test;

use \RestPHP\Configuration;

if (version_compare(PHP_VERSION, '7.0', '>=')) {
    class ConfigurationTest extends \PHPUnit\Framework\TestCase
    {
        public function testCanCreateFromFile()
        {
            $c = new Configuration();
            $c->createFromFile(dirname(__DIR__) . '/src/RestPHP/config.php');
        }

        public function testCanNotCreateFromInvalidFile()
        {
            $this->expectException(get_class(new \Exception));
            $c = new Configuration();
            $c->createFromFile('non_existing_configuration_file.php');
        }
    }
} else {
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
}

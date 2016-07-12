<?php
namespace Test;

use \RestPHP\Router;
use \RestPHP\BaseAPI;

class BaseAPITest extends \PHPUnit_Framework_TestCase {
	public function testCanNotCreateTokenServerWithInvalidArguments() {
		$this->setExpectedException('Exception');
		$server = BaseAPI::createTokenServer(false, null, null, null);
	}
}

<?php
namespace Test;

use \RestPHP\Router;

class RouterTest extends \PHPUnit_Framework_TestCase {

	public function testCanNotExecuteWhenNoEmptyRouteIsSet() {
		$this->setExpectedException('Exception');
		$r = new Router;
		$r->execute();
	}

	public function testCanExecute() {
		$r = new Router;
		$r->add('', function() {});
		$r->add('/test', function() {
			return 'test';
		});

		$r->setUrl('/test');

		$expectedResult = 'test';
		$result = $r->execute();

		$this->assertEquals($expectedResult, $result);
	}
}

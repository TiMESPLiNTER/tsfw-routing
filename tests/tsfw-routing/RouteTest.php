<?php

namespace timesplinter\tsfw\routing\tests;
use timesplinter\tsfw\routing\Route;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2014, TiMESPLiNTER Webdevelopment
 */
class RouteTest extends \PHPUnit_Framework_TestCase 
{
	public function testGetParamGeneric()
	{
		$route = new Route(
			'test/route',
			array(
				'*' => 'ch.timesplinter.site-Acontroller.aMethod'
			),
			array(
				0 => 256
			)
		);
		
		$this->assertEquals(256, $route->getParam(0), 'Get existing parameter');
		$this->assertEquals(null, $route->getParam(1), 'Get not existing parameter');
		$this->assertEquals(array(0 => 256), $route->getParams(), 'Get all parameters');
	}

	public function testGetParamAssoc()
	{
		$route = new Route(
			'test/route',
			array(
				'*' => 'ch.timesplinter.site-Acontroller.aMethod'
			),
			array(
				'item_id' => 256
			)
		);

		$this->assertEquals(256, $route->getParam('item_id'), 'Get existing parameter');
		$this->assertEquals(null, $route->getParam('version'), 'Get not existing parameter');
		$this->assertEquals(array('item_id' => 256), $route->getParams(), 'Get all parameters');
	}
}

/* EOF */ 
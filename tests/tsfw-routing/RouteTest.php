<?php

namespace timesplinter\tsfw\routing\tests;
use timesplinter\tsfw\routing\Route;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2014, TiMESPLiNTER Webdevelopment
 */
class RouteTest extends \PHPUnit_Framework_TestCase 
{
	public function testGeneralGetters()
	{
		$route = new Route(
			'route-name',
			'test/route',
			array(
				'*' => 'ch.timesplinter.site-Acontroller.aMethod'
			),
			array(
				'item_id' => 256
			)
		);
		
		$this->assertEquals('route-name', $route->getName(), 'Get route name');
		$this->assertEquals('test/route', $route->getPattern(), 'Get pattern');
	}

	public function testGetParamAssoc()
	{
		$route = new Route(
			null,
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
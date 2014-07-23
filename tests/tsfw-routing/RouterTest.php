<?php

namespace timesplinter\tsfw\routing\tests;

use timesplinter\tsfw\routing\Route;
use timesplinter\tsfw\routing\Router;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2014, TiMESPLiNTER Webdevelopment
 */
class RouterTest extends \PHPUnit_Framework_TestCase {
	/** @var  Router */
	protected $router;

	protected function setUp()
	{
		$this->router = new Router();
		$this->router->setRoutes(array(
			'test-basic-route' => array(
				'pattern' => 'basic/route',
				'mapping' => array(
					'GET' => 'ch.timesplinter.site.AController.aMethod'
				)
			),
			
			'test-generic-params' => array(
				'pattern' => 'params/generic/(\d+)/view',
				'mapping' => array(
					'*' => 'ch.timesplinter.site.AController.aMethod'
				)
			),
			
			'test-assoc-params' => array(
				'pattern' => 'params/assoc/{%item_id%}/version/{%version%}',
				'params' => array(
					'item_id' => 'numeric',
					'version' => '\d+\.\d+'
				),
				'mapping' => array(
					'*' => 'ch.timesplinter.site.AController.aMethod'
				)
			),
			
			'test-string-param' => array(
				'pattern' => 'params/string/{%item_id%}',
				'params' => array(
					'item_id' => 'alphabetic'
				),
				'mapping' => array(
					'*' => 'ch.timesplinter.site.AController.aMethod'
				)
			),
			
			'test-assoc-unused-param' => array(
				'pattern' => 'params/assoc/{%item_id%}/version/{%version%}',
				'params' => array(
					'item_id' => 'int'
				),
				'mapping' => array(
					'*' => 'ch.timesplinter.site.AController.aMethod'
				)
			),
			
			'test-multiple-1' => array(
				'pattern' => 'multiple/route',
				'mapping' => array(
					'GET' => 'ch.timesplinter.site.AController.getMethod'
				)
			),
			
			'test-multiple-2' => array(
				'pattern' => 'multiple/route',
				'mapping' => array(
					'POST' => 'ch.timesplinter.site.AController.postMethod'
				)
			),

			'test-multiple-3' => array(
				'pattern' => 'multiple/route',
				'mapping' => array(
					'*' => 'ch.timesplinter.site.AController.postMethod',
					'DELETE' => 'ch.timesplinter.site.AController.postMethod'
				)
			)
		));
	}
	
	public function testBasicRoute()
	{
		$expectedRoutes = array(
			'test-basic-route' => new Route(
				'basic/route',
				array(
					'GET' => 'ch.timesplinter.site.AController.aMethod'
				),
				array()
			)
		);

		$actualRoutes = $this->router->fromURI('basic/route');
		
		$this->assertEquals($expectedRoutes, $actualRoutes, 'Basic route matching');
	}

	public function testNoneExistingRoute()
	{
		$expectedRoutes = array();

		$actualRoutes = $this->router->fromURI('none/existing/route');
		
		$this->assertEquals($expectedRoutes, $actualRoutes, 'None existing route');
	}

	public function testNumericParamsRoute()
	{
		$expectedRoutes = array(
			'test-generic-params' => new Route(
				'params/generic/(\d+)/view',
				array(
					'*' => 'ch.timesplinter.site.AController.aMethod'
				),
				array(
					0 => 256
				)
			)
		);
		
		$actualRoutes = $this->router->fromURI('params/generic/256/view');
		
		$this->assertEquals($expectedRoutes, $actualRoutes, 'Generic parameters test');
	}
	
	public function testAssociativeParamsRoute()
	{
		$expectedRoutes = array(
			'test-assoc-params' => new Route(
				'params/assoc/(\d+(?:\.\d+)?)/version/(\d+\.\d+)',
				array(
					'*' => 'ch.timesplinter.site.AController.aMethod'
				),
				array(
					'item_id' => 256,
					'version' => 2.3
				)
			)
		);
		
		$actualRoutes = $this->router->fromURI('params/assoc/256/version/2.3');

		$this->assertEquals($expectedRoutes, $actualRoutes, 'Associative parameters test');
	}
	
	public function testAssocUnusedParam()
	{
		$expectedRoutes = array(
			'test-assoc-unused-param' => new Route(
				'params/assoc/(\d+)/version/{%version%}',
				array(
					'*' => 'ch.timesplinter.site.AController.aMethod'
				),
				array(
					'item_id' => 256
				)
			)
		);

		$actualRoutes = $this->router->fromURI('params/assoc/256/version/{%version%}');

		$this->assertEquals($expectedRoutes, $actualRoutes, 'Associative not used parameter test');
	}
	
	public function testStringParamRoute()
	{
		$expectedRoutes = array(
			'test-string-param' => new Route(
					'params/string/([A-Za-z]+)',
					array(
						'*' => 'ch.timesplinter.site.AController.aMethod'
					),
					array(
						'item_id' => 'foobar'
					)
				)
		);

		$actualRoutes = $this->router->fromURI('params/string/foobar');

		$this->assertEquals($expectedRoutes, $actualRoutes, 'String parameter test');
	}
	
	public function testMultipleMatchingRoutes()
	{
		$expectedRoutes = array(
			'test-multiple-1' => new Route(
				'multiple/route',
				array(
					'GET' => 'ch.timesplinter.site.AController.getMethod'
				),
				array()
			),

			'test-multiple-2' => new Route(
				'multiple/route',
				array(
					'POST' => 'ch.timesplinter.site.AController.postMethod'
				),
				array()
			),
			
			'test-multiple-3' => new Route(
				'multiple/route',
				array(
					'*' => 'ch.timesplinter.site.AController.postMethod',
					'DELETE' => 'ch.timesplinter.site.AController.postMethod'
				),
				array()
			)
		);
		
		$actualRoutes = $this->router->fromURI('multiple/route');
		
		$this->assertEquals($expectedRoutes, $actualRoutes, 'Multiple routes');
	}
	
	public function testFilteredRoutes()
	{
		$expectedRoutes = array(
			'test-multiple-1' => new Route(
				'multiple/route',
				array(
					'GET' => 'ch.timesplinter.site.AController.getMethod'
				),
				array()
			),

			'test-multiple-3' => new Route(
				'multiple/route',
				array(
					'*' => 'ch.timesplinter.site.AController.postMethod',
					'DELETE' => 'ch.timesplinter.site.AController.postMethod'
				),
				array()
			)
		);

		$actualRoutes = $this->router->fromURI('multiple/route', array(
			Route::HTTP_METHOD_GET
		));

		$this->assertEquals($expectedRoutes, $actualRoutes, 'Filtered routes for GET');
	}
}

/* EOF */ 
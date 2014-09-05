<?php

namespace timesplinter\tsfw\routing\tests;

use timesplinter\tsfw\routing\dispatcher\CharCountDispatcher;
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
		$dispatcher = new CharCountDispatcher();
		$dispatcher->registerRegexTranslation('hex', '[A-Fa-f0-9]{2}');
		
		$this->router = new Router($dispatcher);
		$this->router->setRoutes(array(
			 array(
				'name' => 'test-basic-route',
				'pattern' => 'basic/route',
				'mapping' => array(
					'GET' => 'ch.timesplinter.site.AController.aMethod'
				)
			),
			
			array(
				'name' => 'test-generic-params',
				'pattern' => 'params/generic/{item_id:\d+}/view',
				'mapping' => array(
					'*' => 'ch.timesplinter.site.AController.aMethod'
				)
			),
			
			array(
				'name' => 'test-assoc-params',
				'pattern' => 'params/assoc/{item_id:numeric}/version/{version:\d+\.\d+}',
				'mapping' => array(
					'*' => 'ch.timesplinter.site.AController.aMethod'
				)
			),

			array(
				'name' => 'test-assoc-unused-param',
				'pattern' => 'params/assoc/{item_id:\d+}/version/{version:.+}',
				'mapping' => array(
					'*' => 'ch.timesplinter.site.AController.aMethod'
				)
			),
			
			array(
				'name' => 'test-string-param',
				'pattern' => 'params/string/{item_id:alphabetic}',
				'mapping' => array(
					'*' => 'ch.timesplinter.site.AController.aMethod'
				)
			),
						
			array(
				'name' => 'test-multiple',
				'pattern' => 'multiple/route',
				'mapping' => array(
					'*' => 'ch.timesplinter.site.AController.postMethod',
					'DELETE' => 'ch.timesplinter.site.AController.postMethod',
					'POST' => 'ch.timesplinter.site.AController.postMethod',
					'GET' => 'ch.timesplinter.site.AController.getMethod'
				)
			),

			array(
				'name' => 'test-regex-translation',
				'pattern' => 'additional/regex/{hex_code:hex}',
				'mapping' => array(
					'*' => 'ch.timesplinter.site.AController.postMethod'
				)
			)
		));
	}
	
	public function testBasicRoute()
	{
		$expectedRoute = new Route(
			'test-basic-route',
			'basic/route',
			array(
				'GET' => 'ch.timesplinter.site.AController.aMethod'
			),
			array()
		);

		$actualRoute = $this->router->fromURI('basic/route');
		$this->assertEquals($expectedRoute, $actualRoute, 'Basic route matching');
	}

	public function testNoneExistingRoute()
	{
		$actualRoute = $this->router->fromURI('none/existing/route');
		
		$this->assertEquals(404, $actualRoute, 'None existing route');
	}
	
	public function testMethodNotAllowed() {
		$methodNotAllowedRoute = $this->router->fromURI('basic/route', array(Route::HTTP_METHOD_POST));
		
		$this->assertEquals(405, $methodNotAllowedRoute, 'Basic route match but method not allowed');
	}

	public function testNumericParamsRoute()
	{
		$expectedRoute = new Route(
			'test-generic-params',
			'params/generic/{item_id:\d+}/view',
			array(
				'*' => 'ch.timesplinter.site.AController.aMethod'
			),
			array(
				'item_id' => 256
			)
		);
		
		$actualRoute = $this->router->fromURI('params/generic/256/view');
		
		$this->assertEquals($expectedRoute, $actualRoute, 'Generic parameters test');
	}
	
	public function testAssociativeParamsRoute()
	{
		$expectedRoute = new Route(
			'test-assoc-params',
			'params/assoc/{item_id:numeric}/version/{version:\d+\.\d+}',
			array(
				'*' => 'ch.timesplinter.site.AController.aMethod'
			),
			array(
				'item_id' => 256,
				'version' => 2.3
			)
		);
		
		$actualRoutes = $this->router->fromURI('params/assoc/256/version/2.3');
		
		$this->assertEquals($expectedRoute, $actualRoutes, 'Associative parameters test');
	}
	
	public function testAssocUnusedParam()
	{
		$expectedRoute = new Route(
			'test-assoc-unused-param',
			'params/assoc/{item_id:\d+}/version/{version:.+}',
			array(
				'*' => 'ch.timesplinter.site.AController.aMethod'
			),
			array(
				'item_id' => 256,
				'version' => 'version_string'
			)
		);

		$actualRoutes = $this->router->fromURI('params/assoc/256/version/version_string');
		
		$this->assertEquals($expectedRoute, $actualRoutes, 'Associative not used parameter test');
	}
	
	public function testStringParamRoute()
	{
		$expectedRoutes = new Route(
			'test-string-param',
			'params/string/{item_id:alphabetic}',
			array(
				'*' => 'ch.timesplinter.site.AController.aMethod'
			),
			array(
				'item_id' => 'foobar'
			)
		);

		$actualRoutes = $this->router->fromURI('params/string/foobar');

		$this->assertEquals($expectedRoutes, $actualRoutes, 'String parameter test');
	}
	
	public function testMultipleMatchingRoutes()
	{
		$expectedRoutes = new Route(
			'test-multiple',
			'multiple/route',
			array(
				'POST' => 'ch.timesplinter.site.AController.postMethod',
				'*' => 'ch.timesplinter.site.AController.postMethod',
				'DELETE' => 'ch.timesplinter.site.AController.postMethod',
				'GET' => 'ch.timesplinter.site.AController.getMethod'
			),
			array()
		);
		
		$actualRoutes = $this->router->fromURI('multiple/route');
		
		$this->assertEquals($expectedRoutes, $actualRoutes, 'Multiple routes');
	}
	
	public function testFilteredRoutes()
	{
		$expectedRoutes = new Route(
			'test-multiple',
			'multiple/route',
			array(
				'*' => 'ch.timesplinter.site.AController.postMethod',
				'POST' => 'ch.timesplinter.site.AController.postMethod',
				'DELETE' => 'ch.timesplinter.site.AController.postMethod',
				'GET' => 'ch.timesplinter.site.AController.getMethod'
			),
			array()
		);

		$actualRoutes = $this->router->fromURI('multiple/route', array(
			Route::HTTP_METHOD_GET
		));

		$this->assertEquals($expectedRoutes, $actualRoutes, 'Filtered routes for GET');
	}
	
	public function testAdditionalRegexTranslation() {
		$expectedRoute = new Route(
			'test-regex-translation',
			'additional/regex/{hex_code:hex}',
			array(
				'*' => 'ch.timesplinter.site.AController.postMethod'
			),
			array(
				'hex_code' => '2C'
			)
		);

		$actualRoute = $this->router->fromURI('additional/regex/2C');
		$this->assertEquals($expectedRoute, $actualRoute, 'Check new regex translation');

		$actualRoute = $this->router->fromURI('additional/regex/XY');
		$this->assertEquals(404, $actualRoute, 'Let regex translation fail');
	}
}

/* EOF */ 
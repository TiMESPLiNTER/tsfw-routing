<?php

namespace timesplinter\tsfw\routing;

use timesplinter\tsfw\routing\dispatcher\Dispatcher;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2014, TiMESPLiNTER Webdevelopment
 */
class Router implements RouterInterface {
	protected $routes;
	protected $dispatcher;
	
	public function __construct(Dispatcher $dispatcher) 
	{
		$this->dispatcher = $dispatcher;
		$this->routes = array();
	}

	/**
	 * @param string $uri The URI to find a route object for
	 * @param array $httpMethods
	 * @return array The matched (and filtered) route objects as an array
	 */
	public function fromURI($uri, array $httpMethods = array(Route::HTTP_METHOD_ANY)) 
	{
		if(($matchedRoute = $this->match($uri)) === false)
			return 404;
		
		if(in_array(Route::HTTP_METHOD_ANY, $httpMethods) === true)
			return $matchedRoute;
		
		$routeMapping = $matchedRoute->getMapping();
		$routeMappingMethods = array_keys($routeMapping);
		
		return (in_array(Route::HTTP_METHOD_ANY, $routeMappingMethods) === true || count(array_intersect($routeMappingMethods, $httpMethods)) > 0)?$matchedRoute:405;
	}

	/**
	 * @param $str
	 * @return Route|false The matched routes as an array
	 */
	protected function match($str) 
	{
		if(($routeInfo = $this->dispatcher->match($str)) === false)
			return false;
		
		
		return $this->createRouteFromArray($this->routes[$routeInfo['index']], $routeInfo['params']);
	}

	/**
	 * Creates a Route object from route data given as array and its potential parameters
	 * @param array $routeArray
	 * @param array $params
	 * @return Route
	 */
	protected function createRouteFromArray(array $routeArray, array $params = array()) 
	{
		return new Route(
			isset($routeArray['name'])?$routeArray['name']:null,
			$routeArray['pattern'], 
			$routeArray['mapping'],
			$params
		);
	}

	/**
	 * Sets the available routes to match against
	 * @param array $routes Available routes to match against
	 */
	public function setRoutes(array $routes)
	{
		$this->routes = array_values($routes);
		$this->dispatcher->prepare($this->routes);
	}
}

/* EOF */
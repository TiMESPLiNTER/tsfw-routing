<?php

namespace timesplinter\tsfw\routing;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2014, TiMESPLiNTER Webdevelopment
 */
class Router {
	protected $routes;
	protected $paramKeywords;
	
	public function __construct() 
	{
		$this->routes = array();
		$this->paramKeywords = array(
			'numeric' => '\d+(?:\.\d+)?',
			'int' => '\d+',
			'alphabetic' => '[A-Za-z]+',
			'alphanumeric' => '[A-Za-z0-9]+',
			'string' => '\.+?'
		);
	}

	/**
	 * @param string $uri The URI to find a route object for
	 * @param array $httpMethods
	 * @return array The matched route objects as an array
	 */
	public function fromURI($uri, array $httpMethods = array(Route::HTTP_METHOD_ANY)) 
	{
		$matchedRoutes = $this->match($uri);
		
		if(in_array(Route::HTTP_METHOD_ANY, $httpMethods) === true)
			return $matchedRoutes;
		
		return array_filter($matchedRoutes, function(Route $route) use ($httpMethods) {
			$routeMapping = $route->getMapping();
			$routeMappingMethods = array_keys($routeMapping);
			
			return (in_array(Route::HTTP_METHOD_ANY, $routeMappingMethods) === true || count(array_intersect($routeMappingMethods, $httpMethods)) > 0);
		});
	}
	
	protected function match($str) 
	{
		$matchedRoutes = array();
		
		foreach($this->routes as $id => $r) {
			if(isset($r['params']) === true)
				$this->preparePattern($r);
			
			/** @var array $r */
			if(preg_match('/^' . str_replace('/', '\/', $r['pattern']) . '$/', $str, $matches) === 0)
				continue;

			array_shift($matches);
			
			if(isset($r['params']) === true)
				$matches = array_combine(array_keys($r['params']), $matches);
			
			$params = array();
						
			foreach($matches as $k => $param) {
				if(is_numeric($param) === true)
					$params[$k] = (($intParam = (int)$param) == $param)?$intParam:(double)$param;
				else
					$params[$k] = $param;
			}
			
			$route = $this->createRouteFromArray($r, $params);

			$matchedRoutes[$id] = $route;
		}

		return $matchedRoutes;
	}

	/**
	 * @param array $routeArray
	 * @param array $params
	 * @return Route
	 */
	protected function createRouteFromArray(array $routeArray, array $params = array()) 
	{
		return new Route(
			$routeArray['pattern'], 
			$routeArray['mapping'],
			$params
		);
	}
	
	protected function preparePattern(array &$routeData) 
	{
		$replace = array();
			
		foreach($routeData['params'] as $name => $pattern) {
			$replace['{%' . $name . '%}'] = '(' . strtr($pattern, $this->paramKeywords) . ')';
		}

		$routeData['pattern'] = strtr($routeData['pattern'], $replace);
	}

	/**
	 * Sets the available routes to match against
	 * @param array $routes Available routes to match against
	 */
	public function setRoutes(array $routes)
	{
		$this->routes = $routes;
	}

	/**
	 * Registers a new parameter keyword and its corresponding regex pattern
	 * @param string $keyword Name of the keyword
	 * @param string $regex The replacement regex 
	 */
	public function registerParamKeyword($keyword, $regex)
	{
		$this->paramKeywords[$keyword] = $regex;
	}
}

/* EOF */
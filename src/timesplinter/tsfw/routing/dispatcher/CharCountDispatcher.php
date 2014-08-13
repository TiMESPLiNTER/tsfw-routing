<?php

namespace timesplinter\tsfw\routing\dispatcher;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2014, TiMESPLiNTER Webdevelopment
 */
class CharCountDispatcher implements Dispatcher {
	protected $routesRegex;
	protected $routesCount;
	protected $routesParams;
	protected $regexTranslations;

	public function __construct() {
		$this->regexTranslations = array(
			'numeric' => '\d+(?:\.\d+)?',
			'int' => '\d+',
			'alphabetic' => '[A-Za-z]+',
			'alphanumeric' => '[A-Za-z0-9]+',
			'string' => '.+?'
		);
		$this->routesParams = array();
	}
	
	public function prepare(array $routes)
	{
		$i = 0;
		$routePatterns = array();

		foreach($routes as $route) {
			$paramNames = array();
			
			$preparedRoute = preg_replace_callback('~\{([A-Za-z0-9_]+)(?:\:(.+?))?\}~', function($matches) use (&$paramNames) {
				$paramName = $matches[1];
				$paramType = isset($matches[2])?$matches[2]:null;

				$paramRegex = '.+?';

				if($paramType !== null) {
					if(isset($this->regexTranslations[$paramType]))
						$paramRegex = $this->regexTranslations[$paramType];
					else
						$paramRegex = $paramType;
				}
				
				$paramNames[] = $paramName;

				return '(' . $paramRegex . ')';
			}, $route['pattern']);

			$this->routesParams[$i] = $paramNames;
			$routePatterns[$i] = $preparedRoute . ';(d{' . $i . '})d*';

			++$i;
		}

		$this->routesRegex = '~^(?|' . PHP_EOL . implode(PHP_EOL . '|', $routePatterns) . PHP_EOL . ')$~x';
		$this->routesCount = $i;
	}

	public function match($uri)
	{
		if(preg_match($this->routesRegex, $uri . ';' . str_repeat('d', $this->routesCount), $matches) === 0)
			return false;
	
		$lastMatch = array_pop($matches);
		$routeIndex = strlen($lastMatch);
		$paramsMatches = array_slice($matches, 1);
		$params = array();
		
		foreach(array_combine($this->routesParams[$routeIndex], $paramsMatches) as $key => $value) {
			if(is_numeric($value) === true)
				$params[$key] = (($intParam = (int)$value) == $value)?$intParam:(double)$value;
			else
				$params[$key] = $value;
		}
		
		return array(
			'index' => $routeIndex,
			'params' => $params
		);
	}

	/**
	 * Registers a new parameter type keyword and its corresponding regex pattern
	 * @param string $keyword Name of the keyword
	 * @param string $regex The replacement regex
	 */
	public function registerRegexTranslation($keyword, $regex)
	{
		$this->regexTranslations[$keyword] = $regex;
	}
}

/* EOF */ 
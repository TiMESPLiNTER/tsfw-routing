<?php

namespace timesplinter\tsfw\routing;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2014, TiMESPLiNTER Webdevelopment
 */
class Route
{
	const HTTP_METHOD_POST = 'POST';
	const HTTP_METHOD_GET = 'GET';
	const HTTP_METHOD_ANY = '*';
	const HTTP_METHOD_HEAD = 'HEAD';
	const HTTP_METHOD_DELETE = 'DELETE';
	const HTTP_METHOD_PUT = 'PUT';
	
	protected $name;
	protected $pattern;
	protected $params;
	protected $mapping;

	/**
	 * @param string $name
	 * @param string $pattern
	 * @param array $mapping
	 * @param array $params
	 */
	public function __construct($name, $pattern, array $mapping, array $params = array())
	{
		$this->name = $name;
		$this->pattern = $pattern;
		$this->params = $params;
		$this->mapping = $mapping;
	}

	/**
	 * @return string
	 */
	public function getPattern()
	{
		return $this->pattern;
	}

	/**
	 * @return array
	 */
	public function getParams()
	{
		return $this->params;
	}

	/**
	 * Get a specific parameters value
	 * @param int|string $index Parameter index (generic) or parameter name (associative)
	 * @return int|double|string|null Parameters value if parameter exists else null
	 */
	public function getParam($index)
	{
		return array_key_exists($index, $this->params)?$this->params[$index]:null;
	}

	/**
	 * @return array
	 */
	public function getMapping()
	{
		return $this->mapping;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
}

/* EOF */ 
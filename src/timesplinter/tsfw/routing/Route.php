<?php

namespace timesplinter\tsfw\routing;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2014, TiMESPLiNTER Webdevelopment
 */
class Route {
	const HTTP_METHOD_POST = 'POST';
	const HTTP_METHOD_GET = 'GET';
	const HTTP_METHOD_ANY = '*';
	const HTTP_METHOD_HEAD = 'HEAD';
	const HTTP_METHOD_DELETE = 'DELETE';
	const HTTP_METHOD_PUT = 'PUT';
	
	protected $pattern;
	protected $params;
	protected $mapping;
	
	public function __construct($pattern, array $mapping, array $params = array()) {
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
	 * @param int $index
	 * @return string|null
	 */
	public function getParam($index) {
		return array_key_exists($index, $this->params)?$this->params[$index]:null;
	}

	/**
	 * @return array
	 */
	public function getMapping()
	{
		return $this->mapping;
	}
}

/* EOF */ 
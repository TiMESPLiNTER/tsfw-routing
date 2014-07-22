<?php

namespace timesplinter\tsfw\routing;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2014, TiMESPLiNTER Webdevelopment
 */
interface RouterInterface {
	public function fromURI($uri, array $httpMethods);
}

/* EOF */ 
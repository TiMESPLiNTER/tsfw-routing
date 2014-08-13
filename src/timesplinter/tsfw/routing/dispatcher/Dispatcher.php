<?php

namespace timesplinter\tsfw\routing\dispatcher;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2014, TiMESPLiNTER Webdevelopment
 */
interface Dispatcher {
	/**
	 * This method gets called every time when routes of the Router get updated
	 * @param array $routes The new updated routes
	 * @return void
	 */
	public function prepare(array $routes);

	/**
	 * Matchs a given URI against the routes of the attached Router
	 * @param string $uri The URI to dispatch
	 * @return array|false The dispatched route index and its parameters or false if the URI didn't match any route
	 */
	public function match($uri);
}

/* EOF */ 
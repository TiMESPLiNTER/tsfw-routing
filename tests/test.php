<?php

namespace timesplinter\tsfw\routing\tests;

use timesplinter\tsfw\routing\Route;
use timesplinter\tsfw\routing\Router;

require '../vendor/autoload.php';

/**
 * @author Pascal Muenst <entwicklung@metanet.ch>
 * @copyright Copyright (c) 2014, METANET AG
 */ 

$router = new Router();
$router->setRoutes(array(
	'my-first-route' => array(
		'pattern' => 'items/item/(\d+)/view',
		'mapping' => array(
			'GET' => 'ch.timesplinter.site.AController.aMethod',
			'POST' => 'ch.timesplinter.site.BController.aMethod'
		)
	),
	
	'my-second-route' => array(
		'pattern' => 'items/item/{%item_id%}/version/{%version%}',
		'params' => array(
			'item_id' => 'numeric',
			'version' => '\d+\.\d+'
		),
		'mapping' => array(
			'*' => 'ch.timesplinter.site.AController.aMethod',
		)
	)
));

$routes = $router->fromURI('items/item/256/view', array(Route::HTTP_METHOD_ANY, Route::HTTP_METHOD_DELETE));
$routes2 = $router->fromURI('items/item/256/version/2.1', array(Route::HTTP_METHOD_GET));

echo'<pre>'; var_dump($routes, $routes2);
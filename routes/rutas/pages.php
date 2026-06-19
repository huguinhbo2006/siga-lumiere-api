<?php
	$router->get('pages/mostrar', ['uses' => 'PagesController@mostrar']);
	$router->post('pages/nuevo', ['uses' => 'PagesController@nuevo']);
	$router->post('pages/modificar', ['uses' => 'PagesController@modificar']);
	$router->post('pages/activar', ['uses' => 'PagesController@activar']);
	$router->post('pages/desactivar', ['uses' => 'PagesController@desactivar']);
	$router->post('pages/eliminar', ['uses' => 'PagesController@eliminar']);
	$router->post('pages/eliminar', ['uses' => 'PagesController@eliminar']);
	$router->post('pages/traer', ['uses' => 'PagesController@traer']);
	$router->post('pages/contenido', ['uses' => 'PagesController@contenido']);
	$router->post('pages/cursos', ['uses' => 'PagesController@cursos']);
?>
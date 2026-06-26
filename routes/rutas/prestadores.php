<?php
	$router->get('prestadores/mostrar', ['uses' => 'PrestadoresController@mostrar']);
	$router->post('prestadores/nuevo', ['uses' => 'PrestadoresController@nuevo']);
	$router->post('prestadores/modificar', ['uses' => 'PrestadoresController@modificar']);
	$router->post('prestadores/activar', ['uses' => 'PrestadoresController@activar']);
	$router->post('prestadores/desactivar', ['uses' => 'PrestadoresController@desactivar']);
	$router->post('prestadores/eliminar', ['uses' => 'PrestadoresController@eliminar']);
?>
<?php
	$router->get('metascursos/mostrar', ['uses' => 'MetascursosController@mostrar']);
	$router->post('metascursos/nuevo', ['uses' => 'MetascursosController@nuevo']);
	$router->post('metascursos/modificar', ['uses' => 'MetascursosController@modificar']);
	$router->post('metascursos/eliminar', ['uses' => 'MetascursosController@eliminar']);
?>
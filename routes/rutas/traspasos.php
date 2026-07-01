<?php
	$router->get('traspasos/mostrar', ['uses' => 'TraspasosController@mostrar']);
	$router->post('traspasos/nuevo', ['uses' => 'TraspasosController@nuevo']);
	$router->post('traspasos/modificar', ['uses' => 'TraspasosController@modificar']);
	$router->post('traspasos/activar', ['uses' => 'TraspasosController@activar']);
	$router->post('traspasos/desactivar', ['uses' => 'TraspasosController@desactivar']);
	$router->post('traspasos/eliminar', ['uses' => 'TraspasosController@eliminar']);
?>
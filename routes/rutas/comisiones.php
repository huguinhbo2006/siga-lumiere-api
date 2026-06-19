<?php
	$router->get('comisiones/mostrar', ['uses' => 'ComisionesController@mostrar']);
	$router->post('comisiones/comisiones', ['uses' => 'ComisionesController@comisiones']);
	$router->get('comisiones/traer', ['uses' => 'ComisionesController@traer']);
	$router->post('comisiones/nuevo', ['uses' => 'ComisionesController@nuevo']);
	$router->post('comisiones/modificar', ['uses' => 'ComisionesController@modificar']);
	$router->post('comisiones/eliminar', ['uses' => 'ComisionesController@eliminar']);
?>
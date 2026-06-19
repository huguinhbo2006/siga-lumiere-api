<?php
	$router->post('transferencias/nuevo', ['uses' => 'TransferenciasController@nuevo']);
	$router->post('transferencias/eliminar', ['uses' => 'TransferenciasController@eliminar']);
	$router->post('transferencias/modificar', ['uses' => 'TransferenciasController@modificar']);
	$router->post('transferencias/aceptar', ['uses' => 'TransferenciasController@aceptar']);
	$router->post('transferencias/rechazar', ['uses' => 'TransferenciasController@rechazar']);
	$router->post('transferencias/creadas', ['uses' => 'TransferenciasController@creadas']);
	$router->post('transferencias/recibidas', ['uses' => 'TransferenciasController@recibidas']);
?>
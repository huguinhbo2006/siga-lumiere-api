<?php
	$router->post('metasmes/nuevo', ['uses' => 'MetasmesController@nuevo']);
	$router->post('metasmes/modificar', ['uses' => 'MetasmesController@modificar']);
	$router->post('metasmes/eliminar', ['uses' => 'MetasmesController@eliminar']);
	$router->get('metasmes/mostrar', ['uses' => 'MetasmesController@mostrar']);
?>
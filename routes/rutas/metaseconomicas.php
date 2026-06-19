<?php
	$router->post('metaseconomicas/mostrar', ['uses' => 'MetaseconomicasController@mostrar']);
	$router->post('metaseconomicas/nuevo', ['uses' => 'MetaseconomicasController@nuevo']);
	$router->post('metaseconomicas/modificar', ['uses' => 'MetaseconomicasController@modificar']);
	$router->post('metaseconomicas/traer', ['uses' => 'MetaseconomicasController@traer']);
	$router->post('metaseconomicas/obtener', ['uses' => 'MetaseconomicasController@obtener']);
?>
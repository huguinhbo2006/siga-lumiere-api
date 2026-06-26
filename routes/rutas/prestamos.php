<?php
	$router->get('prestamos/mostrar', ['uses' => 'PrestamosController@mostrar']);
	$router->post('prestamos/nuevo', ['uses' => 'PrestamosController@nuevo']);
	$router->post('prestamos/traer', ['uses' => 'PrestamosController@traer']);
	$router->post('prestamos/abono', ['uses' => 'PrestamosController@abono']);
?>
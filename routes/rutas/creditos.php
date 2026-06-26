<?php
	$router->get('creditos/mostrar', ['uses' => 'CreditosController@mostrar']);
	$router->post('creditos/nuevo', ['uses' => 'CreditosController@nuevo']);
	$router->post('creditos/abono', ['uses' => 'CreditosController@abono']);
	$router->post('creditos/traer', ['uses' => 'CreditosController@traer']);
?>
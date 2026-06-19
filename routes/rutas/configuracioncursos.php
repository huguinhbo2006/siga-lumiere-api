<?php
	$router->post('configuracioncursos/mostrar', ['uses' => 'ConfiguracioncursosController@mostrar']);
	$router->post('configuracioncursos/nuevo', ['uses' => 'ConfiguracioncursosController@nuevo']);
?>
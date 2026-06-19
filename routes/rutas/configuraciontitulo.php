<?php
	$router->post('configuraciontitulo/mostrar', ['uses' => 'ConfiguraciontituloController@mostrar']);
	$router->post('configuraciontitulo/nuevo', ['uses' => 'ConfiguraciontituloController@nuevo']);
?>
<?php
	$router->post('configuracionbanner/mostrar', ['uses' => 'ConfiguracionbannerController@mostrar']);
	$router->post('configuracionbanner/nuevo', ['uses' => 'ConfiguracionbannerController@nuevo']);
	$router->post('configuracionbanner/actualizar', ['uses' => 'ConfiguracionbannerController@actualizar']);
	$router->post('configuracionbanner/eliminar', ['uses' => 'ConfiguracionbannerController@eliminar']);
?>
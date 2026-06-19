<?php
	$router->post('configuracionvideo/mostrar', ['uses' => 'ConfiguracionvideoController@mostrar']);
	$router->post('configuracionvideo/nuevo', ['uses' => 'ConfiguracionvideoController@nuevo']);
?>
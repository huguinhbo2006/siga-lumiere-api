<?php
	$router->post('cupones/cursosCongelados', ['uses' => 'CuponesController@cursosCongelados']);
	$router->post('cupones/canjear', ['uses' => 'CuponesController@canjear']);
?>
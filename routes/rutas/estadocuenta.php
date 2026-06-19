<?php
	$router->post('estadocuenta/mostrar', ['uses' => 'EstadocuentaController@mostrar']);
	$router->post('estadocuenta/agregarCargo', ['uses' => 'EstadocuentaController@agregarCargo']);
	$router->post('estadocuenta/quitarCargo', ['uses' => 'EstadocuentaController@quitarCargo']);
	$router->post('estadocuenta/agregarAbono', ['uses' => 'EstadocuentaController@agregarAbono']);
	$router->post('estadocuenta/quitarAbono', ['uses' => 'EstadocuentaController@quitarAbono']);
	$router->post('estadocuenta/agregarDescuento', ['uses' => 'EstadocuentaController@agregarDescuento']);
	$router->post('estadocuenta/quitarDescuento', ['uses' => 'EstadocuentaController@quitarDescuento']);
	$router->post('estadocuenta/agregarDevolucion', ['uses' => 'EstadocuentaController@agregarDevolucion']);
	$router->post('estadocuenta/quitarDevolucion', ['uses' => 'EstadocuentaController@quitarDevolucion']);
	$router->post('estadocuenta/agregarExtra', ['uses' => 'EstadocuentaController@agregarExtra']);
	$router->post('estadocuenta/quitarExtra', ['uses' => 'EstadocuentaController@quitarExtra']);
?>
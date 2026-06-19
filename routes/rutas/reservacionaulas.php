<?php
	$router->post('reservacionaulas/mostrar', ['uses' => 'ReservacionesaulasController@mostrar']);
	$router->post('reservacionaulas/reservar', ['uses' => 'ReservacionesaulasController@reservar']);
	$router->post('reservacionaulas/eliminar', ['uses' => 'ReservacionesaulasController@eliminar']);
	$router->post('reservacionaulas/reservadas', ['uses' => 'ReservacionesaulasController@reservadas']);
	$router->get('reservacionaulas/listas', ['uses' => 'ReservacionesaulasController@listas']);
	$router->post('reservacionaulas/horarios', ['uses' => 'ReservacionesaulasController@horarios']);
	$router->post('reservacionaulas/liberar', ['uses' => 'ReservacionesaulasController@liberar']);
?>
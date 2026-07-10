<?php
	$router->post('alumnos/buscar', ['uses' => 'AlumnosController@buscar']);
	$router->post('alumnos/datos', ['uses' => 'AlumnosController@datos']);
	$router->post('alumnos/modificarPersonales', ['uses' => 'AlumnosController@modificarPersonales']);
	$router->post('alumnos/modificarTutor', ['uses' => 'AlumnosController@modificarTutor']);
	$router->post('alumnos/modificarDomicilio', ['uses' => 'AlumnosController@modificarDomicilio']);
	$router->post('alumnos/fichas', ['uses' => 'AlumnosController@fichas']);
	$router->post('alumnos/actualizarNumeroRegistro', ['uses' => 'AlumnosController@actualizarNumeroRegistro']);
	$router->post('alumnos/actualizarEstatusFicha', ['uses' => 'AlumnosController@actualizarEstatusFicha']);
	$router->post('alumnos/actualizarDatosPublicitarios', ['uses' => 'AlumnosController@actualizarDatosPublicitarios']);
	$router->post('alumnos/actualizarDatosAspiracion', ['uses' => 'AlumnosController@actualizarDatosAspiracion']);
	$router->post('alumnos/traer', ['uses' => 'AlumnosController@traer']);
	$router->post('alumnos/inscripcion', ['uses' => 'AlumnosController@inscripcion']);
	$router->post('alumnos/alumno', ['uses' => 'AlumnosController@alumno']);
	$router->post('alumnos/modificarNombre', ['uses' => 'AlumnosController@modificarNombre']);
?>
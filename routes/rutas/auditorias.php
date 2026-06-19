<?php
	$router->get('auditorias/listas', ['uses' => 'AuditoriasController@listas']);
	$router->post('auditorias/ingresos', ['uses' => 'AuditoriasController@ingresos']);
	$router->post('auditorias/auditarIngreso', ['uses' => 'AuditoriasController@auditarIngreso']);
	$router->post('auditorias/desauditarIngreso', ['uses' => 'AuditoriasController@desauditarIngreso']);
	$router->post('auditorias/problemaIngreso', ['uses' => 'AuditoriasController@problemaIngreso']);
	$router->post('auditorias/financierosIngreso', ['uses' => 'AuditoriasController@financierosIngreso']);
	$router->post('auditorias/observacionesIngreso', ['uses' => 'AuditoriasController@observacionesIngreso']);
	$router->post('auditorias/voucherIngreso', ['uses' => 'AuditoriasController@voucherIngreso']);
	$router->post('auditorias/posiblesIngresos', ['uses' => 'AuditoriasController@posiblesIngresos']);
?>
<?php
use flight\Engine;
use flight\net\Router;
use app\controllers\AuthController;
//use Flight;

/** 
 * @var Router $router 
 * @var Engine $app
 */
$router->get('/', function() {
	Flight::redirect('/login');
});

$router->get('/login', function() {
	$controller = new AuthController(Flight::authService());
	$controller->showLogin();
});

$router->post('/login', function() {
	$controller = new AuthController(Flight::authService());
	$controller->handleLogin();
});

$router->get('/logout', function() {
	$controller = new AuthController(Flight::authService());
	$controller->handleLogout();
});


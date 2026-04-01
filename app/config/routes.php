<?php
use flight\Engine;
use flight\net\Router;
use app\controllers\AuthController;
use app\controllers\VoteController;
use app\controllers\ResultController;
//use Flight;

/** 
 * @var Router $router 
 * @var Engine $app
 */
$router->get('/', function() {
	if (Flight::authService()->requireAuth()) {
		Flight::redirect('/saisie');
		return;
	}

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

$router->get('/saisie', function() {
	$controller = new VoteController(Flight::voteRepository(), Flight::voteService(), Flight::authService());
	$controller->showSaisie();
});

$router->post('/saisie', function() {
	$controller = new VoteController(Flight::voteRepository(), Flight::voteService(), Flight::authService());
	$controller->handleSaisie();
});

$router->get('/tableau', function() {
	$controller = new VoteController(Flight::voteRepository(), Flight::voteService(), Flight::authService());
	$controller->showTableau();
});

$router->get('/resultats', function() {
	$controller = new ResultController(Flight::resultService(), Flight::authService());
	$controller->showResults();
});



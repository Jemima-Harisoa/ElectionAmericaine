<?php
use flight\Engine;
use flight\net\Router;
use app\controllers\AuthController;
use app\controllers\VoteController;
use app\controllers\ResultController;
use app\controllers\MapController;
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
	$controller = new ResultController(Flight::resultService(), Flight::pdfService(), Flight::authService());
	$controller->showResults();
});

$router->get('/resultats/pdf', function() {
	$controller = new ResultController(Flight::resultService(), Flight::pdfService(), Flight::authService());
	$controller->exportPDF();
});

$router->get('/carte', function() {
	$controller = new MapController(Flight::mapService(), Flight::voteRepository(), Flight::authService());
	$controller->showMap();
});

$router->get('/carte/etat/@id', function($id) {
	$controller = new MapController(Flight::mapService(), Flight::voteRepository(), Flight::authService());
	// Passer l'ID dans les url_vars
	\Flight::request()->url_vars['id'] = $id;
	$controller->getStateDetail();
});


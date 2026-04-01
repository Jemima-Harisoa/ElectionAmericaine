<?php

use flight\Engine;
use flight\database\PdoWrapper;
// use flight\debug\database\PdoQueryCapture;
// use Tracy\Debugger;
use app\repositories\UserRepository;
use app\repositories\VoteRepository;
use app\repositories\ResultRepository;
use app\services\AuthService;
use app\services\VoteService;
use app\services\ResultService;
use app\services\PdfService;
use app\services\MapService;

/** 
 * @var array $config This comes from the returned array at the bottom of the config.php file
 * @var Engine $app
 */

// uncomment the following line for MySQL
$dsn = 'mysql:host=' . $config['database']['host'] . ';dbname=' . $config['database']['dbname'] . ';charset=utf8mb4';

// uncomment the following line for SQLite
// $dsn = 'sqlite:' . $config['database']['file_path'];

// uncomment the following line for PostgreSQL
// $dsn = 'pgsql:host=' . $config['database']['host'] . ';port=' . $config['database']['port'] . ';dbname=' . $config['database']['dbname'];

// uncomment the following line for psql

// Uncomment the below lines if you want to add a Flight::db() service
// In development, you'll want the class that captures the queries for you. In production, not so much.
$pdoClass = PdoWrapper::class;

$app->register('db', $pdoClass, [ $dsn, $config['database']['user'] ?? null, $config['database']['password'] ?? null ]);

$app->map('userRepository', function() {
	return new UserRepository(Flight::db());
});

$app->map('authService', function() {
	return new AuthService(Flight::userRepository());
});

$app->map('voteRepository', function() {
	return new VoteRepository(Flight::db());
});

$app->map('voteService', function() {
	return new VoteService(Flight::voteRepository());
});

$app->map('resultRepository', function() {
	return new ResultRepository(Flight::db());
});

$app->map('resultService', function() {
	return new ResultService(Flight::resultRepository());
});

$app->map('pdfService', function() {
	return new PdfService(Flight::resultRepository());
});

$app->map('mapService', function() {
	return new MapService(Flight::resultRepository(), Flight::voteRepository());
});

// Got google oauth stuff? You could register that here
// $app->register('google_oauth', Google_Client::class, [ $config['google_oauth'] ]);

// Redis? This is where you'd set that up
// $app->register('redis', Redis::class, [ $config['redis']['host'], $config['redis']['port'] ]);


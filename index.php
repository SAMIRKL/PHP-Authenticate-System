<?php

require_once 'auth.php';
require_once 'route.php';
session_start();

$Router = new Routers();
$Router->addRoute('GET', '/', function () {
	echo 'OK';
});

$Router->addRoute('GET', '/admin/', function () {
	if (Authorize::verifyIdentity() ) {
		echo 'OK';
	} else {
		echo 'NOT_OK';
	}
});
$Router->addRoute('GET', '/login/{username}/{password}', function ($username, $password) {
//	if (isset($_GET['username']) and isset($_GET['password'])) {
	Authorize::Auth($username, $password);
	echo 'OK';

//	}
});

try {
	$Router->matchRoute();
} catch ( Exception $e ) {
	echo '404 NOT FOUND' . $e;
}
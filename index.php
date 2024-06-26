<?php

require_once 'auth.php';
require_once 'route.php';
session_start();

$Router = new Routers();
$Router->addRoute( 'GET', '/', function () {
	echo 'OK';
} );

$Router->addRoute( 'GET', '/admin/', function () {
//	var_dump($_SESSION);
//	exit();
	if ( Authorize::verifyIdentity() ) {
		echo 'OK';
	} else {
		echo '<b>Need Login</b><br><a href="/login/">Go to Login Page</a>';
	}
} );
$Router->addRoute( 'GET', '/login/{username}/{password}', function ( $username, $password ) {
//	if (isset($_GET['username']) and isset($_GET['password'])) {
	Authorize::auth( $username, $password );
	echo 'OK';

//	}
} );
$Router->addRoute( 'GET', '/login/', function () {
	if ( Authorize::verifyIdentity() ) {
		echo '<b>You Already Logged in</b>';
	} else {
		echo '<b>Set username and password</b>';
	}

} );

try {
	$Router->matchRoute();
} catch ( Exception $e ) {
	echo '404 NOT FOUND' . $e;
}
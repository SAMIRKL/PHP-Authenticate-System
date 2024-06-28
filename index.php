<?php
require_once 'vendor/autoload.php';
require_once 'auth.php';
require_once 'route.php';
session_start();


Routers::addRoute( 'GET', '/', function () {
	echo 'OK';
} );

Routers::addRoute( 'GET', '/admin/', function () {
//	var_dump($_SESSION);
//	exit();
	if ( Authorize::verifyIdentity(true) ) {
		echo 'OK';
	} else {
		echo '<b>Need Login</b><br><a href="/login/">Go to Login Page</a>';
	}
} );
Routers::addRoute( 'GET', '/login/{username}/{password}', function ( $username, $password ) {
//	if (isset($_GET['username']) and isset($_GET['password'])) {
	$data = array(
		"username" => $username,
		"password" => $password,

	);
	Authorize::auth( $data );
	echo 'OK';

//	}
} );
Routers::addRoute( 'GET', '/login/', function () {
	if ( Authorize::verifyIdentity() ) {
		echo '<b>You Already Logged in</b>';
	} else {
		echo '<b>Set username and password</b>';
	}

} );

try {
	Routers::matchRoute();
} catch ( Exception $e ) {
	echo '404 NOT FOUND' . $e;
}
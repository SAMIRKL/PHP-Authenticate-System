<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once 'vendor/autoload.php';

class Authorize {
	private static string $JWTKey = 'Laxo';

	public static function hash( string|array $value ): string {
		return bin2hex( JWT::encode( $value, self::$JWTKey, 'HS256' ) );
	}

	/**
	 * @throws JsonException
	 */
	public static function unHash( string $value ): false|string {
		return json_encode( JWT::decode( hex2bin( $value ), new Key( self::$JWTKey, 'HS256' ) ), JSON_THROW_ON_ERROR );
	}

	/**
	 * @throws Exception
	 */
	public static function auth( string|bool $username = false, string|bool $password = false ): void {
		if ( session_status() !== PHP_SESSION_ACTIVE ) {
			throw new Exception( 'Enable/Start Session' );
		}

		if ( ! isset( $_SESSION['userinfo'] ) ) {
			$_SESSION['userinfo'] = [];
		}

		$_SESSION['userinfo']['last_request'] = time();
		$_SESSION['userinfo']['ip']           = self::getIPAddress();
		$_SESSION['lastToken']                = $_COOKIE['token'] ?? '';

		if ( $username && $password ) {
			$_SESSION['userinfo']['username'] = $username;
			$_SESSION['userinfo']['password'] = $password;
			$current_token                    = self::hash( $_SESSION['userinfo'] );
			$_SESSION['current_token']        = $current_token;
			setcookie( 'token', $current_token, time() + 28800, "/" );
		}

	}

	/**
	 * @throws Exception
	 */
	public static function verifyIdentity( bool $isApi = false ): bool {

		$tokenData = self::validateToken( $_COOKIE['token'] ?? '' );

		if ( $tokenData && self::isValidToken( $tokenData ) ) {
			if ( $isApi ) {
				self::auth( $tokenData['username'], $tokenData['password'] );
			}

			return true;
		}

		self::auth();
		self::logOut();

		return false;

	}

	public static function getIPAddress() {
		return $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
	}

	public static function logOut(): bool {
		if ( isset( $_COOKIE['token'] ) ) {
			unset( $_COOKIE['token'] );
			unset( $_SESSION['userinfo'] );
			setcookie( 'token', '', - 1, '/' );

		} else {
			return 0;
		}

		return 1;
	}

	/**
	 * @throws JsonException
	 */
	private static function validateToken( string $token ): ?array {
		if ( ! $token ) {
			return null;
		}

		return json_decode( self::unHash( $token ), true, 512, JSON_THROW_ON_ERROR );
	}

	private static function isValidToken( array $tokenData ): bool {
		return isset( $tokenData['username'], $tokenData['password'], $tokenData['last_request'], $tokenData['ip'] ) &&
		       ( time() - $tokenData['last_request'] >= 1 ) &&
		       ( $tokenData['ip'] === self::getIPAddress() ) &&
		       ( $_SESSION['lastToken'] !== $_COOKIE['token'] ) &&
		       ( $tokenData['username'] === $_SESSION['userinfo']['username'] ) &&
		       ( $tokenData['password'] === $_SESSION['userinfo']['password'] ) &&
		       ( $_SESSION['current_token'] === $_COOKIE['token'] );
	}


}
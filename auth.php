<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class Authorize {
	private static string $JWTKey = 'your-key';

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
	 * @param array|bool $protectedData Data of use must be correct like username, password, name, phone number, ...
	 *
	 * @throws Exception
	 */
	public static function auth( array|bool $protectedData = false ): void {

		$_SESSION['userinfo']                 ??= [];
		$_SESSION['userinfo']['last_request'] = time();
		$_SESSION['userinfo']['ip']           = self::getIPAddress();
		$_SESSION['lastToken']                = $_COOKIE['token'] ?? '';

		if ( $protectedData ) {
			$_SESSION['userinfo']['protectedData'] = $protectedData;
			$current_token                         = self::hash( $_SESSION['userinfo'] );
			$_SESSION['current_token']             = $current_token;
			setcookie( 'token', $current_token, time() + 28800, "/" );
		}

	}

	/**
	 * verify identity of user
	 * @param bool $isApi if set to true, token will be updated after authentication
	 *
	 * @throws Exception
	 */
	public static function verifyIdentity( bool $isApi = false ): bool {

		$tokenData = self::validateToken( $_COOKIE['token'] ?? '' );

		if ( $tokenData && self::isValidToken( $tokenData ) ) {
			if ( $isApi ) {
				self::auth( $tokenData['protectedData'] );
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
		return isset( $tokenData['protectedData'], $tokenData['last_request'], $tokenData['ip'] ) &&
		       ( time() - $tokenData['last_request'] >= 1 ) &&
		       ( $tokenData['ip'] === self::getIPAddress() ) &&
		       ( $_SESSION['lastToken'] !== $_COOKIE['token'] ) &&
		       ( $tokenData['protectedData'] === $_SESSION['userinfo']['protectedData'] ) &&
		       ( $_SESSION['current_token'] === $_COOKIE['token'] );
	}


}
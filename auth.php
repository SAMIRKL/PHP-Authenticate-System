<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once 'vendor/autoload.php';

class Authorize {
	private static string $JTWKey = 'Laxo';

	public static function hash( string|array $value ): string {


		$jwt = JWT::encode( $value, self::$JTWKey, 'HS256' );

		return unpack( 'H*', $jwt )[1];

	}

	public static function unHash( string $value ): string {
		$jwt = pack( "H*", $value );

		return json_encode( JWT::decode( $jwt, new Key( self::$JTWKey, 'HS256' ) ) );
	}

	/**
	 * @throws Exception
	 */
	public static function Auth( string|bool $username = false, string|bool $password = false ): int {
		if ( session_status() == 2 ) {
			if ( ! isset( $_SESSION['userinfo'] ) ) {
				$_SESSION['userinfo'] = array();

			}

			$_SESSION['userinfo']['last_request'] = time();
			$_SESSION['userinfo']['ip']           = self::getIPAddress();
			$_SESSION['lastToken'] = $_COOKIE['token'] ?? '';
			if ( $username && $password ) {
				$_SESSION['userinfo']['username'] = $username;
				$_SESSION['userinfo']['password'] = $password;
				$current_token     = self::hash( $_SESSION['userinfo'] );
				setcookie( 'token', $current_token, time() + ( 1200 * 24 ), "/" );
			}
			return 1;


		}
		throw new Exception( 'Enable/Start Session' );

	}

	/**
	 * @throws Exception
	 */
	public static function verifyIdentity(): bool {

		if ( isset( $_COOKIE['token'] ) ) {
			$token = json_decode( self::unHash( $_COOKIE['token'] ), true );
			$valid = isset( $token['username'] )
			         && isset( $token['password'] )
			         && isset( $token['last_request'] )
			         && isset( $token['ip'] )
			         && $token['ip'] == self::getIPAddress()
			         && $_SESSION['lastToken'] != $_COOKIE['token'];

			if ( ! $valid ) {
				$result= 0;

			} else {
				$result= 1;
			}
		}
		if ($result ?? 0) {
			self::Auth($token['username'], $token['password']) ?? null;


		} else {
			self::Auth();
		}
		return $result ?? 0;
//

	}

	public
	static function getIPAddress() {
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}


}
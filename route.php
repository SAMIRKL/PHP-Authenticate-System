<?php

class Routers {
	protected array $routes = []; // stores routes

	public function addRoute( string $method, string $url, closure $target, bool $api = false ): void {
		$this->routes[ $method ][ $url ] = [$target, $api];
	}

	/**
	 * @throws Exception
	 */
	public function matchRoute(): void {
		$method = $_SERVER['REQUEST_METHOD'];
		$url    = str_replace( '/laxo/', '/', $_SERVER['REQUEST_URI'] );

		if ( isset( $this->routes[ $method ] ) ) {

			foreach ( $this->routes[ $method ] as $routeUrl => $target ) {

				$pattern = preg_replace_callback( '@{([^}]+)}@', function ( array $match ) {
					$name = $match[1];
					if ( $name[ - 1 ] == '?' ) {
						$name   = substr( $name, 0, - 1 );
						$suffix = '?';
					} else {
						$suffix = '';
					}

					$pattern = '[^/]+';

					return '(?<' . $name . '>' . $pattern . ')' . $suffix;
				}, $routeUrl );

				if ( preg_match( '@^' . $pattern . '$@', $url, $matches ) ) {
					$params = array_filter( $matches, 'is_string', ARRAY_FILTER_USE_KEY ); // Only keep named subpattern matches
					call_user_func_array( $target[0], $params );
					if (! $target[1]) {
						$_SESSION['last_page'] = explode('/', $_SERVER['REQUEST_URI'], 2)[1];
					}
//					var_dump($_SESSION);
					return;
				}
			}
		}
		throw new Exception( 'Route not found' );
	}
}
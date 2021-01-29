<?php

namespace Kodmanyagha\Helpers;

use Illuminate\Support\Facades\Log;

if ( !function_exists( 'getOnlyNumbers' ) ) {
	
	function getOnlyNumbers( $string, $length = 0 ) {
		preg_match_all( '!\d+!', $string, $matches );
		$string = implode( "", $matches[0] );
		unset( $matches );
		return substr( $string, $length );
	}
}

if ( !function_exists( 'startsWith' ) ) {

	function startsWith( $haystack, $needle ) {
		$length = strlen( $needle );
		return ( substr( $haystack, 0, $length ) === $needle );
	}
}

if ( !function_exists( 'numberFormat' ) ) {

	function numberFormat( $number, $decimal = 6 ) {
		$number = (float) $number;
		return number_format( $number, $decimal, '.', '' );
	}
}

if ( !function_exists( 'endsWith' ) ) {

	function endsWith( $haystack, $needle ) {
		$length = strlen( $needle );
		if ( $length == 0 ) {
			return true;
		}
		return ( substr( $haystack, -$length ) === $needle );
	}
}

if ( !function_exists( 'makeEnglish' ) ) {

	function makeEnglish( $str ) {
		$turkish = array(
			'ı',
			'ğ',
			'ü',
			'ş',
			'ö',
			'ç',
			'İ',
			'Ğ',
			'Ü',
			'Ö',
			'Ç'
		);
		$english = array(
			'i',
			'g',
			'u',
			's',
			'o',
			'c',
			'I',
			'G',
			'U',
			'O',
			'C'
		);

		$str = str_replace( $turkish, $english, $str );
		return $str;
	}
}

if ( !function_exists( 's2o' ) ) {

	/*************
	 * String to object (json)
	 *
	 * @param string $str
	 * @param boolean $assoc
	 * @return mixed
	 */
	function s2o( $str, $assoc = false ) {
		return json_decode( $str, $assoc );
	}
}

if ( !function_exists( 'o2s' ) ) {

	/*************
	 * Object to string
	 *
	 * @param mixed $obj
	 * @return string
	 */
	function o2s( $obj, $pretty = false ) {
		if ( $pretty )
			return json_encode( $obj, JSON_PRETTY_PRINT );

		return json_encode( $obj );
	}
}

if ( !function_exists( 'mo' ) ) {

	/*************
	 * Make object
	 *
	 * @param mixed $obj
	 * @return \stdClass
	 */
	function mo( $obj ) {
		return s2o( o2s( $obj ) );
	}
}

if ( !function_exists( 'ma' ) ) {

	/*************
	 * Make array
	 *
	 * @param mixed $obj
	 * @return array
	 */
	function ma( $obj ) {
		return s2o( o2s( $obj ), true );
	}
}

if ( !function_exists( 'rq' ) ) {

	/********************
	 * rq: random query
	 *
	 * @return object
	 */
	function rq() {
		if ( env( 'APP_ENV' ) == 'local' )
			return microtime( true );
		return date( 'i' );
	}
}

if ( !function_exists( 'ee' ) ) {

	/********************
	 * ee: export and exit
	 *
	 * @return object
	 */
	function ee() {
		echo "<pre>";
		$args = func_get_args();

		foreach ( $args as $arg ) {
			print_r( $arg );
		}

		exit();
	}
}

if ( !function_exists( 'gp' ) ) {

	/********************
	 * gp: Get Params
	 *
	 * @return array
	 */
	function gp() {
		$params = request()->route()->parameters();
		return array_values( $params );
	}
}

if ( !function_exists( 'sw' ) ) {

	/********************
	 * sw: starts with
	 *
	 * @param $string string
	 * @param $startString string
	 * @return bool
	 */
	function sw( $string, $startString ) {
		$len = strlen( $startString );
		return ( substr( $string, 0, $len ) === $startString );
	}
}

if ( !function_exists( 'ew' ) ) {

	/********************
	 * ew: ends with
	 *
	 * @param $string string
	 * @param $endString string
	 * @return bool
	 */
	function ew( $string, $endString ) {
		$len = strlen( $endString );
		if ( $len == 0 ) {
			return true;
		}
		return ( substr( $string, -$len ) === $endString );
	}
}

if ( !function_exists( 'lg' ) ) {

	/********************
	 * lg: log
	 *
	 */
	function lg( $anything, $type = 'debug' ) {
		if ( !in_array( gettype( $anything ), [
			'string',
			'number'
		] ) ) {
			$anything = o2s( $anything, true );
		}

		$bt = debug_backtrace();

		foreach ( $bt as $i => $b ) {
			if ( $i > 10 )
				break;

			//Log::debug($b['file']);
		}
		$cwd = base_path();

		$caller = array_shift( $bt );
		$file = $caller['file'];
		$file = substr( $file, strlen( $cwd ) );

		if ( $file == '/app/Helpers/VariousHelper.php' )
			$caller = array_shift( $bt );

		$file = $caller['file'];
		$line = $caller['line'];

		$file = substr( $file, strlen( $cwd ) );

		$message = $file . ':' . $line . ' ' . $anything;

		if ( strtolower( $type ) == 'debug' )
			Log::debug( $message );
		else if ( strtolower( $type ) == 'info' )
			Log::info( $message );
		else if ( strtolower( $type ) == 'warning' )
			Log::warning( $message );
		else if ( strtolower( $type ) == 'error' )
			Log::error( $message );
	}

	function lgd() {
		$args = func_get_args();
		if ( count( $args ) == 1 )
			lg( $args[0], 'debug' );
		else
			lg( $args, 'debug' );
	}

	function lgi() {
		$args = func_get_args();
		if ( count( $args ) == 1 )
			lg( $args[0], 'info' );
		else
			lg( $args, 'info' );
	}

	function lgw() {
		$args = func_get_args();
		if ( count( $args ) == 1 )
			lg( $args[0], 'warning' );
		else
			lg( $args, 'warning' );
	}

	function lge() {
		$args = func_get_args();
		if ( count( $args ) == 1 )
			lg( $args[0], 'error' );
		else
			lg( $args, 'error' );
	}
}
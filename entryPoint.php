<?php

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */

if ( defined( 'PHAR_BUILDER_VERSION' ) ) {
	// Do not initialize more then once.
	return;
}

define( 'PHAR_BUILDER_VERSION', '0.1' );
define( 'PHAR_BUILDER_DIRECTORY', __DIR__ );

spl_autoload_register( function ( $className ) {
	$className = ltrim( $className, '\\' );
	$fileName = '';
	$namespace = '';

	if ( $lastNsPos = strripos( $className, '\\') ) {
		$namespace = substr( $className, 0, $lastNsPos );
		$className = substr( $className, $lastNsPos + 1 );
		$fileName  = str_replace( '\\', '/', $namespace ) . '/';
	}

	$fileName .= str_replace( '_', '/', $className ) . '.php';

	$namespaceSegments = explode( '\\', $namespace );

	$inNamespace = $namespaceSegments[0] === 'PharBuilder';

	if ( $inNamespace ) {
		$inTestNamespace = count( $namespaceSegments ) > 1 && $namespaceSegments[1] === 'Tests';

		if ( $inTestNamespace ) {
			$pathParts = explode( '/', $fileName );
			array_shift( $pathParts );
			array_shift( $pathParts );
			$fileName = implode( '/', $pathParts );

			require_once __DIR__ . 'tests/phpunit/' . $fileName;
		}
		else {
			$pathParts = explode( '/', $fileName );
			array_shift( $pathParts );
			$fileName = implode( '/', $pathParts );

			require_once __DIR__ . '/src/' . $fileName;
		}
	}
} );
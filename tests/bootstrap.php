<?php

/**
 * PHPUnit bootstrap file for PharBuilder.
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */

error_reporting(E_ALL| E_STRICT);
ini_set("display_errors", 1);

if ( is_readable( __DIR__ . '/../vendor/autoload.php' ) ) {
	include __DIR__ . '/../vendor/autoload.php';
}

include __DIR__ . '/../entryPoint.php';
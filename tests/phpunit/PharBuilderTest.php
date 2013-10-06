<?php

namespace PharBuilder\Tests;

use PharBuilder\PharBuilder;
use RuntimeException;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PharBuilderTest extends \PHPUnit_Framework_TestCase {

	protected static $pharFileName;

	public static function setUpBeforeClass() {
		self::$pharFileName = PHAR_BUILDER_DIRECTORY . '/build/PharBuilder.phar';
	}

	public static function tearDownAfterClass() {
		unlink( self::$pharFileName );
	}

	public function testExecuteBuildPharCreatesPharFile() {
		$builder = $this->newPharBuilder();

		$builder->buildPhar();

		$this->assertFileExists( self::$pharFileName, 'A file with the correct name was created' );

		$this->assertEquals(
			42,
			include_once 'phar://' . self::$pharFileName . '/tests/testPhpFile.php'
		);
	}

	protected function newPharBuilder() {
		return new PharBuilder(
			self::$pharFileName,
			'PharBuilder',
			'entryPoint.php',
			PHAR_BUILDER_DIRECTORY
		);
	}

	public function testCannotExecuteBuildPharWhenPharReadonly() {
		$builder = $this->newPharBuilder();

		ini_set( 'phar.readonly', '1' );

		$exception = null;

		try {
			$builder->buildPhar();
		}
		catch ( RuntimeException $exception ) {}

		$this->assertNotNull(
			$exception,
			'An exception should be thrown when trying to create a phar when phar.readonly is true'
		);

		ini_set( 'phar.readonly', '0' );
	}

}
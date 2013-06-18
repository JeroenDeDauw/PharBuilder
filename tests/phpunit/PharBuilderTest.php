<?php

namespace PharBuilder\Tests;

use PharBuilder\PharBuilder;
use RuntimeException;

class PharBuilderTest extends \PHPUnit_Framework_TestCase {

	public static function tearDownAfterClass() {
		unlink( 'WikibaseDataModel.phar' );
	}

	public function testExecuteBuildPharCreatesPharFile() {
		$builder = $this->newDataModelBuilder();

		$builder->buildPhar();

		$this->assertFileExists( 'WikibaseDataModel.phar', 'A file with the correct name was created' );
	}

	protected function newDataModelBuilder() {
		return new PharBuilder(
			'WikibaseDataModel.phar',
			'WikibaseDataModel',
			'WikibaseDataModel.php',
			PHAR_BUILDER_DIRECTORY
		);
	}

	public function testCannotExecuteBuildPharWhenPharReadonly() {
		$builder = $this->newDataModelBuilder();

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
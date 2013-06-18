<?php

namespace PharBuilder;

use Phar;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use SplFileInfo;

class PharBuilder {

	protected $pharFileName;
	protected $pharInternalNamespace;
	protected $entryPoint;

	public function __construct( $pharFileName, $pharInternalNamespace, $entryPoint ) {
		$this->pharFileName = $pharFileName;
		$this->pharInternalNamespace = $pharInternalNamespace;
		$this->entryPoint = $entryPoint;
	}

	public function buildPhar() {
		$this->verifyCanBuild();

		$phar = new Phar(
			$this->pharFileName,
			Phar::CURRENT_AS_FILEINFO | Phar::KEY_AS_FILENAME,
			$this->pharInternalNamespace
		);

		$phar->startBuffering();

		$phar = $phar->convertToExecutable();

		$phar->setStub( $this->getStub() );

		$path = '/home/j/www/phase3/extensions/WikibaseDataModel/';

		/**
		 * @var splFileInfo $fileInfo
		 */
		foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $path ) ) as $fileInfo ) {
			if ( $fileInfo->getExtension() === 'php' ) {
				$phar->addFile( $fileInfo->getPathname() );
			}
		}

		$phar->stopBuffering();
	}

	protected function verifyCanBuild() {
		if ( ini_get( 'phar.readonly' ) ) {
			throw new RuntimeException(
				'PHP init setting phar.readonly is set to true. Cannot construct phar archives. See '
					. 'http://de3.php.net/manual/en/phar.configuration.php#ini.phar.readonly'
			);
		}
	}

	protected function getStub() {
		$entryPoint = 'phar://' . $this->pharInternalNamespace . '/' . $this->entryPoint;

		return <<<EOF
			<?php
Phar::mapPhar();
include '$entryPoint';
__HALT_COMPILER();
EOF;
	}

}
<?php

namespace PharBuilder;

use Phar;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use SplFileInfo;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PharBuilder {

	protected $pharFileName;
	protected $pharInternalNamespace;
	protected $entryPoint;
	protected $sourceDirectory;

	public function __construct( $pharFileName, $pharInternalNamespace, $entryPoint, $sourceDirectory ) {
		$this->pharFileName = $pharFileName;
		$this->pharInternalNamespace = $pharInternalNamespace;
		$this->entryPoint = $entryPoint;
		$this->sourceDirectory = $sourceDirectory;
	}

	public function buildPhar() {
		$this->verifyCanBuild();

		$phar = $this->getNewPhar();

		$this->addFilesToPhar( $phar );

		$phar->setStub( $this->getStub() );
	}

	protected function verifyCanBuild() {
		if ( ini_get( 'phar.readonly' ) ) {
			throw new RuntimeException(
				'PHP init setting phar.readonly is set to true. Cannot construct phar archives. See '
					. 'http://de3.php.net/manual/en/phar.configuration.php#ini.phar.readonly'
			);
		}
	}

	protected function getNewPhar() {
		$phar = new Phar(
			$this->pharFileName,
			Phar::CURRENT_AS_FILEINFO | Phar::KEY_AS_FILENAME,
			$this->pharInternalNamespace
		);

		return $phar->convertToExecutable();
	}

	protected function getStub() {
		$entryPoint = 'phar://' . $this->pharFileName . '/' . $this->entryPoint;

		return <<<EOF
<?php
Phar::mapPhar();
include '$entryPoint';
__HALT_COMPILER();
EOF;
	}

	protected function addFilesToPhar( Phar $phar ) {
		$phar->startBuffering();

		/**
		 * @var splFileInfo $fileInfo
		 */
		foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $this->sourceDirectory ) ) as $fileInfo ) {
			if ( $fileInfo->getExtension() === 'php' ) {
				$relativePath = substr( $fileInfo->getPathname(), strlen( $this->sourceDirectory ) + 1 );
				$phar->addFile( $fileInfo->getPathname(), $relativePath );
			}
		}

		$phar->stopBuffering();
	}

}
<?php
namespace WhisperSystems\LibSignal\Fingerprint;

use Exception;

class FingerprintIdentifierMismatchException extends Exception{

    /**
     * @var string $localIdentifier
     */
    private $localIdentifier;
    /**
     * @var string $remoteIdentifier
     */
    private $remoteIdentifier;
    /**
     * @var string $scannedLocalIdentifier
     */
    private $scannedLocalIdentifier;
    /**
     * @var string $scannedRemoteIdentifier
     */
    private $scannedRemoteIdentifier;

    public function __construct(string $localIdentifier,string $remoteIdentifier,string $scannedLocalIdentifier,string $scannedRemoteIdentifier){
        $this->localIdentifier = $localIdentifier;
        $this->remoteIdentifier = $remoteIdentifier;
        $this->scannedLocalIdentifier = $scannedLocalIdentifier;
        $this->scannedRemoteIdentifier = $scannedRemoteIdentifier;
    }

    public function getScannedRemoteIdentifier(): string{
        return $this->scannedRemoteIdentifier;
    }

    public function getScannedLocalIdentifier(): string{
        return $this->scannedLocalIdentifier;
    }

    public function getRemoteIdentifier(): string{
        return $this->remoteIdentifier;
    }

    public function getLocalIdentifier(): string{
        return $this->localIdentifier;
    }

}
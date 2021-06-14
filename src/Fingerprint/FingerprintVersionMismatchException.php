<?php
namespace WhisperSystems\LibSignal\Fingerprint;

use Exception;

class FingerprintVersionMismatchException extends Exception{

    /**
     * @var int $theirVersion
     */
    private $theirVersion;
    /**
     * @var int $ourVersion
     */
    private $ourVersion;

    public function __construct(int $theirVersion,int $ourVersion){
        parent::__construct();
        $this->theirVersion = $theirVersion;
        $this->ourVersion = $ourVersion;
    }

    public function getTheirVersion(): int{
        return $this->theirVersion;
    }

    public function getOurVersion(): int{
        return $this->ourVersion;
    }

}
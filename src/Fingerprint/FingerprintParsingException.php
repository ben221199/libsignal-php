<?php
namespace WhisperSystems\LibSignal\Fingerprint;

use Exception;

class FingerprintParsingException extends Exception{

    public function __construct(Exception $nested){
        parent::__construct('',0,$nested);
    }

}
<?php
namespace WhisperSystems\LibSignal;

use Exception;

class LegacyMessageException extends Exception{

    public function __construct(string $s){
        parent::__construct($s);
    }

}
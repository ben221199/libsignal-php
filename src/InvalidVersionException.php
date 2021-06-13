<?php
namespace WhisperSystems\LibSignal;

use Exception;

class InvalidVersionException extends Exception{

    public function __construct(string $detailMessage){
        parent::__construct($detailMessage);
    }

}
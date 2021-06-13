<?php
namespace WhisperSystems\LibSignal;

use Exception;
use Throwable;

class InvalidKeyIdException extends Exception{

    /**
     * InvalidKeyIdException constructor.
     * @param string|Throwable $detailMessageOrThrowable
     */
    public function __construct($detailMessageOrThrowable){
        if(is_string($detailMessageOrThrowable)){
            parent::__construct($detailMessageOrThrowable);
        }elseif($detailMessageOrThrowable instanceof Throwable){
            parent::__construct('',0,$detailMessageOrThrowable);
        }
    }

}
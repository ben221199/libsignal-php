<?php
namespace WhisperSystems\LibSignal;

use Exception;
use Throwable;

class NoSessionException extends Exception{

    /**
     * NoSessionException constructor.
     * @param string|Throwable $messageOrPrevious
     */
    public function __construct($messageOrPrevious){
        if(is_string($messageOrPrevious)){
            parent::__construct($messageOrPrevious);
        }elseif($messageOrPrevious instanceof Throwable){
            parent::__construct('',0,$messageOrPrevious);
        }
    }

}
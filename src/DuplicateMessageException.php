<?php
namespace WhisperSystems\LibSignal;

use Exception;

class DuplicateMessageException extends Exception{

    public function __construct(string $s) {
        parent::__construct($s);
    }

}
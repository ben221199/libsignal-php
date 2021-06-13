<?php
namespace WhisperSystems\LibSignal\Util\Guava;

use Exception;

class Preconditions{

    private function __construct(){}

    /**
     * @param $reference
     * @param null $a
     * @param null $b
     * @return mixed
     * @throws Exception
     */
    public static function checkNotNull($reference,$a=null,$b=null){
        if($reference===null){
            throw new Exception();
        }
        return $reference;
    }

}
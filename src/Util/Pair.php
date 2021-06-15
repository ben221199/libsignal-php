<?php
namespace WhisperSystems\LibSignal\Util;

class Pair{

    private $v1;
    private $v2;

    public function __construct($v1,$v2){
        $this->v1 = $v1;
        $this->v2 = $v2;
    }

    public function first(){
        return $this->v1;
    }

    public function second(){
        return $this->v2;
    }

    public function equals($o): bool{
        return $o instanceof Pair && $this->equal($o->first(),$this->first()) && $this->equal($o->second(),$this->second());
    }

    private function equal($first,$second): bool{
        if ($first===null && $second===null){
            return true;
        }
        if ($first===null || $second===null){
            return false;
        }
        if(method_exists($first,'equals')){
            return $first->equals($second);
        }
        return $first===$second;
    }

}
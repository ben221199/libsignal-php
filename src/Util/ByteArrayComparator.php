<?php
namespace WhisperSystems\LibSignal\Util;

abstract class ByteArrayComparator{

    protected function compare(string $left,string $right): int{
        for($i=0,$j=0;$i<strlen($left) && $j<strlen($right);$i++,$j++){
            $a = ($left[$i] & 0xff);
            $b = ($right[$j] & 0xff);
            if($a!==$b){
                return $a - $b;
            }
        }
        return strlen($left) - strlen($right);
    }

}
<?php
namespace WhisperSystems\LibSignal\Util;

class ByteUtil{

    /**
     * @param string $input
     * @param int $firstLength
     * @param int $secondLength
     * @param int|null $thirdLength
     * @return array
     */
    public static function split(string $input,int $firstLength,int $secondLength,?int $thirdLength=null): array{
        $parts = [
            substr($input,0,$firstLength),
            substr($input,$firstLength,$secondLength),
        ];
        if($thirdLength!==null){
            $parts[] = substr($input,$firstLength+$secondLength,$thirdLength);
        }
        return $parts;
    }

}
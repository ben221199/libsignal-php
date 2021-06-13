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

    public static function intToByteArray(int $value): string{
        $bytes = str_repeat("\0",4);
        ByteUtil::intToByteArray0($bytes,0,$value);
        return $bytes;
    }

    public static function intToByteArray0(string &$bytes,int $offset,int $value): int{
        $bytes[$offset + 3] = chr($value);
        $bytes[$offset + 2] = chr($value >> 8);
        $bytes[$offset + 1] = chr($value >> 16);
        $bytes[$offset] = chr($value >> 24);
        return 4;
    }

}
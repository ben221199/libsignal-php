<?php
namespace WhisperSystems\LibSignal\Util;

class ByteUtil{

    public static function combine(string ...$elements): string{
        $baos = '';

        foreach($elements AS $element){
            $baos .= $element;
        }

        return $baos;
    }

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

    public static function trim(string $input,int $length): string{
        return substr($input,0,$length);
    }

    public static function shortToByteArray(int $value): string{
        $bytes = str_repeat("\0",2);
        ByteUtil::shortToByteArray0($bytes,0,$value);
        return $bytes;
    }

    public static function shortToByteArray0(string $bytes,int $offset,int $value): int{
        $bytes[$offset+1] = chr($value);
        $bytes[$offset]   = chr($value >> 8);
        return 2;
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

    public static function byteArray5ToLong(string $bytes,int $offset): int{
        return ((ord($bytes[$offset]) & 0xff) << 32) | ((ord($bytes[$offset + 1]) & 0xff) << 24) | ((ord($bytes[$offset + 2]) & 0xff) << 16) | ((ord($bytes[$offset + 3]) & 0xff) << 8) | (ord($bytes[$offset + 4]) & 0xff);
    }

}
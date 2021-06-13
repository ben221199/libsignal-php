<?php
namespace WhisperSystems\LibSignal\Util;

use Exception;

class Hex{

    private const HEX_DIGITS = [
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f'
    ];

    public static function toString(string $bytes,int $offset=0,int $length=-1): string{
        if($length===-1){
            $length = strlen($bytes);
        }
        $buf = '';
        for($i=0;$i<$length;$i++) {
            self::appendHexChar($buf,$bytes[$offset + $i]);
            $buf .= ', ';
        }
        return $buf;
    }

    public static function toStringCondensed(string $bytes): string{
        $buf = '';
        for($i=0;$i<strlen($bytes);$i++){
            self::appendHexChar($buf,$bytes[$i]);
        }
        return $buf;
    }

    /**
     * @param string $encoded
     * @return string
     * @throws Exception
     */
    public static function fromStringCondensed(string $encoded): string{
        $data = str_split($encoded);
        $len = count($data);

        if(($len & 0x01)!==0){
            throw new Exception("Odd number of characters.");
        }

        $out = [];

        for($i=0,$j=0;$j<$len;$i++){
            $f = ord($data[$j]) << 4;
            $j++;
            $f = $f | ord($data[$j]);
            $j++;
            $out[$i] = ($f & 0xFF);
        }

        $out2 = '';
        foreach($out AS $o){
            $out2 = chr($o);
        }
        return $out2;
    }

    private static function appendHexChar(string &$buf,int $b): void{
        $buf .= '(byte)0x';
        $buf .= self::HEX_DIGITS[($b >> 4) & 0xf];
        $buf .= self::HEX_DIGITS[$b & 0xf];
    }

}
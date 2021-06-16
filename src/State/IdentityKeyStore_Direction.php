<?php
namespace WhisperSystems\LibSignal\State;

class IdentityKeyStore_Direction{

    private $enum;

    private static $SENDING;
    public static function SENDING(){
        if(!static::$SENDING){
            static::$SENDING = new IdentityKeyStore_Direction('SENDING');
        }
        return static::$SENDING;
    }

    private static $RECEIVING;
    public static function RECEIVING(){
        if(!static::$RECEIVING){
            static::$RECEIVING = new IdentityKeyStore_Direction('RECEIVING');
        }
        return static::$RECEIVING;
    }

    public function __construct($enum){
        $this->enum = $enum;
    }

}
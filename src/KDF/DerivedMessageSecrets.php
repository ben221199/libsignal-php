<?php
namespace WhisperSystems\LibSignal\KDF;

use AssertionError;
use Exception;

class DerivedMessageSecrets{

    public const SIZE = 80;
    private const CIPHER_KEY_LENGTH = 32;
    private const MAC_KEY_LENGTH = 32;
    private const IV_LENGTH = 16;

    /**
     * @var string $cipherKey
     */
    private $cipherKey;
    /**
     * @var string $macKey
     */
    private $macKey;
    /**
     * @var string $iv
     */
    private $iv;

    public function __construct(string $okm){
        try{
            $keys = [substr($okm,0,self::CIPHER_KEY_LENGTH),substr($okm,self::CIPHER_KEY_LENGTH,self::MAC_KEY_LENGTH),substr($okm,self::CIPHER_KEY_LENGTH+self::MAC_KEY_LENGTH,self::IV_LENGTH)];

            $this->cipherKey = $keys[0];
            $this->macKey = $keys[1];
            $this->iv = $keys[2];
        }catch(Exception $e){
            throw new AssertionError($e);
        }
    }

    public function getCipherKey(){
        return $this->cipherKey;
    }

    public function getMacKey(){
        return $this->macKey;
    }

    public function getIv(){
        return $this->iv;
    }

}
<?php
namespace WhisperSystems\LibSignal\ECC;

use WhisperSystems\LibSignal\InvalidKeyException;

class Curve{

    public const DJB_TYPE = 0x05;

    public static function isNative(): bool{
        return true;
    }

    public static function generateKeyPair(): ECKeyPair{
        $keyPair = null;//TODO

        return new ECKeyPair(new DjbECPublicKey($keyPair->getPublicKey()),
            new DjbECPrivateKey($keyPair->getPrivateKey()));
    }

    /**
     * @param string $bytes
     * @param int $offset
     * @return ECPublicKey
     * @throws InvalidKeyException
     */
    public static function decodePoint(string $bytes,int $offset): ECPublicKey{
        if ($bytes===null || strlen($bytes) - $offset < 1){
            throw new InvalidKeyException("No key type identifier");
        }

        $type = $bytes[$offset] & 0xFF;

        switch($type){
            case self::DJB_TYPE:
                if(strlen($bytes) - $offset < 33){
                    throw new InvalidKeyException("Bad key length: " . strlen($bytes));
                }

                $keyBytes = (string) substr($bytes,$offset+1,32);
                return new DjbECPublicKey($keyBytes);
            default:
                throw new InvalidKeyException("Bad key type: " . $type);
        }
    }

    public static function decodePrivatePoint(string $bytes): ECPrivateKey{
        return new DjbECPrivateKey($bytes);
    }

    /**
     * @param ECPublicKey $publicKey
     * @param ECPrivateKey $privateKey
     * @return string
     * @throws InvalidKeyException
     */
    public static function calculateAgreement(ECPublicKey $publicKey,ECPrivateKey $privateKey): string{
        if($publicKey===null){
            throw new InvalidKeyException("public value is null");
        }

        if($privateKey===null){
            throw new InvalidKeyException("private value is null");
        }

        if($publicKey->getType()!==$privateKey->getType()){
            throw new InvalidKeyException("Public and private keys must be of the same type!");
        }

        if($publicKey->getType()===self::DJB_TYPE){
            return null;//TODO
        }else{
            throw new InvalidKeyException("Unknown type: " . $publicKey->getType());
        }
    }

    /**
     * @param ECPrivateKey $signingKey
     * @param string $message
     * @return string
     * @throws InvalidKeyException
     */
    public static function calculateVrfSignature(ECPrivateKey $signingKey,string $message): string{
        if($signingKey===null || $message===null){
            throw new InvalidKeyException("Values must not be null");
        }

        if($signingKey->getType()===self::DJB_TYPE){
            /**@var DjbECPrivateKey $signingKey*/
            return null;//TODO
        } else {
            throw new InvalidKeyException("Unknown type: " . $signingKey->getType());
        }
    }

    /**
     * @param ECPublicKey $signingKey
     * @param string $message
     * @param string $signature
     * @return string
     * @throws InvalidKeyException
     */
    public static function verifyVrfSignature(ECPublicKey $signingKey,string $message,string $signature): string{
        if($signingKey == null || $message == null || $signature == null){
            throw new InvalidKeyException("Values must not be null");
        }
        if($signingKey->getType()==self::DJB_TYPE){
            /**@var DjbECPublicKey $signingKey*/
            return null;//TODO
        }else{
            throw new InvalidKeyException("Unknown type: " . $signingKey->getType());
        }
    }

}
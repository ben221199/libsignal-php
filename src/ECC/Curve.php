<?php
namespace WhisperSystems\LibSignal\ECC;

use Curve25519\Curve25519;
use WhisperSystems\LibSignal\InvalidKeyException;

class Curve{

    public const DJB_TYPE = 0x05;

    public static function isNative(): bool{
        return true;
    }

    public static function generateKeyPair(): ECKeyPair{
        //TODO Improve
        $tmpPriv = random_bytes(32);
        $tmpPub = (new Curve25519)->publicKey($tmpPriv);

        return new ECKeyPair(new DjbECPublicKey($tmpPub),new DjbECPrivateKey($tmpPriv));

//        return new ECKeyPair(new DjbECPublicKey($keyPair->getPublicKey()),
//            new DjbECPrivateKey($keyPair->getPrivateKey()));
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

        $type = ord($bytes[$offset]) & 0xFF;

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
            /**@var DjbECPublicKey $publicKey*/
            /**@var DjbECPrivateKey $privateKey*/
            //TODO Improve
            return (new Curve25519)->sharedKey($privateKey->getPrivateKey(),$publicKey->getPublicKey());
        }else{
            throw new InvalidKeyException("Unknown type: " . $publicKey->getType());
        }
    }

    /**
     * @param ECPublicKey $signingKey
     * @param string $message
     * @param string $signature
     * @return bool
     * @throws InvalidKeyException
     */
    public static function verifySignature(ECPublicKey $signingKey,string $message,string $signature): bool{
        if($signingKey===null || $message===null || $signature===null){
            throw new InvalidKeyException("Values must not be null");
        }

        if($signingKey->getType()==self::DJB_TYPE){
            //TODO Improve
            /**@var DjbECPublicKey $signingKey*/
            return (new \deemru\Curve25519)->verify($signature,$message,$signingKey->getPublicKey());
//            return Curve25519.getInstance(BEST)
//                .verifySignature(((DjbECPublicKey) signingKey).getPublicKey(), message, signature);
        }else{
            throw new InvalidKeyException("Unknown type: " . $signingKey->getType());
        }
    }

    /**
     * @param ECPrivateKey $signingKey
     * @param string $message
     * @return string
     * @throws InvalidKeyException
     */
    public static function calculateSignature(ECPrivateKey $signingKey,string $message): string{
        if($signingKey===null || $message===null){
            throw new InvalidKeyException("Values must not be null");
        }

        if ($signingKey->getType()===self::DJB_TYPE){
            /**@var DjbECPrivateKey $signingKey*/
            //TODO Improve
            return (new \deemru\Curve25519)->sign($message,$signingKey->getPrivateKey());
//            return Curve25519.getInstance(BEST)
//                .calculateSignature(((DjbECPrivateKey) signingKey).getPrivateKey(), message);
        }else{
            throw new InvalidKeyException("Unknown type: " . $signingKey->getType());
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
            return str_repeat("\0",32);//TODO
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
            return str_repeat("\0",32);//TODO
        }else{
            throw new InvalidKeyException("Unknown type: " . $signingKey->getType());
        }
    }

}
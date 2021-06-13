<?php
namespace WhisperSystems\LibSignal\Util;

use AssertionError;
use Exception;
use WhisperSystems\LibSignal\ECC\Curve;
use WhisperSystems\LibSignal\ECC\ECKeyPair;
use WhisperSystems\LibSignal\IdentityKey;
use WhisperSystems\LibSignal\IdentityKeyPair;
use WhisperSystems\LibSignal\State\PreKeyRecord;
use WhisperSystems\LibSignal\State\SignedPreKeyRecord;

class KeyHelper{

    private function __construct(){}

    public static function generateIdentityKeyPair(): IdentityKeyPair{
        $keyPair = Curve::generateKeyPair();
        $publicKey = new IdentityKey($keyPair->getPublicKey());
        return new IdentityKeyPair($publicKey,$keyPair->getPrivateKey());
    }

    public static function generateRegistrationId(bool $extendedRange): int{
        try{
            if($extendedRange){
                return random_int(0,PHP_INT_MAX-1)+1;
            }
            return random_int(0,16380)+1;
        }catch(Exception $e){
            throw new AssertionError($e);
        }
    }

    public static function getRandomSequence(int $max): int{
        try{
            return random_int(0,$max);
        }catch(Exception $e){
            throw new AssertionError($e);
        }
    }

    /**
     * @param int $start
     * @param int $count
     * @return PreKeyRecord[]|array
     */
    public static function generatePreKeys(int $start,int $count): array{
        $results = [];

        $start--;

        for($i=0;$i<$count;$i++){
            $results[] = new PreKeyRecord((($start + $i) % (Medium::MAX_VALUE-1)) + 1, Curve::generateKeyPair());
        }

        return $results;
    }

    /**
     * @param IdentityKeyPair $identityKeyPair
     * @param int $signedPreKeyId
     * @return SignedPreKeyRecord
     * @throws \WhisperSystems\LibSignal\InvalidKeyException
     */
    public static function generateSignedPreKey(IdentityKeyPair $identityKeyPair,int $signedPreKeyId): SignedPreKeyRecord{
        $keyPair = Curve::generateKeyPair();
        $signature = Curve::calculateSignature($identityKeyPair->getPrivateKey(),$keyPair->getPublicKey()->serialize());

        return new SignedPreKeyRecord($signedPreKeyId,round(microtime(true)*1000),$keyPair,$signature);
    }

    public static function generateSenderSigningKey(): ECKeyPair{
        return Curve::generateKeyPair();
    }

    public static function generateSenderKey(): string{
        try{
            $key = random_bytes(32);
            return $key;
        }catch(Exception $e){
            throw new AssertionError($e);
        }
    }

    public static function generateSenderKeyId(): int{
        try{
            return random_int(0,PHP_INT_MAX);
        }catch(Exception $e){
            throw new AssertionError($e);
        }
    }

}
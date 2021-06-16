<?php
namespace WhisperSystems\LibSignal\State;

use AssertionError;
use Exception;
use RuntimeException;
use WhisperSystems\LibSignal\ECC\Curve;
use WhisperSystems\LibSignal\ECC\ECKeyPair;
use WhisperSystems\LibSignal\InvalidKeyException;
use WhisperSystems\LibSignal\State\StorageProtos\SignedPreKeyRecordStructure;

class SignedPreKeyRecord{

    /**
     * @var SignedPreKeyRecordStructure $structure
     */
    private $structure;

    /**
     * SignedPreKeyRecord constructor.
     * @param string|int $serializedOrId
     * @param int $timestamp
     * @param ECKeyPair $keyPair
     * @param string $signature
     */
    public function __construct($serializedOrId,$timestamp=null,$keyPair=null,$signature=null){
        if(is_string($serializedOrId) && $timestamp===null && $keyPair===null && $signature===null){
            $this->structure = new SignedPreKeyRecordStructure;
            $this->structure->mergeFrom($serializedOrId);
        }elseif(is_int($serializedOrId) && is_int($timestamp) && $keyPair instanceof ECKeyPair && is_string($signature)){
            $this->structure = (new SignedPreKeyRecordStructure)
                ->setId($serializedOrId)
                ->setPublicKey($keyPair->getPublicKey()->serialize())
                ->setPrivateKey($keyPair->getPrivateKey()->serialize())
                ->setSignature($signature)
                ->setTimestamp($timestamp);
        }else{
            throw new RuntimeException('Invalid constructor call');
        }
    }

    public function getId(): int{
        return $this->structure->getId();
    }

    public function getTimestamp(): int{
        return $this->structure->getTimestamp();
    }

    public function getKeyPair(): ECKeyPair{
        try{
            $publicKey = Curve::decodePoint($this->structure->getPublicKey(),0);
            $privateKey = Curve::decodePrivatePoint($this->structure->getPrivateKey());

            return new ECKeyPair($publicKey,$privateKey);
        }catch (InvalidKeyException $e){
            throw new AssertionError($e);
        }
    }

    public function getSignature(): string{
        return $this->structure->getSignature();
    }

    public function serialize(): string{
        return $this->structure->serializeToString();
    }

}
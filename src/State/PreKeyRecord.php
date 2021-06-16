<?php
namespace WhisperSystems\LibSignal\State;

use AssertionError;
use RuntimeException;
use WhisperSystems\LibSignal\ECC\Curve;
use WhisperSystems\LibSignal\ECC\ECKeyPair;
use WhisperSystems\LibSignal\InvalidKeyException;
use WhisperSystems\LibSignal\State\StorageProtos\PreKeyRecordStructure;

class PreKeyRecord{

    /**
     * @var PreKeyRecordStructure $structure
     */
    private $structure;

    public function __construct($idOrSerialized,$keyPairOrNull=null){
        if(is_int($idOrSerialized) && $keyPairOrNull instanceof ECKeyPair){
            $this->structure = (new PreKeyRecordStructure)
                ->setId($idOrSerialized)
                ->setPublicKey($keyPairOrNull->getPublicKey()->serialize())
                ->setPrivateKey($keyPairOrNull->getPrivateKey()->serialize());
        }elseif(is_string($idOrSerialized) && $keyPairOrNull===null){
            $this->structure = new PreKeyRecordStructure;
            $this->structure->mergeFrom($idOrSerialized);
        }else{
            throw new RuntimeException('Invalid constructor call');
        }
    }

    public function getId(): int{
        return $this->structure->getId();
    }

    /**
     * @return ECKeyPair
     */
    public function getKeyPair(): ECKeyPair{
        try {
            $publicKey = Curve::decodePoint($this->structure->getPublicKey(),0);
            $privateKey = Curve::decodePrivatePoint($this->structure->getPrivateKey());

            return new ECKeyPair($publicKey,$privateKey);
        } catch (InvalidKeyException $e) {
            throw new AssertionError($e);
        }
    }

    public function serialize(): string{
        return $this->structure->serializeToString();
    }

}
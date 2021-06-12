<?php
namespace WhisperSystems\LibSignal\State;

use AssertionError;
use WhisperSystems\LibSignal\ECC\Curve;
use WhisperSystems\LibSignal\ECC\ECKeyPair;

class SignedPreKeyRecord{

    /**
     * @var SignedPreKeyRecordStructure $structure
     */
    private $structure;

    /**
     * SignedPreKeyRecord constructor.
     * @param string $serialized
     * @throws IOException
     */
    public function __construct(string $serialized){
        $this->structure = SignedPreKeyRecordStructure::parseFrom($serialized);
    }

    public function getId(): int{
        return $this->structure->getId();
    }

    public function getTimestamp(): int{
        return $this->structure->getTimestamp();
    }

    public function getKeyPair(): ECKeyPair{
        try{
            $publicKey = Curve::decodePoint($this->structure->getPublicKey()->toByteArray(),0);
            $privateKey = Curve::decodePrivatePoint($this->structure->getPrivateKey()->toByteArray());

            return new ECKeyPair($publicKey,$privateKey);
        }catch (InvalidKeyException $e){
            throw new AssertionError($e);
        }
    }

    public function getSignature(): string{
        return $this->structure->getSignature()->toByteArray();
    }

    public function serialize(): string{
        return $this->structure->toByteArray();
    }

}
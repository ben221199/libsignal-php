<?php
namespace WhisperSystems\LibSignal\Protocol;

use AssertionError;
use WhisperSystems\LibSignal\Devices\DeviceConsistencyCommitment;
use WhisperSystems\LibSignal\Devices\DeviceConsistencySignature;
use WhisperSystems\LibSignal\ECC\Curve;
use WhisperSystems\LibSignal\IdentityKey;
use WhisperSystems\LibSignal\IdentityKeyPair;
use WhisperSystems\LibSignal\InvalidKeyException;
use WhisperSystems\LibSignal\InvalidMessageException;
use WhisperSystems\LibSignal\Protocol\SignalProtos\DeviceConsistencyCodeMessage;

class DeviceConsistencyMessage{

    /**
     * @var DeviceConsistencySignature $signature
     */
    private $signature;
    /**
     * @var int $generation
     */
    private $generation;
    /**
     * @var string $serialized
     */
    private $serialized;

    /**
     * DeviceConsistencyMessage constructor.
     * @param DeviceConsistencyCommitment $commitment
     * @param IdentityKeyPair|string $identityKeyPairOrSerialized
     * @param IdentityKey|null $identityKeyOrNull
     * @throws InvalidMessageException
     * @throws \Exception
     */
    public function __construct(DeviceConsistencyCommitment $commitment,$identityKeyPairOrSerialized,$identityKeyOrNull=null){
        if($identityKeyPairOrSerialized instanceof IdentityKeyPair && $identityKeyOrNull==null){
            try{
                $identityKeyPair = $identityKeyPairOrSerialized;
                $signatureBytes = Curve::calculateVrfSignature($identityKeyPair->getPrivateKey(),$commitment->toByteArray());
                $vrfOutputBytes = Curve::verifyVrfSignature($identityKeyPair->getPublicKey()->getPublicKey(),$commitment->toByteArray(),$signatureBytes);

                $this->generation = $commitment->getGeneration();
                $this->signature  = new DeviceConsistencySignature($signatureBytes,$vrfOutputBytes);
                $this->serialized = (new DeviceConsistencyCodeMessage)
                    ->setGeneration($commitment->getGeneration())
                    ->setSignature($this->signature->getSignature())
                    ->serializeToString();
            }catch(InvalidKeyException $e){
                throw new AssertionError($e);
            }
        }elseif(is_string($identityKeyPairOrSerialized) && $identityKeyOrNull instanceof IdentityKey){
            try{
                $serialized = $identityKeyPairOrSerialized;
                $identityKey = $identityKeyOrNull;
                $message = new DeviceConsistencyCodeMessage;
                $message->mergeFromString($serialized);
                $vrfOutputBytes = Curve::verifyVrfSignature($identityKey->getPublicKey(),$commitment->toByteArray(),$message->getSignature());

                $this->generation = $message->getGeneration();
                $this->signature = new DeviceConsistencySignature($message->getSignature(),$vrfOutputBytes);
                $this->serialized = $serialized;
            }catch (InvalidKeyException $e) {
                throw new InvalidMessageException($e);
            }
        }
    }

    public function getSerialized(): string{
        return $this->serialized;
    }

    public function getSignature(): DeviceConsistencySignature{
        return $this->signature;
    }

    public function getGeneration(): int{
        return $this->generation;
    }

}
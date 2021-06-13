<?php
namespace WhisperSystems\LibSignal;

use Exception;
use Localstorage\IdentityKeyPairStructure;
use WhisperSystems\LibSignal\ECC\Curve;
use WhisperSystems\LibSignal\ECC\ECPrivateKey;

class IdentityKeyPair{

    /**
     * @var IdentityKey $publicKey
     */
    private $publicKey;
    /**
     * @var ECPrivateKey $privateKey
     */
    private $privateKey;

    /**
     * IdentityKeyPair constructor.
     * @param IdentityKey|string $publicKeyOrSerialized
     * @param ECPrivateKey|null $privateKeyOrNull
     * @throws InvalidKeyException
     */
    public function __construct($publicKeyOrSerialized,$privateKeyOrNull){
        if($publicKeyOrSerialized instanceof IdentityKey && $privateKeyOrNull instanceof ECPrivateKey){
            $this->publicKey  = $publicKeyOrSerialized;
            $this->privateKey = $privateKeyOrNull;
        }elseif(is_string($publicKeyOrSerialized) && $privateKeyOrNull===null){
            try{
                $structure = IdentityKeyPairStructure::parseFrom($publicKeyOrSerialized);
                $this->publicKey  = new IdentityKey($structure->getPublicKey()->toByteArray(),0);
                $this->privateKey = Curve::decodePrivatePoint($structure->getPrivateKey()->toByteArray());
            }catch(Exception $e){
                throw new InvalidKeyException($e);
            }
        }
    }

    public function getPublicKey(): IdentityKey{
        return $this->publicKey;
    }

    public function getPrivateKey(): ECPrivateKey{
        return $this->privateKey;
    }

    public function serialize(): string{
        return (new IdentityKeyPairStructure)
            ->setPublicKey($this->publicKey->serialize())
            ->setPrivateKey($this->privateKey->serialize())
            ->serializeToString();
    }

}
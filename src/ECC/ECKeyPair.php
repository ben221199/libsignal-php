<?php
namespace WhisperSystems\LibSignal\ECC;

class ECKeyPair{

    /**
     * @var ECPublicKey $publicKey
     */
    private $publicKey;
    /**
     * @var ECPrivateKey $privateKey
     */
    private $privateKey;

    public function __construct(ECPublicKey $publicKey,ECPrivateKey $privateKey){
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
    }

    public function getPublicKey(): ECPublicKey{
        return $this->publicKey;
    }

    public function getPrivateKey(): ECPrivateKey{
        return $this->privateKey;
    }

}
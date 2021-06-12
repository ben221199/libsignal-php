<?php
namespace WhisperSystems\LibSignal\ECC2;

class DjbECPrivateKey implements ECPrivateKey{

    /**
     * @var string $privateKey
     */
    private $privateKey;

    public function __construct(string $privateKey){
        $this->privateKey = $privateKey;
    }

    public function serialize(): string{
        return $this->privateKey;
    }

    public function getType(): int{
        return Curve::DJB_TYPE;
    }

    public function getPrivateKey(){
        return $this->privateKey;
    }

}
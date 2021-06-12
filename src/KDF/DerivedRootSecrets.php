<?php
namespace WhisperSystems\LibSignal\KDF;

class DerivedRootSecrets{

    public const SIZE = 64;

    /**
     * @var string $rootKey
     */
    private $rootKey;
    /**
     * @var string $chainKey
     */
    private $chainKey;

    public function __construct(string $okm){
        $keys = chunk_split($okm,32,32);
        $this->rootKey  = $keys[0];
        $this->chainKey = $keys[1];
    }

    public function getRootKey(): string{
        return $this->rootKey;
    }

    public function getChainKey(): string{
        return $this->chainKey;
    }

}
<?php
namespace WhisperSystems\LibSignal;

use Exception;

class UntrustedIdentityException extends Exception{

    /**
     * @var string $name
     */
    private $name;
    /**
     * @var IdentityKey $key
     */
    private $key;

    public function __construct(string $name,IdentityKey $key){
        $this->name = $name;
        $this->key = $key;
    }

    public function getUntrustedIdentity(): IdentityKey{
        return $this->key;
    }

    public function getName(): string{
        return $this->name;
    }

}
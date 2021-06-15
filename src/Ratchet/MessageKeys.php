<?php
namespace WhisperSystems\LibSignal\Ratchet;

class MessageKeys{

    /**
     * @var string $cipherKey
     */
    private $cipherKey;
    /**
     * @var string $macKey
     */
    private $macKey;
    /**
     * @var string $iv
     */
    private $iv;
    /**
     * @var int $counter
     */
    private $counter;

    public function __construct(string $cipherKey,string $macKey,string $iv,int $counter){
        $this->cipherKey = $cipherKey;
        $this->macKey = $macKey;
        $this->iv = $iv;
        $this->counter = $counter;
    }

    public function getCipherKey(): string{
        return $this->cipherKey;
    }

    public function getMacKey(): string{
        return $this->macKey;
    }

    public function getIv(): string{
        return $this->iv;
    }

    public function getCounter(): int{
        return $this->counter;
    }

}
<?php
namespace WhisperSystems\LibSignal\Ratchet;

class MessageKeys{

    /**
     * @var SecretKeySpec $cipherKey
     */
    private $cipherKey;
    /**
     * @var SecretKeySpec $macKey
     */
    private $macKey;
    /**
     * @var IvParameterSpec $iv
     */
    private $iv;
    /**
     * @var int $counter
     */
    private $counter;

    public function __construct(SecretKeySpec $cipherKey,SecretKeySpec $macKey,IvParameterSpec $iv,int $counter){
        $this->cipherKey = $cipherKey;
        $this->macKey = $macKey;
        $this->iv = $iv;
        $this->counter = $counter;
    }

    public function getCipherKey(): SecretKeySpec{
        return $this->cipherKey;
    }

    public function getMacKey(): SecretKeySpec{
        return $this->macKey;
    }

    public function getIv(): IvParameterSpec{
        return $this->iv;
    }

    public function getCounter(): int{
        return $this->counter;
    }

}
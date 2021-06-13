<?php
namespace WhisperSystems\LibSignal\Ratchet;

use AssertionError;
use WhisperSystems\LibSignal\InvalidKeyException;
use WhisperSystems\LibSignal\KDF\DerivedMessageSecrets;
use WhisperSystems\LibSignal\KDF\HKDF;

class ChainKey{

    private const MESSAGE_KEY_SEED = "\x01";
    private const CHAIN_KEY_SEED = "\x02";

    /**
     * @var HKDF $kdf
     */
    private $kdf;
    /**
     * @var string $key
     */
    private $key;
    /**
     * @var int $index
     */
    private $index;

    public function __construct(HKDF $kdf,string $key,int $index){
        $this->kdf = $kdf;
        $this->key = $key;
        $this->index = $index;
    }

    public function getKey(): string{
        return $this->key;
    }

    public function getIndex(): int{
        return $this->index;
    }

    public function getNextChainKey(): ChainKey{
        $nextKey = $this->getBaseMaterial(self::CHAIN_KEY_SEED);
        return new ChainKey($this->kdf, $nextKey, $this->index + 1);
    }

    public function getMessageKeys(): MessageKeys{
        $inputKeyMaterial = $this->getBaseMaterial(self::MESSAGE_KEY_SEED);
        $keyMaterialBytes = $this->kdf->deriveSecrets0($inputKeyMaterial,'WhisperMessageKeys',DerivedMessageSecrets::SIZE);
        $keyMaterial = new DerivedMessageSecrets($keyMaterialBytes);

        return new MessageKeys($keyMaterial->getCipherKey(),$keyMaterial->getMacKey(),$keyMaterial->getIv(),$this->index);
    }

    private function getBaseMaterial(string $seed): string{
        try{
            return hash_hmac('sha256',$seed,$this->key,true);
        }catch(InvalidKeyException $e) {
            throw new AssertionError($e);
        }
    }

}
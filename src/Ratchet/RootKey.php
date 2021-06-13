<?php
namespace WhisperSystems\LibSignal\Ratchet;

use WhisperSystems\LibSignal\ECC\Curve;
use WhisperSystems\LibSignal\ECC\ECKeyPair;
use WhisperSystems\LibSignal\ECC\ECPublicKey;
use WhisperSystems\LibSignal\InvalidKeyException;
use WhisperSystems\LibSignal\KDF\DerivedRootSecrets;
use WhisperSystems\LibSignal\KDF\HKDF;

class RootKey{

    /**
     * @var HKDF $kdf
     */
    private $kdf;
    /**
     * @var string $key
     */
    private $key;

    public function __construct(HKDF $kdf,string $key){
        $this->kdf = $kdf;
        $this->key = $key;
    }

    public function getKeyBytes(): string{
        return $this->key;
    }

    /**
     * @param ECPublicKey $theirRatchetKey
     * @param ECKeyPair $ourRatchetKey
     * @return Pair<RootKey, ChainKey>
     * @throws InvalidKeyException
     */
    public function createChain(ECPublicKey $theirRatchetKey,ECKeyPair $ourRatchetKey): Pair{
        $sharedSecret = Curve::calculateAgreement($theirRatchetKey,$ourRatchetKey->getPrivateKey());
        $derivedSecretBytes = $this->kdf->deriveSecrets($sharedSecret,$this->key,'WhisperRatchet',DerivedRootSecrets::SIZE);
        $derivedSecrets = new DerivedRootSecrets($derivedSecretBytes);

        $newRootKey  = new RootKey($this->kdf,$derivedSecrets->getRootKey());
        $newChainKey = new ChainKey($this->kdf,$derivedSecrets->getChainKey(),0);

        return new Pair($newRootKey,$newChainKey);
    }

}
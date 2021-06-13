<?php
namespace WhisperSystems\LibSignal;

use WhisperSystems\LibSignal\ECC\Curve;
use WhisperSystems\LibSignal\ECC\ECPublicKey;

class IdentityKey{

    /**
     * @var ECPublicKey $publicKey
     */
    private $publicKey;

    /**
     * IdentityKey constructor.
     * @param ECPublicKey|string $publicKeyOrBytes
     * @param int|null $offset
     */
    public function __construct($publicKeyOrBytes,$offset=null){
        if($publicKeyOrBytes instanceof ECPublicKey && $offset==null){
            $this->publicKey = $publicKeyOrBytes;
        }elseif(is_string($publicKeyOrBytes) && is_int($offset)){
            $this->publicKey = Curve::decodePoint($publicKeyOrBytes,$offset);
        }
    }

    public function getPublicKey(): ECPublicKey{
        return $this->publicKey;
    }

    public function serialize(): string{
        return $this->publicKey->serialize();
    }

    public function getFingerprint(): string{
        return Hex::toString($this->publicKey->serialize());
    }

public function equals($other): bool{
    if($other===null){
        return false;
    }
    if(!($other instanceof IdentityKey)){
        return false;
    }
    /**@var IdentityKey $other*/
    return $this->publicKey->equals($other->getPublicKey());
}

}
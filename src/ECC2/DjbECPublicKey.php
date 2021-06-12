<?php
namespace WhisperSystems\LibSignal\ECC2;

class DjbECPublicKey implements ECPublicKey{

    /**
     * @var string $publicKey
     */
    private $publicKey;

    public function __construct(string $publicKey){
        $this->publicKey = $publicKey;
    }

    public function serialize(): string{
        $type = (string) decbin(Curve::DJB_TYPE);
        return $type.$this->publicKey;
    }

    public function getType(): int{
        return Curve::DJB_TYPE;
    }

    public function equals($other): bool{
        if($other==null){
            return false;
        }
        if(!($other instanceof DjbECPublicKey)){
            return false;
        }
        /**@var DjbECPublicKey $other*/
        return $this->publicKey===$other->publicKey;
    }

    public function compareTo(ECPublicKey $another){
        $thisPublicKey = $this->publicKey;
        /**@var DjbECPublicKey $another*/
        $anotherPublicKey = $another->publicKey;

        $a = bindec($thisPublicKey);
        $b = bindec($anotherPublicKey);
        return $a===$b?0:($a>$b?1:-1);
    }

    public function getPublicKey(): string{
        return $this->publicKey;
    }

}
<?php
namespace WhisperSystems\LibSignal\State;

use WhisperSystems\LibSignal\ECC\ECPublicKey;
use WhisperSystems\LibSignal\IdentityKey;

class PreKeyBundle{

    /**
     * @var int $registrationId
     */
    private $registrationId;

    /**
     * @var int $deviceId
     */
    private $deviceId;

    /**
     * @var int $preKeyId
     */
    private $preKeyId;
    /**
     * @var ECPublicKey $preKeyPublic
     */
    private $preKeyPublic;

    /**
     * @var int $signedPreKeyId
     */
    private $signedPreKeyId;
    /**
     * @var ECPublicKey $signedPreKeyPublic
     */
    private $signedPreKeyPublic;
    /**
     * @var string $signedPreKeySignature
     */
    private $signedPreKeySignature;

    /**
     * @var IdentityKey $identityKey
     */
    private $identityKey;

    public function __construct(int $registrationId,int $deviceId,int $preKeyId,ECPublicKey $preKeyPublic,int $signedPreKeyId,?ECPublicKey $signedPreKeyPublic,?string $signedPreKeySignature,IdentityKey $identityKey){
        $this->registrationId = $registrationId;
        $this->deviceId = $deviceId;
        $this->preKeyId = $preKeyId;
        $this->preKeyPublic = $preKeyPublic;
        $this->signedPreKeyId = $signedPreKeyId;
        $this->signedPreKeyPublic = $signedPreKeyPublic;
        $this->signedPreKeySignature = $signedPreKeySignature;
        $this->identityKey = $identityKey;
    }

    /**
     * @return int the device ID this PreKey belongs to.
     */
    public function getDeviceId(): int{
        return $this->deviceId;
    }

    /**
     * @return int the unique key ID for this PreKey.
     */
    public function getPreKeyId(): int{
        return $this->preKeyId;
    }

    /**
     * @return ECPublicKey the public key for this PreKey.
     */
    public function getPreKey(): ECPublicKey{
        return $this->preKeyPublic;
    }

    /**
     * @return int the unique key ID for this signed prekey.
     */
    public function getSignedPreKeyId(): int{
        return $this->signedPreKeyId;
    }

    /**
     * @return ECPublicKey the signed prekey for this PreKeyBundle.
     */
    public function getSignedPreKey(): ECPublicKey{
        return $this->signedPreKeyPublic;
    }

    /**
     * @return string the signature over the signed  prekey.
     */
    public function getSignedPreKeySignature(): string{
        return $this->signedPreKeySignature;
    }

    /**
     * @return IdentityKey the {@link org.whispersystems.libsignal.IdentityKey} of this PreKeys owner.
     */
    public function getIdentityKey(): IdentityKey{
        return $this->identityKey;
    }

    /**
     * @return int the registration ID associated with this PreKey.
     */
    public function getRegistrationId(): int{
        return $this->registrationId;
    }

}
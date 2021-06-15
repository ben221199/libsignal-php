<?php
namespace WhisperSystems\LibSignal\State\Impl;

use WhisperSystems\LibSignal\IdentityKey;
use WhisperSystems\LibSignal\IdentityKeyPair;
use WhisperSystems\LibSignal\SignalProtocolAddress;
use WhisperSystems\LibSignal\State\IdentityKeyStore;
use WhisperSystems\LibSignal\State\IdentityKeyStore_Direction;

class InMemoryIdentityKeyStore implements IdentityKeyStore{

    private $trustedKeys = [];

    /**
     * @var IdentityKeyPair $identityKeyPair
     */
    private $identityKeyPair;
    /**
     * @var int $localRegistrationId
     */
    private $localRegistrationId;

    public function __construct(IdentityKeyPair $identityKeyPair,int $localRegistrationId){
        $this->identityKeyPair = $identityKeyPair;
        $this->localRegistrationId = $localRegistrationId;
    }

    public function getIdentityKeyPair(): IdentityKeyPair{
        return $this->identityKeyPair;
    }

    public function getLocalRegistrationId(): int{
        return $this->localRegistrationId;
    }

    public function saveIdentity(SignalProtocolAddress $address,IdentityKey $identityKey): bool{
        $existing = $this->trustedKeys[$address->getDeviceId()];

        if(!$identityKey->equals($existing)){
            $trustedKeys[$address->getDeviceId()] = $identityKey;
            return true;
        }else{
            return false;
        }
    }

    public function isTrustedIdentity(SignalProtocolAddress $address,IdentityKey $identityKey,IdentityKeyStore_Direction $direction): bool{
        $trusted = $this->trustedKeys[$address->getDeviceId()];
        return ($trusted===null || $trusted->equals($identityKey));
    }

    public function getIdentity(SignalProtocolAddress $address): IdentityKey{
        return $this->trustedKeys[$address->getDeviceId()];
    }

}
<?php
namespace WhisperSystems\LibSignal\State\Impl;

use WhisperSystems\LibSignal\IdentityKey;
use WhisperSystems\LibSignal\IdentityKeyPair;
use WhisperSystems\LibSignal\InvalidKeyIdException;
use WhisperSystems\LibSignal\SignalProtocolAddress;
use WhisperSystems\LibSignal\State\IdentityKeyStore_Direction;
use WhisperSystems\LibSignal\State\PreKeyRecord;
use WhisperSystems\LibSignal\State\SessionRecord;
use WhisperSystems\LibSignal\State\SignedPreKeyRecord;

class InMemorySignalProtocolStore{

    /**
     * @var InMemoryPreKeyStore $preKeyStore
     */
    private $preKeyStore;
    /**
     * @var InMemorySessionStore $sessionStore
     */
    private $sessionStore;
    /**
     * @var InMemorySignedPreKeyStore $signedPreKeyStore
     */
    private $signedPreKeyStore;

    /**
     * @var InMemoryIdentityKeyStore $identityKeyStore
     */
    private $identityKeyStore;

    public function __construct(IdentityKeyPair $identityKeyPair,int $registrationId){
        $this->identityKeyStore = new InMemoryIdentityKeyStore($identityKeyPair,$registrationId);
    }

    public function getIdentityKeyPair(): IdentityKeyPair{
        return $this->identityKeyStore->getIdentityKeyPair();
    }

    public function getLocalRegistrationId(): int{
        return $this->identityKeyStore->getLocalRegistrationId();
    }

    public function saveIdentity(SignalProtocolAddress $address,IdentityKey $identityKey): bool{
        return $this->identityKeyStore->saveIdentity($address,$identityKey);
    }

    public function isTrustedIdentity(SignalProtocolAddress $address,IdentityKey $identityKey,IdentityKeyStore_Direction $direction): bool{
        return $this->identityKeyStore->isTrustedIdentity($address,$identityKey,$direction);
    }

    public function getIdentity(SignalProtocolAddress $address): IdentityKey{
        return $this->identityKeyStore->getIdentity($address);
    }

    /**
     * @param int $preKeyId
     * @return PreKeyRecord
     * @throws InvalidKeyIdException
     */
    public function loadPreKey(int $preKeyId): PreKeyRecord{
        return $this->preKeyStore->loadPreKey($preKeyId);
    }

    public function storePreKey(int $preKeyId,PreKeyRecord $record): void{
        $this->preKeyStore->storePreKey($preKeyId,$record);
    }

    public function containsPreKey(int $preKeyId): bool{
        return $this->preKeyStore->containsPreKey($preKeyId);
    }

    public function removePreKey(int $preKeyId): void{
        $this->preKeyStore->removePreKey($preKeyId);
    }

    public function loadSession(SignalProtocolAddress $address): SessionRecord{
        return $this->sessionStore->loadSession($address);
    }

    public function getSubDeviceSessions(String $name): array{
        return $this->sessionStore->getSubDeviceSessions($name);
    }

    public function storeSession(SignalProtocolAddress $address,SessionRecord $record): void{
        $this->sessionStore->storeSession($address,$record);
    }

    public function containsSession(SignalProtocolAddress $address): bool{
        return $this->sessionStore->containsSession($address);
    }

    public function deleteSession(SignalProtocolAddress $address): void{
        $this->sessionStore->deleteSession($address);
    }

    public function deleteAllSessions(String $name): void{
        $this->sessionStore->deleteAllSessions($name);
    }

    /**
     * @param int $signedPreKeyId
     * @return SignedPreKeyRecord
     * @throws InvalidKeyIdException
     */
    public function loadSignedPreKey(int $signedPreKeyId): SignedPreKeyRecord{
        return $this->signedPreKeyStore->loadSignedPreKey($signedPreKeyId);
    }

    public function loadSignedPreKeys(): array{
        return $this->signedPreKeyStore->loadSignedPreKeys();
    }

    public function storeSignedPreKey(int $signedPreKeyId,SignedPreKeyRecord $record): void{
        $this->signedPreKeyStore->storeSignedPreKey($signedPreKeyId,$record);
    }

    public function containsSignedPreKey(int $signedPreKeyId): bool{
        return $this->signedPreKeyStore->containsSignedPreKey($signedPreKeyId);
    }

    public function removeSignedPreKey(int $signedPreKeyId): void{
        $this->signedPreKeyStore->removeSignedPreKey($signedPreKeyId);
    }

}
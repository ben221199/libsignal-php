<?php
namespace WhisperSystems\LibSignal;

use WhisperSystems\LibSignal\ECC\Curve;
use WhisperSystems\LibSignal\State\Impl\InMemoryIdentityKeyStore;
use WhisperSystems\LibSignal\Util\KeyHelper;

class TestInMemoryIdentityKeyStore extends InMemoryIdentityKeyStore{

    /**
     * TestInMemoryIdentityKeyStore constructor.
     * @throws InvalidKeyException
     */
    public function __construct(){
        parent::__construct(self::generateIdentityKeyPair(),self::generateRegistrationId());
    }

    /**
     * @return IdentityKeyPair
     * @throws InvalidKeyException
     */
    private static function generateIdentityKeyPair(): IdentityKeyPair{
        $identityKeyPairKeys = Curve::generateKeyPair();

        return new IdentityKeyPair(new IdentityKey($identityKeyPairKeys->getPublicKey()),$identityKeyPairKeys->getPrivateKey());
    }

    private static function generateRegistrationId(): int{
        return KeyHelper::generateRegistrationId(false);
    }

}
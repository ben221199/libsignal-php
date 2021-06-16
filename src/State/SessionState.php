<?php
namespace WhisperSystems\LibSignal\State;

use AssertionError;
use Exception;
use RuntimeException;
use WhisperSystems\LibSignal\ECC\Curve;
use WhisperSystems\LibSignal\ECC\DjbECPublicKey;
use WhisperSystems\LibSignal\ECC\ECKeyPair;
use WhisperSystems\LibSignal\ECC\ECPublicKey;
use WhisperSystems\LibSignal\IdentityKey;
use WhisperSystems\LibSignal\IdentityKeyPair;
use WhisperSystems\LibSignal\InvalidKeyException;
use WhisperSystems\LibSignal\KDF\HKDF;
use WhisperSystems\LibSignal\Logging\Log;
use WhisperSystems\LibSignal\Ratchet\ChainKey;
use WhisperSystems\LibSignal\Ratchet\MessageKeys;
use WhisperSystems\LibSignal\Ratchet\RootKey;
use WhisperSystems\LibSignal\State\StorageProtos\SessionStructure;
use WhisperSystems\LibSignal\State\StorageProtos\SessionStructure\Chain;
use WhisperSystems\LibSignal\State\StorageProtos\SessionStructure\PendingKeyExchange;
use WhisperSystems\LibSignal\State\StorageProtos\SessionStructure\PendingPreKey;
use WhisperSystems\LibSignal\Util\Guava\Optional;
use WhisperSystems\LibSignal\Util\Pair;

class SessionState{

    private const MAX_MESSAGE_KEYS = 2000;

    /**
     * @var SessionStructure $sessionStructure
     */
    private $sessionStructure;

    /**
     * @param SessionStructure|SessionState $sessionStructureOrCopy
     */
    public function __construct($sessionStructureOrCopy=null){
        if($sessionStructureOrCopy===null){
            $this->sessionStructure = new SessionStructure;
        }elseif($sessionStructureOrCopy instanceof SessionStructure){
            $this->sessionStructure = $sessionStructureOrCopy;
        }elseif($sessionStructureOrCopy instanceof SessionState){
            $this->sessionStructure = new SessionStructure;
            $this->sessionStructure->mergeFrom($sessionStructureOrCopy->sessionStructure);
        }else{
            throw new RuntimeException('Invalid constructor call');
        }
    }

    public function getStructure(): SessionStructure{
        return $this->sessionStructure;
    }

    public function getAliceBaseKey(): string{
        return $this->sessionStructure->getAliceBaseKey();
    }

    public function setAliceBaseKey(string $aliceBaseKey): void{
        $this->sessionStructure = $this->sessionStructure->setAliceBaseKey($aliceBaseKey);
    }

    public function setSessionVersion(int $version): void{
        $this->sessionStructure = $this->sessionStructure->setSessionVersion($version);
    }

    public function getSessionVersion(): int{
        $sessionVersion = $this->sessionStructure->getSessionVersion();

        if($sessionVersion===0){
            return 2;
        }else{
            return $sessionVersion;
        }
    }

    public function setRemoteIdentityKey(IdentityKey $identityKey): void{
        $this->sessionStructure = $this->sessionStructure->setRemoteIdentityPublic($identityKey->serialize());
    }

    public function setLocalIdentityKey(IdentityKey $identityKey): void{
        $this->sessionStructure = $this->sessionStructure->setLocalIdentityPublic($identityKey->serialize());
    }

    public function getRemoteIdentityKey(): IdentityKey{
        try{
            if(!$this->sessionStructure->hasRemoteIdentityPublic()){
                return null;
            }

            return new IdentityKey($this->sessionStructure->getRemoteIdentityPublic(),0);
        }catch(InvalidKeyException $e){
            Log::w("SessionRecordV2",$e);
            return null;
        }
    }

    public function getLocalIdentityKey(): IdentityKey{
        try{
            return new IdentityKey($this->sessionStructure->getLocalIdentityPublic(),0);
        }catch(InvalidKeyException $e){
            throw new AssertionError($e);
        }
    }

    public function getPreviousCounter(): int{
        return $this->sessionStructure->getPreviousCounter();
    }

    public function setPreviousCounter(int $previousCounter): void{
        $this->sessionStructure = $this->sessionStructure->setPreviousCounter($previousCounter);
    }

    public function getRootKey(): RootKey{
        return new RootKey(HKDF::createFor($this->getSessionVersion()),$this->sessionStructure->getRootKey());
    }

    public function setRootKey(RootKey $rootKey): void{
        $this->sessionStructure = $this->sessionStructure->setRootKey($rootKey->getKeyBytes());
    }

    public function getSenderRatchetKey(): ECPublicKey{
        try{
            return Curve::decodePoint($this->sessionStructure->getSenderChain()->getSenderRatchetKey(),0);
        }catch(InvalidKeyException $e){
            throw new AssertionError($e);
        }
    }

    public function getSenderRatchetKeyPair(): ECKeyPair{
        $publicKey = $this->getSenderRatchetKey();
        $privateKey = Curve::decodePrivatePoint($this->sessionStructure->getSenderChain()->getSenderRatchetKeyPrivate());

        return new ECKeyPair($publicKey,$privateKey);
    }

    public function hasReceiverChain(ECPublicKey $senderEphemeral): bool{
        return $this->getReceiverChain($senderEphemeral)!==null;
    }

    public function hasSenderChain(): bool{
        return $this->sessionStructure->hasSenderChain();
    }

    private function getReceiverChain(ECPublicKey $senderEphemeral): Pair{
        $receiverChains = $this->sessionStructure->getReceiverChains();
        $index = 0;

        /**
         * @var Chain $receiverChain
         */
        foreach($receiverChains AS $receiverChain){
            try {
                $chainSenderRatchetKey = Curve::decodePoint($receiverChain->getSenderRatchetKey(),0);

                /**@var DjbECPublicKey $chainSenderRatchetKey*/
                if ($chainSenderRatchetKey->equals($senderEphemeral)){
                    return new Pair($receiverChain,$index);
                }
            }catch(InvalidKeyException $e){
                Log::w("SessionRecordV2",$e);
            }

            $index++;
        }

        return null;
    }

    public function getReceiverChainKey(ECPublicKey $senderEphemeral): ChainKey{
        $receiverChainAndIndex = $this->getReceiverChain($senderEphemeral);
        /**@var Chain $receiverChain*/
        $receiverChain = $receiverChainAndIndex->first();

        if($receiverChain===null){
            return null;
        } else {
            return new ChainKey(HKDF::createFor($this->getSessionVersion()),$receiverChain->getChainKey()->getKey(),$receiverChain->getChainKey()->getIndex());
        }
    }

    public function addReceiverChain(ECPublicKey $senderRatchetKey,ChainKey $chainKey): void{
        $chainKeyStructure = (new Chain\ChainKey)
            ->setKey($chainKey->getKey())
            ->setIndex($chainKey->getIndex());

        $chain = (new Chain)
            ->setChainKey($chainKeyStructure)
            ->setSenderRatchetKey($senderRatchetKey->serialize());

        $receiverChains = [];
        foreach($this->sessionStructure->getReceiverChains() AS $receiverChain){
            $receiverChains[] = $receiverChain;
        }
        $this->sessionStructure->setReceiverChains(array_merge($receiverChains,[$chain]));

        if($this->sessionStructure->getReceiverChains()->count()>5){
            $receiverChains2 = $this->sessionStructure->getReceiverChains();
            $receiverChains3 = [];
            for($i=1;$i<$receiverChains2->count();$i++){
                $receiverChains3[] = $receiverChains2[$i];
            }
            $this->sessionStructure = $this->sessionStructure->setReceiverChains($receiverChains3);
        }
    }

    public function setSenderChain(ECKeyPair $senderRatchetKeyPair, ChainKey $chainKey): void{
        $chainKeyStructure = (new Chain\ChainKey)
            ->setKey($chainKey->getKey())
            ->setIndex($chainKey->getIndex());

        $senderChain = (new Chain)
            ->setSenderRatchetKey($senderRatchetKeyPair->getPublicKey()->serialize())
            ->setSenderRatchetKeyPrivate($senderRatchetKeyPair->getPrivateKey()->serialize())
            ->setChainKey($chainKeyStructure);

        $this->sessionStructure = $this->sessionStructure->setSenderChain($senderChain);
    }

    public function getSenderChainKey(): ChainKey{
        $chainKeyStructure = $this->sessionStructure->getSenderChain()->getChainKey();
        return new ChainKey(HKDF::createFor($this->getSessionVersion()),$chainKeyStructure->getKey(),$chainKeyStructure->getIndex());
    }


    public function setSenderChainKey(ChainKey $nextChainKey): void{
        $chainKey = (new Chain\ChainKey)
            ->setKey($nextChainKey->getKey())
            ->setIndex($nextChainKey->getIndex());

        $chain = $this->sessionStructure->getSenderChain()->setChainKey($chainKey);

        $this->sessionStructure = $this->sessionStructure->setSenderChain($chain);
    }

    public function hasMessageKeys(ECPublicKey $senderEphemeral,int $counter): bool{
        $chainAndIndex = $this->getReceiverChain($senderEphemeral);
        /**@var Chain $chain*/
        $chain = $chainAndIndex->first();

        if($chain===null){
            return false;
        }

        $messageKeyList = $chain->getMessageKeys();

        foreach($messageKeyList AS $messageKey) {
            if($messageKey->getIndex()===$counter){
                return true;
            }
        }

        return false;
    }

    public function removeMessageKeys(ECPublicKey $senderEphemeral,int $counter): MessageKeys{
        $chainAndIndex = $this->getReceiverChain($senderEphemeral);
        /**@var Chain $chain*/
        $chain = $chainAndIndex->first();

        if($chain===null){
            return null;
        }

        $messageKeyList = $chain->getMessageKeys();
        $messageKeyIterator = $messageKeyList->getIterator();
        $result = null;

        while($messageKeyIterator->valid()){
            $messageKeyIterator->next();
            $messageKey = $messageKeyIterator->current();

            if($messageKey->getIndex()===$counter){
                $result = new MessageKeys($messageKey->getCipherKey(),$messageKey->getMacKey(),$messageKey->getIv(),$messageKey->getIndex());

                unset($messageKeyList[$messageKeyIterator->key()]);
                break;
            }
        }

        $updatedChain = $chain->setMessageKeys([])->setMessageKeys($messageKeyList);

        $this->sessionStructure = $this->sessionStructure->setReceiverChains([$chainAndIndex->second(),$updatedChain]);

        return $result;
    }

    public function setMessageKeys(ECPublicKey $senderEphemeral,MessageKeys $messageKeys): void{
        $chainAndIndex = $this->getReceiverChain($senderEphemeral);
        /**@var Chain $chain*/
        $chain = $chainAndIndex->first();
        $messageKeyStructure = (new Chain\MessageKey)
            ->setCipherKey($messageKeys->getCipherKey())
            ->setMacKey($messageKeys->getMacKey())
            ->setIndex($messageKeys->getCounter())
            ->setIv($messageKeys->getIv());

        $updatedChain = (new Chain);
        $updatedChain->mergeFrom($chain);
        $messageKeys2 = [];
        foreach($chain->getMessageKeys() AS $messageKey){
            $messageKeys2[] = $messageKey;
        }
        $chain->setMessageKeys(array_merge($messageKeys2,[$messageKeyStructure]));

        if($updatedChain->getMessageKeys()->count() > self::MAX_MESSAGE_KEYS){
            $messageKeys3 = $updatedChain->getMessageKeys();
            $messageKeys4 = [];
            for($i=1;$i<$messageKeys3->count();$i++){
                $messageKeys4 = $messageKeys3[$i];
            }
            $updatedChain->setMessageKeys($messageKeys4);
        }

        $this->sessionStructure = $this->sessionStructure->setReceiverChains([$chainAndIndex->second(),$updatedChain]);
    }

    public function setReceiverChainKey(ECPublicKey $senderEphemeral,ChainKey $chainKey): void{
        $chainAndIndex = $this->getReceiverChain($senderEphemeral);
        /**@var Chain $chain*/
        $chain = $chainAndIndex->first();

        $chainKeyStructure = (new Chain\ChainKey)
            ->setKey($chainKey->getKey())
            ->setIndex($chainKey->getIndex());

        $updatedChain = (new Chain);
        $updatedChain->mergeFrom($chain);
        $chain->setChainKey($chainKeyStructure);

        $this->sessionStructure = $this->sessionStructure->setReceiverChains([$chainAndIndex->second(),$updatedChain]);
    }

    public function setPendingKeyExchange(int $sequence,ECKeyPair $ourBaseKey,ECKeyPair $ourRatchetKey,IdentityKeyPair $ourIdentityKey): void{
        $structure = (new PendingKeyExchange)
            ->setSequence($sequence)
            ->setLocalBaseKey($ourBaseKey->getPublicKey()->serialize())
            ->setLocalBaseKeyPrivate($ourBaseKey->getPrivateKey()->serialize())
            ->setLocalRatchetKey($ourRatchetKey->getPublicKey()->serialize())
            ->setLocalRatchetKeyPrivate($ourRatchetKey->getPrivateKey()->serialize())
            ->setLocalIdentityKey($ourIdentityKey->getPublicKey()->serialize())
            ->setLocalIdentityKeyPrivate($ourIdentityKey->getPrivateKey()->serialize());

        $this->sessionStructure = $this->sessionStructure->setPendingKeyExchange($structure);
    }

    public function getPendingKeyExchangeSequence(): int{
        return $this->sessionStructure->getPendingKeyExchange()->getSequence();
    }

    /**
     * @return ECKeyPair
     * @throws InvalidKeyException
     */
    public function getPendingKeyExchangeBaseKey(): ECKeyPair{
        $publicKey = Curve::decodePoint($this->sessionStructure->getPendingKeyExchange()->getLocalBaseKey(),0);

        $privateKey = Curve::decodePrivatePoint($this->sessionStructure->getPendingKeyExchange()->getLocalBaseKeyPrivate());

        return new ECKeyPair($publicKey,$privateKey);
    }

    /**
     * @return ECKeyPair
     * @throws InvalidKeyException
     */
    public function getPendingKeyExchangeRatchetKey(): ECKeyPair{
        $publicKey = Curve::decodePoint($this->sessionStructure->getPendingKeyExchange()->getLocalRatchetKey(),0);

        $privateKey = Curve::decodePrivatePoint($this->sessionStructure->getPendingKeyExchange()->getLocalRatchetKeyPrivate());

        return new ECKeyPair($publicKey,$privateKey);
    }

    /**
     * @return IdentityKeyPair
     * @throws InvalidKeyException
     */
    public function getPendingKeyExchangeIdentityKey(): IdentityKeyPair{
        $publicKey = new IdentityKey($this->sessionStructure->getPendingKeyExchange()->getLocalIdentityKey(),0);

        $privateKey = Curve::decodePrivatePoint($this->sessionStructure->getPendingKeyExchange()->getLocalIdentityKeyPrivate());

        return new IdentityKeyPair($publicKey,$privateKey);
    }

    public function hasPendingKeyExchange(): bool{
        return $this->sessionStructure->hasPendingKeyExchange();
    }

    public function setUnacknowledgedPreKeyMessage(Optional $preKeyId,int $signedPreKeyId,ECPublicKey $baseKey): void{
        $pending = (new PendingPreKey)
            ->setSignedPreKeyId($signedPreKeyId)
            ->setBaseKey($baseKey->serialize());

        if($preKeyId->isPresent()){
            $pending->setPreKeyId($preKeyId->get());
        }

        $this->sessionStructure = $this->sessionStructure->setPendingPreKey($pending);
    }

    public function hasUnacknowledgedPreKeyMessage(): bool{
        return $this->sessionStructure->hasPendingPreKey();
    }

    public function getUnacknowledgedPreKeyMessageItems(): SessionState_UnacknowledgedPreKeyMessageItems{
        try{
            $preKeyId = null;

            if($this->sessionStructure->getPendingPreKey()->hasPreKeyId()){
                try{
                    $preKeyId = Optional::of($this->sessionStructure->getPendingPreKey()->getPreKeyId());
                }catch(Exception $e){
                }
            }else{
                $preKeyId = Optional::absent();
            }

            return new SessionState_UnacknowledgedPreKeyMessageItems($preKeyId,$this->sessionStructure->getPendingPreKey()->getSignedPreKeyId(),Curve::decodePoint($this->sessionStructure->getPendingPreKey()->getBaseKey(),0));
        }catch(InvalidKeyException $e){
            throw new AssertionError($e);
        }
    }

    public function clearUnacknowledgedPreKeyMessage(): void{
        $this->sessionStructure = $this->sessionStructure->clearPendingPreKey();
    }

    public function setRemoteRegistrationId(int $registrationId): void{
        $this->sessionStructure = $this->sessionStructure->setRemoteRegistrationId($registrationId);
    }

    public function getRemoteRegistrationId(): int{
        return $this->sessionStructure->getRemoteRegistrationId();
    }

    public function setLocalRegistrationId(int $registrationId): void{
        $this->sessionStructure = $this->sessionStructure->setLocalRegistrationId($registrationId);
    }

    public function getLocalRegistrationId(): int{
        return $this->sessionStructure->getLocalRegistrationId();
    }

    public function serialize(): string{
        return $this->sessionStructure->serializeToString();
    }

}
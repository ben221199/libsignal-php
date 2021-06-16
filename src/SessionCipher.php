<?php
namespace WhisperSystems\LibSignal;

use AssertionError;
use Exception;
use WhisperSystems\LibSignal\ECC\Curve;
use WhisperSystems\LibSignal\ECC\ECPublicKey;
use WhisperSystems\LibSignal\Protocol\CiphertextMessage;
use WhisperSystems\LibSignal\Protocol\PreKeySignalMessage;
use WhisperSystems\LibSignal\Protocol\SignalMessage;
use WhisperSystems\LibSignal\Ratchet\ChainKey;
use WhisperSystems\LibSignal\Ratchet\MessageKeys;
use WhisperSystems\LibSignal\State\IdentityKeyStore;
use WhisperSystems\LibSignal\State\IdentityKeyStore_Direction;
use WhisperSystems\LibSignal\State\PreKeyStore;
use WhisperSystems\LibSignal\State\SessionState;
use WhisperSystems\LibSignal\State\SessionStore;
use WhisperSystems\LibSignal\State\SignalProtocolStore;
use WhisperSystems\LibSignal\State\SignedPreKeyStore;

class SessionCipher{

    /**
     * @var SessionStore $sessionStore
     */
    private $sessionStore;
    /**
     * @var IdentityKeyStore $identityKeyStore
     */
    private $identityKeyStore;
    /**
     * @var SessionBuilder $sessionBuilder
     */
    private $sessionBuilder;
    /**
     * @var PreKeyStore $preKeyStore
     */
    private $preKeyStore;
    /**
     * @var SignalProtocolAddress $remoteAddress
     */
    private $remoteAddress;

    /**
     * SessionBuilder constructor.
     * @param SignalProtocolStore|SessionStore $storeOrSessionStore
     * @param SignalProtocolAddress|PreKeyStore $remoteAddressOrPreKeyStore
     * @param SignedPreKeyStore|null $signedPreKeyStoreOrNull
     * @param IdentityKeyStore|null $identityKeyStoreOrNull
     * @param SignalProtocolAddress|null $remoteAddressOrNull
     */
    public function __construct($storeOrSessionStore,$remoteAddressOrPreKeyStore,$signedPreKeyStoreOrNull=null,$identityKeyStoreOrNull=null,$remoteAddressOrNull=null){
        if($storeOrSessionStore instanceof SignalProtocolStore && $remoteAddressOrPreKeyStore instanceof SignalProtocolAddress){
            self::__construct($storeOrSessionStore,$storeOrSessionStore,$storeOrSessionStore,$storeOrSessionStore,$remoteAddressOrPreKeyStore);
        }elseif($storeOrSessionStore instanceof SessionStore && $remoteAddressOrPreKeyStore instanceof PreKeyStore && $signedPreKeyStoreOrNull instanceof SignedPreKeyStore && $identityKeyStoreOrNull instanceof IdentityKeyStore && $remoteAddressOrNull instanceof SignalProtocolAddress){
            $this->sessionStore = $storeOrSessionStore;
            $this->preKeyStore = $remoteAddressOrPreKeyStore;
            $this->identityKeyStore = $identityKeyStoreOrNull;
            $this->remoteAddress = $remoteAddressOrNull;
            $this->sessionBuilder = new SessionBuilder($storeOrSessionStore,$remoteAddressOrPreKeyStore,$signedPreKeyStoreOrNull,$identityKeyStoreOrNull,$remoteAddressOrNull);
        }else{
            throw new RuntimeException('Invalid constructor call');
        }
    }

    /**
     * @param string $paddedMessage
     * @return CiphertextMessage
     * @throws UntrustedIdentityException
     * @throws InvalidVersionException
     */
    public function encrypt(string $paddedMessage) : CiphertextMessage{
        $sessionRecord = $this->sessionStore->loadSession($this->remoteAddress);
        $sessionState = $sessionRecord->getSessionState();
        $chainKey = $sessionState->getSenderChainKey();
        $messageKeys = $chainKey->getMessageKeys();
        $senderEphemeral = $sessionState->getSenderRatchetKey();
        $previousCounter = $sessionState->getPreviousCounter();
        $sessionVersion  = $sessionState->getSessionVersion();

        $ciphertextBody = self::getCiphertext($messageKeys,$paddedMessage);
        $ciphertextMessage = new SignalMessage($sessionVersion,$messageKeys->getMacKey(),
            $senderEphemeral,$chainKey->getIndex(),
            $previousCounter,$ciphertextBody,
            $sessionState->getLocalIdentityKey(),
            $sessionState->getRemoteIdentityKey());

        if($sessionState->hasUnacknowledgedPreKeyMessage()){
            $items = $sessionState->getUnacknowledgedPreKeyMessageItems();
            $localRegistrationId = $sessionState->getLocalRegistrationId();

            $ciphertextMessage = new PreKeySignalMessage($sessionVersion,$localRegistrationId,$items->getPreKeyId(),
                $items->getSignedPreKeyId(),$items->getBaseKey(),
                $sessionState->getLocalIdentityKey(),
                $ciphertextMessage);
        }

        $sessionState->setSenderChainKey($chainKey->getNextChainKey());

        if(!$this->identityKeyStore->isTrustedIdentity($this->remoteAddress,$sessionState->getRemoteIdentityKey(),IdentityKeyStore_Direction::SENDING())){
            throw new UntrustedIdentityException($this->remoteAddress->getName(),$sessionState->getRemoteIdentityKey());
        }

        $this->identityKeyStore->saveIdentity($this->remoteAddress,$sessionState->getRemoteIdentityKey());
        $this->sessionStore->storeSession($this->remoteAddress,$sessionRecord);

        return $ciphertextMessage;
    }

    public function getRemoteRegistrationId(): int{
        $record = $this->sessionStore->loadSession($this->remoteAddress);
        return $record->getSessionState()->getRemoteRegistrationId();
    }

    /**
     * @return int
     * @throws Exception
     */
    public function getSessionVersion(): int{
        if(!$this->sessionStore->containsSession($this->remoteAddress)){
            throw new Exception(sprintf('No session for (%s)!',$this->remoteAddress));
        }

        $record = $this->sessionStore->loadSession($this->remoteAddress);
        return $record->getSessionState()->getSessionVersion();
    }

    /**
     * @param SessionState $sessionState
     * @param ECPublicKey $theirEphemeral
     * @return ChainKey
     * @throws InvalidMessageException
     */
    private function getOrCreateChainKey(SessionState $sessionState,ECPublicKey $theirEphemeral): ChainKey{
        try{
            if($sessionState->hasReceiverChain($theirEphemeral)){
                return $sessionState->getReceiverChainKey($theirEphemeral);
            }else{
                $rootKey = $sessionState->getRootKey();
                $ourEphemeral = $sessionState->getSenderRatchetKeyPair();
                $receiverChain = $rootKey->createChain($theirEphemeral,$ourEphemeral);
                $ourNewEphemeral = Curve::generateKeyPair();
                $senderChain = $receiverChain->first()->createChain($theirEphemeral,$ourNewEphemeral);

                $sessionState->setRootKey($senderChain->first());
                $sessionState->addReceiverChain($theirEphemeral,$receiverChain->second());
                $sessionState->setPreviousCounter(max($sessionState->getSenderChainKey()->getIndex()-1,0));
                $sessionState->setSenderChain($ourNewEphemeral,$senderChain->second());

                return $receiverChain->second();
            }
        }catch(InvalidKeyException $e){
            throw new InvalidMessageException($e);
        }
    }

    /**
     * @param SessionState $sessionState
     * @param ECPublicKey $theirEphemeral
     * @param ChainKey $chainKey
     * @param int $counter
     * @return MessageKeys
     * @throws DuplicateMessageException
     * @throws InvalidMessageException
     */
    private function getOrCreateMessageKeys(SessionState $sessionState,ECPublicKey $theirEphemeral,ChainKey $chainKey,int $counter): MessageKeys{
        if($chainKey->getIndex() > $counter){
            if($sessionState->hasMessageKeys($theirEphemeral,$counter)){
                return $sessionState->removeMessageKeys($theirEphemeral,$counter);
            }else{
                throw new DuplicateMessageException("Received message with old counter: " . $chainKey->getIndex() . " , " . $counter);
            }
        }

        if ($counter - $chainKey->getIndex() > 2000) {
            throw new InvalidMessageException("Over 2000 messages into the future!");
        }

        while($chainKey->getIndex() < $counter){
            $messageKeys = $chainKey->getMessageKeys();
            $sessionState->setMessageKeys($theirEphemeral,$messageKeys);
            $chainKey = $chainKey->getNextChainKey();
        }

        $sessionState->setReceiverChainKey($theirEphemeral,$chainKey->getNextChainKey());
        return $chainKey->getMessageKeys();
    }

    private function getCiphertext(MessageKeys $messageKeys,string $plaintext): string{
        try{
            return 'CIPHER';
//            $cipher = $this->getCipher(/*Cipher::ENCRYPT_MODE*/1,$messageKeys->getCipherKey(),$messageKeys->getIv());
//            return $cipher->doFinal($plaintext);
        }catch(Exception $e){
            throw new AssertionError($e);
        }
    }

    /**
     * @param MessageKeys $messageKeys
     * @param string $cipherText
     * @return string
     * @throws InvalidMessageException
     */
    private function getPlaintext(MessageKeys $messageKeys,string $cipherText): string{
        try{
            return 'PLAIN';
//            $cipher = $this->getCipher(/*Cipher::DECRYPT_MODE*/2,$messageKeys->getCipherKey(),$messageKeys->getIv());
//            return $cipher->doFinal($cipherText);
        }catch(Exception $e){
            throw new InvalidMessageException($e);
        }
    }

  private function getCipher(int $mode,string $key,string $iv): Cipher{
//    try{
//        Cipher cipher = Cipher.getInstance("AES/CBC/PKCS5Padding");
//      cipher.init(mode, key, iv);
//      return cipher;
//    } catch (NoSuchAlgorithmException | NoSuchPaddingException | java.security.InvalidKeyException |
//    InvalidAlgorithmParameterException e)
//    {
//        throw new AssertionError(e);
//    }
  }

}
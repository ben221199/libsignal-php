<?php
namespace WhisperSystems\LibSignal;

use Exception;
use WhisperSystems\LibSignal\ECC\Curve;
use WhisperSystems\LibSignal\Logging\Log;
use WhisperSystems\LibSignal\Protocol\PreKeySignalMessage;
use WhisperSystems\LibSignal\Ratchet\AliceSignalProtocolParameters;
use WhisperSystems\LibSignal\Ratchet\BobSignalProtocolParameters;
use WhisperSystems\LibSignal\Ratchet\RatchetingSession;
use WhisperSystems\LibSignal\State\IdentityKeyStore;
use WhisperSystems\LibSignal\State\IdentityKeyStore_Direction;
use WhisperSystems\LibSignal\State\PreKeyBundle;
use WhisperSystems\LibSignal\State\PreKeyStore;
use WhisperSystems\LibSignal\State\SessionRecord;
use WhisperSystems\LibSignal\State\SessionStore;
use WhisperSystems\LibSignal\State\SignalProtocolStore;
use WhisperSystems\LibSignal\State\SignedPreKeyStore;
use WhisperSystems\LibSignal\Util\Guava\Optional;

class SessionBuilder{

    private const TAG = 'SessionBuilder';

    /**
     * @var SessionStore $sessionStore
     */
    private $sessionStore;
    /**
     * @var PreKeyStore $preKeyStore
     */
    private $preKeyStore;
    /**
     * @var SignedPreKeyStore $signedPreKeyStore
     */
    private $signedPreKeyStore;
    /**
     * @var IdentityKeyStore $identityKeyStore
     */
    private $identityKeyStore;
    /**
     * @var SignalProtocolAddress $remoteAddress
     */
    private $remoteAddress;

    /**
     * SessionBuilder constructor.
     * @param SignalProtocolStore|SignalProtocolAddress $storeOrSessionStore
     * @param SignalProtocolStore|SignalProtocolAddress $remoteAddressOrPreKeyStore
     * @param SignalProtocolStore|null $signedPreKeyStoreOrNull
     * @param SignalProtocolStore|null $identityKeyStoreOrNull
     * @param SignalProtocolAddress|null $remoteAddressOrNull
     */
    public function __construct($storeOrSessionStore,$remoteAddressOrPreKeyStore,$signedPreKeyStoreOrNull=null,$identityKeyStoreOrNull=null,$remoteAddressOrNull=null){
        if($storeOrSessionStore instanceof SignalProtocolStore && $remoteAddressOrPreKeyStore instanceof SignalProtocolAddress){
            self::__construct($storeOrSessionStore,$storeOrSessionStore,$storeOrSessionStore,$storeOrSessionStore,$remoteAddressOrPreKeyStore);
        }elseif($storeOrSessionStore instanceof SessionStore && $remoteAddressOrPreKeyStore instanceof PreKeyStore && $signedPreKeyStoreOrNull instanceof SignedPreKeyStore && $identityKeyStoreOrNull instanceof IdentityKeyStore && $remoteAddressOrNull instanceof SignalProtocolAddress){
            $this->sessionStore = $storeOrSessionStore;
            $this->preKeyStore = $remoteAddressOrPreKeyStore;
            $this->signedPreKeyStore = $signedPreKeyStoreOrNull;
            $this->identityKeyStore = $identityKeyStoreOrNull;
            $this->remoteAddress= $remoteAddressOrNull;
        }else{
            throw new RuntimeException('Invalid constructor call');
        }
    }

    /**
     * @param SessionRecord $sessionRecord
     * @param PreKeySignalMessage $message
     * @return Optional
     * @throws UntrustedIdentityException
     * @throws InvalidKeyIdException
     */
    function process0(SessionRecord $sessionRecord,PreKeySignalMessage $message): Optional{
        $theirIdentityKey = new IdentityKey($message->getIdentityKey());

        if(!$this->identityKeyStore->isTrustedIdentity($this->remoteAddress,$theirIdentityKey,IdentityKeyStore_Direction::RECEIVING())){
            throw new UntrustedIdentityException($this->remoteAddress->getName(),$theirIdentityKey);
        }

        $unsignedPreKeyId = $this->processV3($sessionRecord,$message);

        $this->identityKeyStore->saveIdentity($this->remoteAddress,$theirIdentityKey);

        return $unsignedPreKeyId;
    }

    /**
     * @param SessionRecord $sessionRecord
     * @param PreKeySignalMessage $message
     * @return Optional
     * @throws Exception
     * @throws InvalidKeyIdException
     */
    private function processV3(SessionRecord $sessionRecord,PreKeySignalMessage $message): Optional{
        if($sessionRecord->hasSessionState($message->getMessageVersion(),$message->getBaseKey()->serialize())){
            Log::w(self::TAG, "We've already setup a session for this V3 message, letting bundled message fall through...");
            return Optional::absent();
        }

        $ourSignedPreKey = $this->signedPreKeyStore->loadSignedPreKey($message->getSignedPreKeyId())->getKeyPair();

        $parameters = BobSignalProtocolParameters::newBuilder();

        $parameters->setTheirBaseKey($message->getBaseKey())
            ->setTheirIdentityKey($message->getIdentityKey())
            ->setOurIdentityKey($this->identityKeyStore->getIdentityKeyPair())
            ->setOurSignedPreKey($ourSignedPreKey)
            ->setOurRatchetKey($ourSignedPreKey);

        if($message->getPreKeyId()->isPresent()){
            $parameters->setOurOneTimePreKey(Optional::of($this->preKeyStore->loadPreKey($message->getPreKeyId()->get())->getKeyPair()));
        } else {
            $parameters->setOurOneTimePreKey(Optional::absent());
        }

        if(!$sessionRecord->isFresh()){
            $sessionRecord->archiveCurrentState();
        };

        RatchetingSession::initializeSession($sessionRecord->getSessionState(),$parameters->create());

        $sessionRecord->getSessionState()->setLocalRegistrationId($this->identityKeyStore->getLocalRegistrationId());
        $sessionRecord->getSessionState()->setRemoteRegistrationId($message->getRegistrationId());
        $sessionRecord->getSessionState()->setAliceBaseKey($message->getBaseKey()->serialize());

        if($message->getPreKeyId()->isPresent()){
            return $message->getPreKeyId();
        }else{
            return Optional::absent();
        }
    }

    /**
     * @param PreKeyBundle $preKey
     * @throws Exception
     * @throws InvalidKeyException
     * @throws UntrustedIdentityException
     */
  public function process(PreKeyBundle $preKey): void{
      if(!$this->identityKeyStore->isTrustedIdentity($this->remoteAddress, $preKey->getIdentityKey(),IdentityKeyStore_Direction::SENDING())){
          throw new UntrustedIdentityException($this->remoteAddress->getName(), $preKey->getIdentityKey());
      }

      if($preKey->getSignedPreKey()!==null && !Curve::verifySignature($preKey->getIdentityKey()->getPublicKey(),$preKey->getSignedPreKey()->serialize(),$preKey->getSignedPreKeySignature())){
          throw new InvalidKeyException("Invalid signature on device key!");
      }

      if($preKey->getSignedPreKey()===null){
          throw new InvalidKeyException("No signed prekey!");
      }

      $sessionRecord = $this->sessionStore->loadSession($this->remoteAddress);
      $ourBaseKey = Curve::generateKeyPair();
      $theirSignedPreKey = $preKey->getSignedPreKey();
      $theirOneTimePreKey = Optional::fromNullable($preKey->getPreKey());
      $theirOneTimePreKeyId = $theirOneTimePreKey->isPresent()?Optional::of($preKey->getPreKeyId()):Optional::absent();

      $parameters = AliceSignalProtocolParameters::newBuilder();

      $parameters->setOurBaseKey($ourBaseKey)
          ->setOurIdentityKey($this->identityKeyStore->getIdentityKeyPair())
          ->setTheirIdentityKey($preKey->getIdentityKey())
          ->setTheirSignedPreKey($theirSignedPreKey)
          ->setTheirRatchetKey($theirSignedPreKey)
          ->setTheirOneTimePreKey($theirOneTimePreKey);

      if (!$sessionRecord->isFresh()){
          $sessionRecord->archiveCurrentState();
      }

      RatchetingSession::initializeSession($sessionRecord->getSessionState(),$parameters->create());

      $sessionRecord->getSessionState()->setUnacknowledgedPreKeyMessage($theirOneTimePreKeyId, $preKey->getSignedPreKeyId(),$ourBaseKey->getPublicKey());
      $sessionRecord->getSessionState()->setLocalRegistrationId($this->identityKeyStore->getLocalRegistrationId());
      $sessionRecord->getSessionState()->setRemoteRegistrationId($preKey->getRegistrationId());
      $sessionRecord->getSessionState()->setAliceBaseKey($ourBaseKey->getPublicKey()->serialize());

      $this->identityKeyStore->saveIdentity($this->remoteAddress,$preKey->getIdentityKey());
      $this->sessionStore->storeSession($this->remoteAddress,$sessionRecord);
  }

}
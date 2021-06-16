<?php
namespace WhisperSystems\LibSignal\Protocol;

use Exception;
use WhisperSystems\LibSignal\ECC\Curve;
use WhisperSystems\LibSignal\ECC\ECPublicKey;
use WhisperSystems\LibSignal\IdentityKey;
use WhisperSystems\LibSignal\InvalidKeyException;
use WhisperSystems\LibSignal\InvalidMessageException;
use WhisperSystems\LibSignal\InvalidVersionException;
use WhisperSystems\LibSignal\LegacyMessageException;
use WhisperSystems\LibSignal\Util\ByteUtil;
use WhisperSystems\LibSignal\Util\Guava\Optional;

class PreKeySignalMessage implements CiphertextMessage{

    /**
     * @var int $version
     */
    private $version;
    /**
     * @var int $registrationId
     */
    private $registrationId;
    /**
     * @var Optional $preKeyId
     */
    private $preKeyId;
    /**
     * @var int $signedPreKeyId
     */
    private $signedPreKeyId;
    /**
     * @var ECPublicKey $baseKey
     */
    private $baseKey;
    /**
     * @var IdentityKey $identityKey
     */
    private $identityKey;
    /**
     * @var SignalMessage $message
     */
    private $message;
    /**
     * @var string $serialized
     */
    private $serialized;

    /**
     * PreKeySignalMessage constructor.
     * @param string|int $serializedOrmessageVersion
     * @param int|null $registrationIdOrNull
     * @param Optional|null $preKeyIdOrNull
     * @param int|null $signedPreKeyIdOrNull
     * @param ECPublicKey|null $baseKeyOrNull
     * @param IdentityKey|null $identityKeyOrNull
     * @param SignalMessage|null $messageOrNull
     * @throws Exception
     * @throws InvalidVersionException
     */
    public function __construct($serializedOrmessageVersion,$registrationIdOrNull=null,$preKeyIdOrNull=null,$signedPreKeyIdOrNull=null,$baseKeyOrNull=null,$identityKeyOrNull=null,$messageOrNull=null){
        if(is_string($serializedOrmessageVersion) && $registrationIdOrNull===null && $preKeyIdOrNull===null && $signedPreKeyIdOrNull===null && $baseKeyOrNull===null && $identityKeyOrNull===null && $messageOrNull===null){
            try{
                $this->version = ByteUtil::highBitsToInt($serializedOrmessageVersion[0]);

                if($this->version > CiphertextMessage::CURRENT_VERSION){
                    throw new InvalidVersionException("Unknown version: " . $this->version);
                }

                if($this->version < CiphertextMessage::CURRENT_VERSION){
                    throw new LegacyMessageException("Legacy version: " . $this->version);
                }

                $preKeyWhisperMessage = new SignalProtos\PreKeySignalMessage;
                $preKeyWhisperMessage->mergeFromString(substr($serializedOrmessageVersion,1,strlen($serializedOrmessageVersion)-1));

                if(!$preKeyWhisperMessage->hasSignedPreKeyId() || !$preKeyWhisperMessage->hasBaseKey() || !$preKeyWhisperMessage->hasIdentityKey() || !$preKeyWhisperMessage->hasMessage()){
                    throw new InvalidMessageException("Incomplete message.");
                }

                $this->serialized = $serializedOrmessageVersion;
                $this->registrationId = $preKeyWhisperMessage->getRegistrationId();
                $this->preKeyId       = $preKeyWhisperMessage->hasPreKeyId()?Optional::of($preKeyWhisperMessage->getPreKeyId()):Optional::absent();
                $this->signedPreKeyId = $preKeyWhisperMessage->hasSignedPreKeyId()?$preKeyWhisperMessage->getSignedPreKeyId() : -1;
                $this->baseKey        = Curve::decodePoint($preKeyWhisperMessage->getBaseKey(),0);
                $this->identityKey    = new IdentityKey(Curve::decodePoint($preKeyWhisperMessage->getIdentityKey(),0));
                $this->message        = new SignalMessage($preKeyWhisperMessage->getMessage());
            }catch(InvalidKeyException|LegacyMessageException $e){
                throw new InvalidMessageException($e);
            }
        }elseif(is_int($serializedOrmessageVersion) && is_int($registrationIdOrNull) && $preKeyIdOrNull instanceof Optional && is_int($signedPreKeyIdOrNull) && $baseKeyOrNull instanceof ECPublicKey && $identityKeyOrNull instanceof IdentityKey && $messageOrNull instanceof SignalMessage){
            $this->version = $serializedOrmessageVersion;
            $this->registrationId = $registrationIdOrNull;
            $this->preKeyId = $preKeyIdOrNull;
            $this->signedPreKeyId = $signedPreKeyIdOrNull;
            $this->baseKey = $baseKeyOrNull;
            $this->identityKey = $identityKeyOrNull;
            $this->message = $messageOrNull;

            $builder = (new SignalProtos\PreKeySignalMessage)
                ->setSignedPreKeyId($signedPreKeyIdOrNull)
                ->setBaseKey($baseKeyOrNull->serialize())
                ->setIdentityKey($identityKeyOrNull->serialize())
                ->setMessage($messageOrNull->serialize())
                ->setRegistrationId($registrationIdOrNull);

            if($preKeyIdOrNull->isPresent()){
                $builder->setPreKeyId($preKeyIdOrNull->get());
            }

            $versionBytes = ByteUtil::intsToByteHighAndLow($this->version,self::CURRENT_VERSION);
            $messageBytes = $builder->serializeToString();

            $this->serialized = ByteUtil::combine($versionBytes,$messageBytes);
        }
    }

    public function getMessageVersion():int{
        return $this->version;
    }

    public function getIdentityKey(): IdentityKey{
        return $this->identityKey;
    }

    public function getRegistrationId(): int{
        return $this->registrationId;
    }

    public function getPreKeyId(): Optional{
        return $this->preKeyId;
    }

    public function getSignedPreKeyId(): int{
        return $this->signedPreKeyId;
    }

    public function getBaseKey(): ECPublicKey{
        return $this->baseKey;
    }

    public function getWhisperMessage(): SignalMessage{
        return $this->message;
    }

    public function serialize(): string{
        return $this->serialized;
    }

    public function getType(): int{
        return CiphertextMessage::PREKEY_TYPE;
    }

}
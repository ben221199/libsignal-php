<?php
namespace WhisperSystems\LibSignal\Protocol;

use AssertionError;
use Exception;
use WhisperSystems\LibSignal\ECC\Curve;
use WhisperSystems\LibSignal\ECC\ECPrivateKey;
use WhisperSystems\LibSignal\ECC\ECPublicKey;
use WhisperSystems\LibSignal\InvalidKeyException;
use WhisperSystems\LibSignal\InvalidMessageException;
use WhisperSystems\LibSignal\LegacyMessageException;
use WhisperSystems\LibSignal\Util\ByteUtil;

class SenderKeyMessage implements CiphertextMessage{

    private const SIGNATURE_LENGTH = 64;

    /**
     * @var int $messageVersion
     */
    private $messageVersion;
    /**
     * @var int $keyId
     */
    private $keyId;
    /**
     * @var int $iteration
     */
    private $iteration;
    /**
     * @var string $ciphertext
     */
    private $ciphertext;
    /**
     * @var string $serialized
     */
    private $serialized;

    /**
     * SenderKeyMessage constructor.
     * @param string|int $serializedOrKeyId
     * @param $iterationOrNull
     * @param $ciphertextOrNull
     * @param $signatureKeyOrNull
     * @throws LegacyMessageException
     * @throws InvalidMessageException
     */
    public function __construct($serializedOrKeyId,$iterationOrNull,$ciphertextOrNull,$signatureKeyOrNull){
        if(is_string($serializedOrKeyId) && $iterationOrNull===null && $ciphertextOrNull===null && $signatureKeyOrNull==null){
            try{
                $messageParts = ByteUtil::split($serializedOrKeyId,1,strlen($serializedOrKeyId)-1-self::SIGNATURE_LENGTH,self::SIGNATURE_LENGTH);
                $version = $messageParts[0][0];
                $message = $messageParts[1];
                $signature = $messageParts[2];

                if (ByteUtil::highBitsToInt($version) < 3) {
                    throw new LegacyMessageException("Legacy message: " . ByteUtil::highBitsToInt($version));
                }

                if (ByteUtil::highBitsToInt($version) > self::CURRENT_VERSION) {
                    throw new InvalidMessageException("Unknown version: " . ByteUtil::highBitsToInt($version));
                }

                $senderKeyMessage = new SignalProtos\SenderKeyMessage;
                $senderKeyMessage->mergeFrom($message);

                if(!$senderKeyMessage->hasId() || !$senderKeyMessage->hasIteration() || !$senderKeyMessage->hasCiphertext()){
                    throw new InvalidMessageException("Incomplete message.");
                }

                $this->serialized = $serializedOrKeyId;
                $this->messageVersion = ByteUtil::highBitsToInt($version);
                $this->keyId = $senderKeyMessage->getId();
                $this->iteration = $senderKeyMessage->getIteration();
                $this->ciphertext = $senderKeyMessage->getCiphertext();
            }catch(Exception $e){
                throw new InvalidMessageException($e);
            }
        }elseif(is_int($serializedOrKeyId) && is_int($iterationOrNull) && is_string($ciphertextOrNull) && $signatureKeyOrNull instanceof ECPrivateKey){
            $version = ByteUtil::intsToByteHighAndLow(self::CURRENT_VERSION,self::CURRENT_VERSION);
            $message = (new SignalProtos\SenderKeyMessage)
                ->setId($serializedOrKeyId)
                ->setIteration($iterationOrNull)
                ->setCiphertext($ciphertextOrNull);

            $signature = $this->getSignature($signatureKeyOrNull,ByteUtil::combine($version,$message));

            $this->serialized = ByteUtil::combine($version,$message,$signature);
            $this->messageVersion = self::CURRENT_VERSION;
            $this->keyId = $serializedOrKeyId;
            $this->iteration = $iterationOrNull;
            $this->ciphertext = $ciphertextOrNull;
        }
    }

    public function getKeyId(): int{
        return $this->keyId;
    }

    public function getIteration(): int{
        return $this->iteration;
    }

    public function getCipherText(): string{
        return $this->ciphertext;
    }

    /**
     * @param ECPublicKey $signatureKey
     * @throws InvalidMessageException
     */
    public function verifySignature(ECPublicKey $signatureKey): void{
        try {
            $parts = ByteUtil::split($this->serialized, strlen($this->serialized) - self::SIGNATURE_LENGTH,self::SIGNATURE_LENGTH);

            if (!Curve::verifySignature($signatureKey,$parts[0],$parts[1])) {
                throw new InvalidMessageException("Invalid signature!");
            }

        } catch (InvalidKeyException $e) {
            throw new InvalidMessageException($e);
        }
    }

    private function getSignature(ECPrivateKey $signatureKey,string $serialized): string{
        try{
            return Curve::calculateSignature($signatureKey,$serialized);
        }catch(InvalidKeyException $e){
            throw new AssertionError($e);
        }
    }

    public function serialize(): string{
        return $this->serialized;
    }

    public function getType(): int{
        return CiphertextMessage::SENDERKEY_TYPE;
    }

}
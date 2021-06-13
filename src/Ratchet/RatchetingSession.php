<?php
namespace WhisperSystems\LibSignal\Ratchet;

use AssertionError;
use Exception;
use WhisperSystems\LibSignal\ECC\Curve;
use WhisperSystems\LibSignal\ECC\ECPublicKey;
use WhisperSystems\LibSignal\KDF\HKDFv3;
use WhisperSystems\LibSignal\Protocol\CiphertextMessage;
use WhisperSystems\LibSignal\State\SessionState;
use WhisperSystems\LibSignal\Util\ByteUtil;
use WhisperSystems\LibSignal\Util\Guava\Optional;

class RatchetingSession{

    /**
     * @param SessionState $sessionState
     * @param SymmetricSignalProtocolParameters|AliceSignalProtocolParameters|BobSignalProtocolParameters $parameters
     * @throws Exception
     */
    public static function initializeSession(SessionState $sessionState,$parameters){
        if($parameters instanceof SymmetricSignalProtocolParameters){
            /**@var SymmetricSignalProtocolParameters $parameters*/
            if(self::isAlice($parameters->getOurBaseKey()->getPublicKey(),$parameters->getTheirBaseKey())){
                $aliceParameters = AliceSignalProtocolParameters::newBuilder();

                $aliceParameters->setOurBaseKey($parameters->getOurBaseKey())
                    ->setOurIdentityKey($parameters->getOurIdentityKey())
                    ->setTheirRatchetKey($parameters->getTheirRatchetKey())
                    ->setTheirIdentityKey($parameters->getTheirIdentityKey())
                    ->setTheirSignedPreKey($parameters->getTheirBaseKey())
                    ->setTheirOneTimePreKey(Optional::absent());

                RatchetingSession::initializeSession($sessionState, $aliceParameters->create());
            }else{
                $bobParameters = BobSignalProtocolParameters::newBuilder();

                $bobParameters->setOurIdentityKey($parameters->getOurIdentityKey())
                    ->setOurRatchetKey($parameters->getOurRatchetKey())
                    ->setOurSignedPreKey($parameters->getOurBaseKey())
                    ->setOurOneTimePreKey(Optional::absent())
                    ->setTheirBaseKey($parameters->getTheirBaseKey())
                    ->setTheirIdentityKey($parameters->getTheirIdentityKey());

                RatchetingSession::initializeSession($sessionState, $bobParameters->create());
            }
        }
        if($parameters instanceof AliceSignalProtocolParameters){
            /**@var AliceSignalProtocolParameters $parameters*/
            try{
                $sessionState->setSessionVersion(CiphertextMessage::CURRENT_VERSION);
                $sessionState->setRemoteIdentityKey($parameters->getTheirIdentityKey());
                $sessionState->setLocalIdentityKey($parameters->getOurIdentityKey()->getPublicKey());

                $sendingRatchetKey = Curve::generateKeyPair();
                $secrets = '';

                $secrets .= self::getDiscontinuityBytes();

                $secrets .= Curve::calculateAgreement($parameters->getTheirSignedPreKey(),$parameters->getOurIdentityKey()->getPrivateKey());
                $secrets .= Curve::calculateAgreement($parameters->getTheirIdentityKey()->getPublicKey(),$parameters->getOurBaseKey()->getPrivateKey());
                $secrets .= Curve::calculateAgreement($parameters->getTheirSignedPreKey(),$parameters->getOurBaseKey()->getPrivateKey());

                if($parameters->getTheirOneTimePreKey()->isPresent()){
                    $secrets .= Curve::calculateAgreement($parameters->getTheirOneTimePreKey()->get(),$parameters->getOurBaseKey()->getPrivateKey());
                }

                $derivedKeys  = self::calculateDerivedKeys($secrets);
                $sendingChain = $derivedKeys->getRootKey()->createChain($parameters->getTheirRatchetKey(), $sendingRatchetKey);

                $sessionState->addReceiverChain($parameters->getTheirRatchetKey(),$derivedKeys->getChainKey());
                $sessionState->setSenderChain($sendingRatchetKey,$sendingChain->second());
                $sessionState->setRootKey($sendingChain->first());
            }catch(Exception $e){
                throw new AssertionError($e);
            }
        }
        if($parameters instanceof BobSignalProtocolParameters){
            /**@var BobSignalProtocolParameters $parameters*/
            try{
                $sessionState->setSessionVersion(CiphertextMessage::CURRENT_VERSION);
                $sessionState->setRemoteIdentityKey($parameters->getTheirIdentityKey());
                $sessionState->setLocalIdentityKey($parameters->getOurIdentityKey()->getPublicKey());

                $secrets = '';

                $secrets .= self::getDiscontinuityBytes();

                $secrets .= Curve::calculateAgreement($parameters->getTheirIdentityKey()->getPublicKey(),$parameters->getOurSignedPreKey()->getPrivateKey());
                $secrets .= Curve::calculateAgreement($parameters->getTheirBaseKey(),$parameters->getOurIdentityKey()->getPrivateKey());
                $secrets .= Curve::calculateAgreement($parameters->getTheirBaseKey(),$parameters->getOurSignedPreKey()->getPrivateKey());

                if($parameters->getOurOneTimePreKey()->isPresent()){
                    $secrets .= Curve::calculateAgreement($parameters->getTheirBaseKey(),$parameters->getOurOneTimePreKey()->get()->getPrivateKey());
                }

                $derivedKeys = self::calculateDerivedKeys($secrets);

                $sessionState->setSenderChain($parameters->getOurRatchetKey(),$derivedKeys->getChainKey());
                $sessionState->setRootKey($derivedKeys->getRootKey());
            }catch(Exception $e) {
                throw new AssertionError($e);
            }
        }
    }

    private static function getDiscontinuityBytes(): string{
        return str_repeat("\xFF",32);
    }

    private static function calculateDerivedKeys(string $masterSecret): RatchetingSession_DerivedKeys{
        $kdf = new HKDFv3();
        $derivedSecretBytes = $kdf->deriveSecrets0($masterSecret,'WhisperText',64);
        $derivedSecrets     = ByteUtil::split($derivedSecretBytes,32,32);

        return new RatchetingSession_DerivedKeys(new RootKey($kdf,$derivedSecrets[0]),
            new ChainKey($kdf,$derivedSecrets[1], 0));
    }

    private static function isAlice(ECPublicKey $ourKey,ECPublicKey $theirKey): bool{
        return $ourKey->compareTo($theirKey) < 0;
    }

}
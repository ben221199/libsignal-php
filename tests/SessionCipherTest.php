<?php
namespace WhisperSystems\LibSignal;

use AssertionError;
use Exception;
use PHPUnit\Framework\TestCase;
use WhisperSystems\LibSignal\ECC\Curve;
use WhisperSystems\LibSignal\Protocol\CiphertextMessage;
use WhisperSystems\LibSignal\Protocol\SignalProtos\SignalMessage;
use WhisperSystems\LibSignal\Ratchet\AliceSignalProtocolParameters;
use WhisperSystems\LibSignal\Ratchet\BobSignalProtocolParameters;
use WhisperSystems\LibSignal\Ratchet\RatchetingSession;
use WhisperSystems\LibSignal\State\SessionRecord;
use WhisperSystems\LibSignal\State\SessionState;
use WhisperSystems\LibSignal\Util\Guava\Optional;

class SessionCipherTest extends TestCase{

    //throws InvalidKeyException, DuplicateMessageException,
    //LegacyMessageException, InvalidMessageException, NoSuchAlgorithmException, NoSessionException, UntrustedIdentityException
    public function testBasicSessionV3(): void{
        $aliceSessionRecord = new SessionRecord();
        $bobSessionRecord = new SessionRecord();

        $this->initializeSessionsV3($aliceSessionRecord->getSessionState(),$bobSessionRecord->getSessionState());
        $this->runInteraction($aliceSessionRecord,$bobSessionRecord);
    }

    //throws Exception

    /**
     * @throws InvalidKeyException
     */
    public function testMessageKeyLimits(): void{
        $aliceSessionRecord = new SessionRecord();
        $bobSessionRecord = new SessionRecord();

        $this->initializeSessionsV3($aliceSessionRecord->getSessionState(),$bobSessionRecord->getSessionState());

        $aliceStore = new TestInMemorySignalProtocolStore();
        $bobStore = new TestInMemorySignalProtocolStore();

        $aliceStore->storeSession(new SignalProtocolAddress('+14159999999',1),$aliceSessionRecord);
        $bobStore->storeSession(new SignalProtocolAddress('+14158888888',1),$bobSessionRecord);

        $aliceCipher = new SessionCipher($aliceStore,new SignalProtocolAddress('+14159999999',1));
        $bobCipher = new SessionCipher($bobStore,new SignalProtocolAddress('+14158888888',1));

        /**@var CiphertextMessage[] $inflight*/
        $inflight = [];

        for($i=0;$i<2010;$i++){
            $inflight[] = $aliceCipher->encrypt('you\'ve never been so hungry, you\'ve never been so cold');
        }

        $bobCipher->decrypt(new SignalMessage($inflight[1000]->serialize()));
        $bobCipher->decrypt(new SignalMessage($inflight[count($inflight)-1]->serialize()));

        try{
            $bobCipher->decrypt(new SignalMessage($inflight[0]->serialize()));
            throw new AssertionError("Should have failed!");
        } catch (DuplicateMessageException $dme) {
            // good
        }
    }

    //      throws DuplicateMessageException, LegacyMessageException, InvalidMessageException, NoSuchAlgorithmException, NoSessionException, UntrustedIdentityException
    private function runInteraction(SessionRecord $aliceSessionRecord,SessionRecord $bobSessionRecord): void{
        $aliceStore = new TestInMemorySignalProtocolStore();
        $bobStore = new TestInMemorySignalProtocolStore();

        $aliceStore->storeSession(new SignalProtocolAddress('+14159999999',1),$aliceSessionRecord);
        $bobStore->storeSession(new SignalProtocolAddress('+14158888888',1),$bobSessionRecord);

        $aliceCipher = new SessionCipher($aliceStore,new SignalProtocolAddress('+14159999999',1));
        $bobCipher = new SessionCipher($bobStore,new SignalProtocolAddress('+14158888888',1));

        $alicePlaintext = 'This is a plaintext message.';
        $message = $aliceCipher->encrypt($alicePlaintext);
        $bobPlaintext = $bobCipher->decrypt(new SignalMessage($message->serialize()));

        $this->assertTrue($alicePlaintext===$bobPlaintext);

        $bobReply = 'This is a message from Bob.';
        $reply = $bobCipher->encrypt($bobReply);
        $receivedReply = $aliceCipher->decrypt(new SignalMessage($reply->serialize()));

        $this->assertTrue($bobReply===$receivedReply);

        $aliceCiphertextMessages = [];
        $alicePlaintextMessages = [];

        for($i=0;$i<50;$i++) {
            $alicePlaintextMessages[] = 'смерть за смерть ' . $i;
            $aliceCiphertextMessages[] = $aliceCipher->encrypt('смерть за смерть ' . $i);
        }

        shuffle($aliceCiphertextMessages);
        shuffle($alicePlaintextMessages);

        for($i=0;$i<count($aliceCiphertextMessages)/2;$i++){
            $receivedPlaintext = $bobCipher->decrypt(new SignalMessage($aliceCiphertextMessages[$i]->serialize()));
            $this->assertTrue($receivedPlaintext===$alicePlaintextMessages[$i]);
        }

        $bobCiphertextMessages = [];
        $bobPlaintextMessages = [];

        for($i=0;$i<20;$i++){
            $bobPlaintextMessages[] = 'смерть за смерть ' . $i;
            $bobCiphertextMessages[] = $bobCipher->encrypt("смерть за смерть " . $i);
        }

        shuffle($bobCiphertextMessages);
        shuffle($bobPlaintextMessages);

        for($i=0;$i<count($bobCiphertextMessages)/2;$i++){
            $receivedPlaintext = $aliceCipher->decrypt(new SignalMessage($bobCiphertextMessages[$i]->serialize()));
            $this->assertTrue($receivedPlaintext===$bobPlaintextMessages[$i]);
        }

        for($i=count($aliceCiphertextMessages)/2;$i<count($aliceCiphertextMessages);$i++){
            $receivedPlaintext = $bobCipher->decrypt(new SignalMessage($aliceCiphertextMessages[$i]->serialize()));
            $this->assertTrue($receivedPlaintext===$alicePlaintextMessages[$i]);
        }

        for($i=count($bobCiphertextMessages)/2;$i<count($bobCiphertextMessages);$i++){
            $receivedPlaintext = $aliceCipher->decrypt(new SignalMessage($bobCiphertextMessages[$i]->serialize()));
            $this->assertTrue($receivedPlaintext===$bobPlaintextMessages[$i]);
        }
    }

    /**
     * @param SessionState $aliceSessionState
     * @param SessionState $bobSessionState
     * @throws Exception
     * @throws InvalidKeyException
     */
    private function initializeSessionsV3(SessionState $aliceSessionState,SessionState $bobSessionState): void{
        $aliceIdentityKeyPair = Curve::generateKeyPair();
        $aliceIdentityKey = new IdentityKeyPair(new IdentityKey($aliceIdentityKeyPair->getPublicKey()),$aliceIdentityKeyPair->getPrivateKey());
        $aliceBaseKey = Curve::generateKeyPair();
        $aliceEphemeralKey = Curve::generateKeyPair();

        $alicePreKey = $aliceBaseKey;

        $bobIdentityKeyPair = Curve::generateKeyPair();
        $bobIdentityKey = new IdentityKeyPair(new IdentityKey($bobIdentityKeyPair->getPublicKey()),$bobIdentityKeyPair->getPrivateKey());
        $bobBaseKey = Curve::generateKeyPair();
        $bobEphemeralKey = $bobBaseKey;

        $bobPreKey = Curve::generateKeyPair();

        $aliceParameters = AliceSignalProtocolParameters::newBuilder()
            ->setOurBaseKey($aliceBaseKey)
            ->setOurIdentityKey($aliceIdentityKey)
            ->setTheirOneTimePreKey(Optional::absent())
            ->setTheirRatchetKey($bobEphemeralKey->getPublicKey())
            ->setTheirSignedPreKey($bobBaseKey->getPublicKey())
            ->setTheirIdentityKey($bobIdentityKey->getPublicKey())
            ->create();

        $bobParameters = BobSignalProtocolParameters::newBuilder()
            ->setOurRatchetKey($bobEphemeralKey)
            ->setOurSignedPreKey($bobBaseKey)
            ->setOurOneTimePreKey(Optional::absent())
            ->setOurIdentityKey($bobIdentityKey)
            ->setTheirIdentityKey($aliceIdentityKey->getPublicKey())
            ->setTheirBaseKey($aliceBaseKey->getPublicKey())
            ->create();

        RatchetingSession::initializeSession($aliceSessionState,$aliceParameters);
        RatchetingSession::initializeSession($bobSessionState,$bobParameters);
    }

}
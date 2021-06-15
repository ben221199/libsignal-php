<?php
namespace WhisperSystems\LibSignal\Ratchet;

use Exception;
use PHPUnit\Framework\TestCase;
use WhisperSystems\LibSignal\ECC\Curve;
use WhisperSystems\LibSignal\ECC\ECKeyPair;
use WhisperSystems\LibSignal\IdentityKey;
use WhisperSystems\LibSignal\IdentityKeyPair;
use WhisperSystems\LibSignal\InvalidKeyException;
use WhisperSystems\LibSignal\State\SessionState;
use WhisperSystems\LibSignal\Util\Guava\Optional;

class RatchetingSessionTest extends TestCase{

    /**
     * @covers RatchetingSession
     * @throws Exception
     * @throws InvalidKeyException
     */
    public function testRatchetingSessionAsBob(): void{
        $bobPublic = implode([
            "\x05",
            "\x2c", "\xb4", "\x97", "\x76", "\xb8", "\x77", "\x02", "\x05",
            "\x74", "\x5a", "\x3a", "\x6e", "\x24", "\xf5", "\x79", "\xcd",
            "\xb4", "\xba", "\x7a", "\x89", "\x04", "\x10", "\x05", "\x92",
            "\x8e", "\xbb", "\xad", "\xc9", "\xc0", "\x5a", "\xd4", "\x58",
        ]);

        $bobPrivate = implode([
            "\xa1", "\xca", "\xb4", "\x8f", "\x7c", "\x89", "\x3f", "\xaf",
            "\xa9", "\x88", "\x0a", "\x28", "\xc3", "\xb4", "\x99", "\x9d",
            "\x28", "\xd6", "\x32", "\x95", "\x62", "\xd2", "\x7a", "\x4e",
            "\xa4", "\xe2", "\x2e", "\x9f", "\xf1", "\xbd", "\xd6", "\x5a",
        ]);

        $bobIdentityPublic = implode([
            "\x05",
            "\xf1", "\xf4", "\x38", "\x74", "\xf6", "\x96", "\x69", "\x56",
            "\xc2", "\xdd", "\x47", "\x3f", "\x8f", "\xa1", "\x5a", "\xde",
            "\xb7", "\x1d", "\x1c", "\xb9", "\x91", "\xb2", "\x34", "\x16",
            "\x92", "\x32", "\x4c", "\xef", "\xb1", "\xc5", "\xe6", "\x26",
        ]);

        $bobIdentityPrivate = implode([
            "\x48", "\x75", "\xcc", "\x69", "\xdd", "\xf8", "\xea", "\x07",
            "\x19", "\xec", "\x94", "\x7d", "\x61", "\x08", "\x11", "\x35",
            "\x86", "\x8d", "\x5f", "\xd8", "\x01", "\xf0", "\x2c", "\x02",
            "\x25", "\xe5", "\x16", "\xdf", "\x21", "\x56", "\x60", "\x5e",
        ]);

        $aliceBasePublic = implode([
            "\x05",
            "\x47", "\x2d", "\x1f", "\xb1", "\xa9", "\x86", "\x2c", "\x3a",
            "\xf6", "\xbe", "\xac", "\xa8", "\x92", "\x02", "\x77", "\xe2",
            "\xb2", "\x6f", "\x4a", "\x79", "\x21", "\x3e", "\xc7", "\xc9",
            "\x06", "\xae", "\xb3", "\x5e", "\x03", "\xcf", "\x89", "\x50",
        ]);

        $aliceEphemeralPublic = implode([
            "\x05",
            "\x6c", "\x3e", "\x0d", "\x1f", "\x52", "\x02", "\x83", "\xef",
            "\xcc", "\x55", "\xfc", "\xa5", "\xe6", "\x70", "\x75", "\xb9",
            "\x04", "\x00", "\x7f", "\x18", "\x81", "\xd1", "\x51", "\xaf",
            "\x76", "\xdf", "\x18", "\xc5", "\x1d", "\x29", "\xd3", "\x4b",
        ]);

        $aliceIdentityPublic = implode([
            "\x05",
            "\xb4", "\xa8", "\x45", "\x56", "\x60", "\xad", "\xa6", "\x5b",
            "\x40", "\x10", "\x07", "\xf6", "\x15", "\xe6", "\x54", "\x04",
            "\x17", "\x46", "\x43", "\x2e", "\x33", "\x39", "\xc6", "\x87",
            "\x51", "\x49", "\xbc", "\xee", "\xfc", "\xb4", "\x2b", "\x4a",
        ]);

        $bobSignedPreKeyPublic = implode([
            "\x05",
            "\xac", "\x24", "\x8a", "\x8f", "\x26", "\x3b", "\xe6", "\x86",
            "\x35", "\x76", "\xeb", "\x03", "\x62", "\xe2", "\x8c", "\x82",
            "\x8f", "\x01", "\x07", "\xa3", "\x37", "\x9d", "\x34", "\xba",
            "\xb1", "\x58", "\x6b", "\xf8", "\xc7", "\x70", "\xcd", "\x67",
        ]);

        $bobSignedPreKeyPrivate = implode([
            "\x58", "\x39", "\x00", "\x13", "\x1f", "\xb7", "\x27", "\x99",
            "\x8b", "\x78", "\x03", "\xfe", "\x6a", "\xc2", "\x2c", "\xc5",
            "\x91", "\xf3", "\x42", "\xe4", "\xe4", "\x2a", "\x8c", "\x8d",
            "\x5d", "\x78", "\x19", "\x42", "\x09", "\xb8", "\xd2", "\x53",
        ]);

        $senderChain = implode([
            "\x97", "\x97", "\xca", "\xca", "\x53", "\xc9", "\x89", "\xbb",
            "\xe2", "\x29", "\xa4", "\x0c", "\xa7", "\x72", "\x70", "\x10",
            "\xeb", "\x26", "\x04", "\xfc", "\x14", "\x94", "\x5d", "\x77",
            "\x95", "\x8a", "\x0a", "\xed", "\xa0", "\x88", "\xb4", "\x4d",
        ]);

        $bobIdentityKeyPublic = new IdentityKey($bobIdentityPublic,0);
        $bobIdentityKeyPrivate = Curve::decodePrivatePoint($bobIdentityPrivate);
        $bobIdentityKey = new IdentityKeyPair($bobIdentityKeyPublic,$bobIdentityKeyPrivate);
        $bobEphemeralPublicKey = Curve::decodePoint($bobPublic,0);
        $bobEphemeralPrivateKey = Curve::decodePrivatePoint($bobPrivate);
        $bobEphemeralKey = new ECKeyPair($bobEphemeralPublicKey,$bobEphemeralPrivateKey);
        $bobBaseKey = $bobEphemeralKey;
        $bobSignedPreKey = new ECKeyPair(Curve::decodePoint($bobSignedPreKeyPublic,0),Curve::decodePrivatePoint($bobSignedPreKeyPrivate));

        $aliceBasePublicKey = Curve::decodePoint($aliceBasePublic,0);
        $aliceEphemeralPublicKey = Curve::decodePoint($aliceEphemeralPublic,0);
        $aliceIdentityPublicKey = new IdentityKey($aliceIdentityPublic,0);

        $parameters = BobSignalProtocolParameters::newBuilder()
            ->setOurIdentityKey($bobIdentityKey)
            ->setOurSignedPreKey($bobSignedPreKey)
            ->setOurRatchetKey($bobEphemeralKey)
            ->setOurOneTimePreKey(Optional::absent())
            ->setTheirIdentityKey($aliceIdentityPublicKey)
            ->setTheirBaseKey($aliceBasePublicKey)
            ->create();

        $session = new SessionState();

        RatchetingSession::initializeSession($session,$parameters);

        $this->assertTrue($session->getLocalIdentityKey()->equals($bobIdentityKey->getPublicKey()));
        $this->assertTrue($session->getRemoteIdentityKey()->equals($aliceIdentityPublicKey));
        $this->assertTrue($session->getSenderChainKey()->getKey()===$senderChain);
    }

    /**
     * @throws Exception
     * @throws InvalidKeyException
     */
    public function testRatchetingSessionAsAlice(): void{
        $bobPublic = implode([
            "\x05",
            "\x2c", "\xb4", "\x97", "\x76", "\xb8", "\x77", "\x02", "\x05",
            "\x74", "\x5a", "\x3a", "\x6e", "\x24", "\xf5", "\x79", "\xcd",
            "\xb4", "\xba", "\x7a", "\x89", "\x04", "\x10", "\x05", "\x92",
            "\x8e", "\xbb", "\xad", "\xc9", "\xc0", "\x5a", "\xd4", "\x58",
        ]);

        $bobIdentityPublic = implode([
            "\x05",
            "\xf1", "\xf4", "\x38", "\x74", "\xf6", "\x96", "\x69", "\x56",
            "\xc2", "\xdd", "\x47", "\x3f", "\x8f", "\xa1", "\x5a", "\xde",
            "\xb7", "\x1d", "\x1c", "\xb9", "\x91", "\xb2", "\x34", "\x16",
            "\x92", "\x32", "\x4c", "\xef", "\xb1", "\xc5", "\xe6", "\x26",
        ]);

        $bobSignedPreKeyPublic = implode([
            "\x05",
            "\xac", "\x24", "\x8a", "\x8f", "\x26", "\x3b", "\xe6", "\x86",
            "\x35", "\x76", "\xeb", "\x03", "\x62", "\xe2", "\x8c", "\x82",
            "\x8f", "\x01", "\x07", "\xa3", "\x37", "\x9d", "\x34", "\xba",
            "\xb1", "\x58", "\x6b", "\xf8", "\xc7", "\x70", "\xcd", "\x67",
        ]);

        $aliceBasePublic = implode([
            "\x05",
            "\x47", "\x2d", "\x1f", "\xb1", "\xa9", "\x86", "\x2c", "\x3a",
            "\xf6", "\xbe", "\xac", "\xa8", "\x92", "\x02", "\x77", "\xe2",
            "\xb2", "\x6f", "\x4a", "\x79", "\x21", "\x3e", "\xc7", "\xc9",
            "\x06", "\xae", "\xb3", "\x5e", "\x03", "\xcf", "\x89", "\x50",
        ]);

        $aliceBasePrivate = implode([
            "\x11", "\xae", "\x7c", "\x64", "\xd1", "\xe6", "\x1c", "\xd5",
            "\x96", "\xb7", "\x6a", "\x0d", "\xb5", "\x01", "\x26", "\x73",
            "\x39", "\x1c", "\xae", "\x66", "\xed", "\xbf", "\xcf", "\x07",
            "\x3b", "\x4d", "\xa8", "\x05", "\x16", "\xa4", "\x74", "\x49",
        ]);

        $aliceEphemeralPublic = implode([
            "\x05",
            "\x6c", "\x3e", "\x0d", "\x1f", "\x52", "\x02", "\x83", "\xef",
            "\xcc", "\x55", "\xfc", "\xa5", "\xe6", "\x70", "\x75", "\xb9",
            "\x04", "\x00", "\x7f", "\x18", "\x81", "\xd1", "\x51", "\xaf",
            "\x76", "\xdf", "\x18", "\xc5", "\x1d", "\x29", "\xd3", "\x4b",
        ]);

        $aliceEphemeralPrivate = implode([
            "\xd1", "\xba", "\x38", "\xce", "\xa9", "\x17", "\x43", "\xd3",
            "\x39", "\x39", "\xc3", "\x3c", "\x84", "\x98", "\x65", "\x09",
            "\x28", "\x01", "\x61", "\xb8", "\xb6", "\x0f", "\xc7", "\x87",
            "\x0c", "\x59", "\x9c", "\x1d", "\x46", "\x20", "\x12", "\x48",
        ]);

        $aliceIdentityPublic   = implode([
            "\x05",
            "\xb4", "\xa8", "\x45", "\x56", "\x60", "\xad", "\xa6", "\x5b",
            "\x40", "\x10", "\x07", "\xf6", "\x15", "\xe6", "\x54", "\x04",
            "\x17", "\x46", "\x43", "\x2e", "\x33", "\x39", "\xc6", "\x87",
            "\x51", "\x49", "\xbc", "\xee", "\xfc", "\xb4", "\x2b", "\x4a",
        ]);

        $aliceIdentityPrivate = implode([
            "\x90", "\x40", "\xf0", "\xd4", "\xe0", "\x9c", "\xf3", "\x8f",
            "\x6d", "\xc7", "\xc1", "\x37", "\x79", "\xc9", "\x08", "\xc0",
            "\x15", "\xa1", "\xda", "\x4f", "\xa7", "\x87", "\x37", "\xa0",
            "\x80", "\xeb", "\x0a", "\x6f", "\x4f", "\x5f", "\x8f", "\x58",
        ]);

        $receiverChain = implode([
            "\x97", "\x97", "\xca", "\xca", "\x53", "\xc9", "\x89", "\xbb",
            "\xe2", "\x29", "\xa4", "\x0c", "\xa7", "\x72", "\x70", "\x10",
            "\xeb", "\x26", "\x04", "\xfc", "\x14", "\x94", "\x5d", "\x77",
            "\x95", "\x8a", "\x0a", "\xed", "\xa0", "\x88", "\xb4", "\x4d",
        ]);

        $bobIdentityKey = new IdentityKey($bobIdentityPublic,0);
        $bobEphemeralPublicKey = Curve::decodePoint($bobPublic,0);
        $bobSignedPreKey = Curve::decodePoint($bobSignedPreKeyPublic,0);
        $aliceBasePublicKey = Curve::decodePoint($aliceBasePublic,0);
        $aliceBasePrivateKey = Curve::decodePrivatePoint($aliceBasePrivate);
        $aliceBaseKey = new ECKeyPair($aliceBasePublicKey,$aliceBasePrivateKey);
        $aliceEphemeralPublicKey = Curve::decodePoint($aliceEphemeralPublic,0);
        $aliceEphemeralPrivateKey = Curve::decodePrivatePoint($aliceEphemeralPrivate);
        $aliceEphemeralKey = new ECKeyPair($aliceEphemeralPublicKey,$aliceEphemeralPrivateKey);
        $aliceIdentityPublicKey = new IdentityKey($aliceIdentityPublic,0);
        $aliceIdentityPrivateKey = Curve::decodePrivatePoint($aliceIdentityPrivate);
        $aliceIdentityKey = new IdentityKeyPair($aliceIdentityPublicKey,$aliceIdentityPrivateKey);

        $session = new SessionState();

        $parameters = AliceSignalProtocolParameters::newBuilder()
            ->setOurBaseKey($aliceBaseKey)
            ->setOurIdentityKey($aliceIdentityKey)
            ->setTheirIdentityKey($bobIdentityKey)
            ->setTheirSignedPreKey($bobSignedPreKey)
            ->setTheirRatchetKey($bobEphemeralPublicKey)
            ->setTheirOneTimePreKey(Optional::absent())
            ->create();

        RatchetingSession::initializeSession($session,$parameters);

        $this->assertTrue($session->getLocalIdentityKey()->equals($aliceIdentityKey->getPublicKey()));
        $this->assertTrue($session->getRemoteIdentityKey()->equals($bobIdentityKey));
        echo(bin2hex($session->getReceiverChainKey($bobEphemeralPublicKey)->getKey())."\n");
        echo(bin2hex($receiverChain)."\n");
        $this->assertTrue($session->getReceiverChainKey($bobEphemeralPublicKey)->getKey()===$receiverChain);

    }

}
<?php
namespace WhisperSystems\LibSignal\ECC;

use PHPUnit\Framework\TestCase;
use WhisperSystems\LibSignal\InvalidKeyException;

class Curve25519Test extends TestCase{

    /**
     * @throws InvalidKeyException
     */
    public function testAgreement(): void{
        $alicePublic = implode([
            "\x05",
            "\x1b", "\xb7", "\x59", "\x66", "\xf2", "\xe9", "\x3a", "\x36",
            "\x91", "\xdf", "\xff", "\x94", "\x2b", "\xb2", "\xa4", "\x66",
            "\xa1", "\xc0", "\x8b", "\x8d", "\x78", "\xca", "\x3f", "\x4d",
            "\x6d", "\xf8", "\xb8", "\xbf", "\xa2", "\xe4", "\xee", "\x28",
        ]);

        $alicePrivate = implode([
            "\xc8", "\x06", "\x43", "\x9d", "\xc9", "\xd2", "\xc4", "\x76",
            "\xff", "\xed", "\x8f", "\x25", "\x80", "\xc0", "\x88", "\x8d",
            "\x58", "\xab", "\x40", "\x6b", "\xf7", "\xae", "\x36", "\x98",
            "\x87", "\x90", "\x21", "\xb9", "\x6b", "\xb4", "\xbf", "\x59",
        ]);

        $bobPublic = implode([
            "\x05",
            "\x65", "\x36", "\x14", "\x99", "\x3d", "\x2b", "\x15", "\xee",
            "\x9e", "\x5f", "\xd3", "\xd8", "\x6c", "\xe7", "\x19", "\xef",
            "\x4e", "\xc1", "\xda", "\xae", "\x18", "\x86", "\xa8", "\x7b",
            "\x3f", "\x5f", "\xa9", "\x56", "\x5a", "\x27", "\xa2", "\x2f",
        ]);

        $bobPrivate = implode([
            "\xb0", "\x3b", "\x34", "\xc3", "\x3a", "\x1c", "\x44", "\xf2",
            "\x25", "\xb6", "\x62", "\xd2", "\xbf", "\x48", "\x59", "\xb8",
            "\x13", "\x54", "\x11", "\xfa", "\x7b", "\x03", "\x86", "\xd4",
            "\x5f", "\xb7", "\x5d", "\xc5", "\xb9", "\x1b", "\x44", "\x66",
        ]);

        $shared = implode([
            "\x32", "\x5f", "\x23", "\x93", "\x28", "\x94", "\x1c", "\xed",
            "\x6e", "\x67", "\x3b", "\x86", "\xba", "\x41", "\x01", "\x74",
            "\x48", "\xe9", "\x9b", "\x64", "\x9a", "\x9c", "\x38", "\x06",
            "\xc1", "\xdd", "\x7c", "\xa4", "\xc4", "\x77", "\xe6", "\x29",
        ]);

        $alicePublicKey = Curve::decodePoint($alicePublic,0);
        $alicePrivateKey = Curve::decodePrivatePoint($alicePrivate);

        $bobPublicKey = Curve::decodePoint($bobPublic,0);
        $bobPrivateKey = Curve::decodePrivatePoint($bobPrivate);

        $sharedOne = Curve::calculateAgreement($alicePublicKey, $bobPrivateKey);
        $sharedTwo = Curve::calculateAgreement($bobPublicKey, $alicePrivateKey);

        $this->assertTrue($sharedOne===$shared);
        $this->assertTrue($sharedTwo===$shared);
    }

}
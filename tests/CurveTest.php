<?php
namespace WhisperSystems\LibSignal;

use PHPUnit\Framework\TestCase;
use WhisperSystems\LibSignal\ECC\Curve;

class CurveTest extends TestCase{

    /**
     * @covers Curve
     */
    public function testPureJava(): void{
        $this->assertTrue(Curve::isNative());
    }

    /**
     * @covers Curve
     * @throws InvalidKeyException
     */
    public function testLargeSignatures(): void{
        $keys = Curve::generateKeyPair();
        $message = str_repeat("\0",1024*1024);
        $signature = Curve::calculateSignature($keys->getPrivateKey(),$message);

        $this->assertTrue(Curve::verifySignature($keys->getPublicKey(),$message,$signature));

        $message[0] = chr(ord($message[0]) ^ 0x01);

        $this->assertFalse(Curve::verifySignature($keys->getPublicKey(),$message,$signature));
    }

}
<?php
namespace WhisperSystems\LibSignal\Ratchet;

use PHPUnit\Framework\TestCase;
use WhisperSystems\LibSignal\KDF\HKDF;

class ChainKeyTest extends TestCase{

    /**
     * @covers ChainKey
     */
    public function testChainKeyDerivationV2(): void{
        $seed = implode([
            "\x8a", "\xb7", "\x2d", "\x6f", "\x4c", "\xc5", "\xac", "\x0d",
            "\x38", "\x7e", "\xaf", "\x46", "\x33", "\x78", "\xdd", "\xb2",
            "\x8e", "\xdd", "\x07", "\x38", "\x5b", "\x1c", "\xb0", "\x12",
            "\x50", "\xc7", "\x15", "\x98", "\x2e", "\x7a", "\xd4", "\x8f",
        ]);

        $messageKey = implode([
            "\x02", "\xa9", "\xaa", "\x6c", "\x7d", "\xbd", "\x64", "\xf9",
            "\xd3", "\xaa", "\x92", "\xf9", "\x2a", "\x27", "\x7b", "\xf5",
            "\x46", "\x09", "\xda", "\xdf", "\x0b", "\x00", "\x82", "\x8a",
            "\xcf", "\xc6", "\x1e", "\x3c", "\x72", "\x4b", "\x84", "\xa7",
        ]);

        $macKey = implode([
            "\xbf", "\xbe", "\x5e", "\xfb", "\x60", "\x30", "\x30", "\x52",
            "\x67", "\x42", "\xe3", "\xee", "\x89", "\xc7", "\x02", "\x4e",
            "\x88", "\x4e", "\x44", "\x0f", "\x1f", "\xf3", "\x76", "\xbb",
            "\x23", "\x17", "\xb2", "\xd6", "\x4d", "\xeb", "\x7c", "\x83",
        ]);

        $nextChainKey = implode([
            "\x28", "\xe8", "\xf8", "\xfe", "\xe5", "\x4b", "\x80", "\x1e",
            "\xef", "\x7c", "\x5c", "\xfb", "\x2f", "\x17", "\xf3", "\x2c",
            "\x7b", "\x33", "\x44", "\x85", "\xbb", "\xb7", "\x0f", "\xac",
            "\x6e", "\xc1", "\x03", "\x42", "\xa2", "\x46", "\xd1", "\x5d",
        ]);

        $chainKey = new ChainKey(HKDF::createFor(2),$seed,0);

        $this->assertTrue($chainKey->getKey()===$seed);
        $this->assertTrue($chainKey->getMessageKeys()->getCipherKey()===$messageKey);
        $this->assertTrue($chainKey->getMessageKeys()->getMacKey()===$macKey);
        $this->assertTrue($chainKey->getNextChainKey()->getKey()===$nextChainKey);
        $this->assertTrue($chainKey->getIndex()===0);
        $this->assertTrue($chainKey->getMessageKeys()->getCounter()===0);
        $this->assertTrue($chainKey->getNextChainKey()->getIndex()===1);
        $this->assertTrue($chainKey->getNextChainKey()->getMessageKeys()->getCounter()===1);
    }

    /**
     * @covers ChainKey
     */
    public function testChainKeyDerivationV3(): void{
        $seed = implode([
            "\x8a", "\xb7", "\x2d", "\x6f", "\x4c",
            "\xc5", "\xac", "\x0d", "\x38", "\x7e",
            "\xaf", "\x46", "\x33", "\x78", "\xdd",
            "\xb2", "\x8e", "\xdd", "\x07", "\x38",
            "\x5b", "\x1c", "\xb0", "\x12", "\x50",
            "\xc7", "\x15", "\x98", "\x2e", "\x7a",
            "\xd4", "\x8f",
        ]);

        $messageKey = implode([
            /* "\x02*/
            "\xbf", "\x51", "\xe9", "\xd7",
            "\x5e", "\x0e", "\x31", "\x03", "\x10",
            "\x51", "\xf8", "\x2a", "\x24", "\x91",
            "\xff", "\xc0", "\x84", "\xfa", "\x29",
            "\x8b", "\x77", "\x93", "\xbd", "\x9d",
            "\xb6", "\x20", "\x05", "\x6f", "\xeb",
            "\xf4", "\x52", "\x17",
        ]);

        $macKey = implode([
            "\xc6", "\xc7", "\x7d", "\x6a", "\x73",
            "\xa3", "\x54", "\x33", "\x7a", "\x56",
            "\x43", "\x5e", "\x34", "\x60", "\x7d",
            "\xfe", "\x48", "\xe3", "\xac", "\xe1",
            "\x4e", "\x77", "\x31", "\x4d", "\xc6",
            "\xab", "\xc1", "\x72", "\xe7", "\xa7",
            "\x03", "\x0b",
        ]);

        $nextChainKey = implode([
            "\x28", "\xe8", "\xf8", "\xfe", "\xe5",
            "\x4b", "\x80", "\x1e", "\xef", "\x7c",
            "\x5c", "\xfb", "\x2f", "\x17", "\xf3",
            "\x2c", "\x7b", "\x33", "\x44", "\x85",
            "\xbb", "\xb7", "\x0f", "\xac", "\x6e",
            "\xc1", "\x03", "\x42", "\xa2", "\x46",
            "\xd1", "\x5d",
        ]);

        $chainKey = new ChainKey(HKDF::createFor(3),$seed,0);

        $this->assertTrue($chainKey->getKey()===$seed);
        $this->assertTrue($chainKey->getMessageKeys()->getCipherKey()===$messageKey);
        $this->assertTrue($chainKey->getMessageKeys()->getMacKey()===$macKey);
        $this->assertTrue($chainKey->getNextChainKey()->getKey()===$nextChainKey);
        $this->assertTrue($chainKey->getIndex() == 0);
        $this->assertTrue($chainKey->getMessageKeys()->getCounter()===0);
        $this->assertTrue($chainKey->getNextChainKey()->getIndex()===1);
        $this->assertTrue($chainKey->getNextChainKey()->getMessageKeys()->getCounter()===1);
    }

}
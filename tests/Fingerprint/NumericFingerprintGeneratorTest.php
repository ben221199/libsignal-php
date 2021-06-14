<?php
namespace WhisperSystems\LibSignal\Fingerprint;

use PHPUnit\Framework\TestCase;
use WhisperSystems\LibSignal\ECC\Curve;
use WhisperSystems\LibSignal\IdentityKey;

class NumericFingerprintGeneratorTest extends TestCase{

	private static $ALICE_IDENTITY;
	private static function ALICE_IDENTITY(){
		if(!static::$ALICE_IDENTITY){
			static::$ALICE_IDENTITY = implode([
				"\x05",
				"\x06", "\x86", "\x3b", "\xc6", "\x6d", "\x02", "\xb4", "\x0d",
				"\x27", "\xb8", "\xd4", "\x9c", "\xa7", "\xc0", "\x9e", "\x92",
				"\x39", "\x23", "\x6f", "\x9d", "\x7d", "\x25", "\xd6", "\xfc",
				"\xca", "\x5c", "\xe1", "\x3c", "\x70", "\x64", "\xd8", "\x68",
			]);
		}
		return static::$ALICE_IDENTITY;
	}

	private static $BOB_IDENTITY;
	private static function BOB_IDENTITY(){
		if(!static::$BOB_IDENTITY){
			static::$BOB_IDENTITY = implode([
				"\x05",
				"\xf7", "\x81", "\xb6", "\xfb", "\x32", "\xfe", "\xd9", "\xba",
				"\x1c", "\xf2", "\xde", "\x97", "\x8d", "\x4d", "\x5d", "\xa2",
				"\x8d", "\xc3", "\x40", "\x46", "\xae", "\x81", "\x44", "\x02",
				"\xb5", "\xc0", "\xdb", "\xd9", "\x6f", "\xda", "\x90", "\x7b",
			]);
		}
		return static::$BOB_IDENTITY;
	}

	private const VERSION_1 = 1;
	private const DISPLAYABLE_FINGERPRINT_V1 = '300354477692869396892869876765458257569162576843440918079131';
	private static $ALICE_SCANNABLE_FINGERPRINT_V1;
	public static function ALICE_SCANNABLE_FINGERPRINT_V1(){
        if(!static::$ALICE_SCANNABLE_FINGERPRINT_V1){
            static::$ALICE_SCANNABLE_FINGERPRINT_V1 = implode([
                "\x08", "\x01", "\x12", "\x22", "\x0a", "\x20", "\x1e", "\x30",
                "\x1a", "\x03", "\x53", "\xdc", "\xe3", "\xdb", "\xe7", "\x68",
                "\x4c", "\xb8", "\x33", "\x6e", "\x85", "\x13", "\x6c", "\xdc",
                "\x0e", "\xe9", "\x62", "\x19", "\x49", "\x4a", "\xda", "\x30",
                "\x5d", "\x62", "\xa7", "\xbd", "\x61", "\xdf", "\x1a", "\x22",
                "\x0a", "\x20", "\xd6", "\x2c", "\xbf", "\x73", "\xa1", "\x15",
                "\x92", "\x01", "\x5b", "\x6b", "\x9f", "\x16", "\x82", "\xac",
                "\x30", "\x6f", "\xea", "\x3a", "\xaf", "\x38", "\x85", "\xb8",
                "\x4d", "\x12", "\xbc", "\xa6", "\x31", "\xe9", "\xd4", "\xfb",
                "\x3a", "\x4d",
            ]);
        }
        return static::$ALICE_SCANNABLE_FINGERPRINT_V1;
    }
    private static $BOB_SCANNABLE_FINGERPRINT_V1;
    public static function BOB_SCANNABLE_FINGERPRINT_V1(){
        if(!static::$BOB_SCANNABLE_FINGERPRINT_V1){
            static::$BOB_SCANNABLE_FINGERPRINT_V1 = implode([
                "\x08", "\x01", "\x12", "\x22", "\x0a", "\x20", "\xd6", "\x2c",
                "\xbf", "\x73", "\xa1", "\x15", "\x92", "\x01", "\x5b", "\x6b",
                "\x9f", "\x16", "\x82", "\xac", "\x30", "\x6f", "\xea", "\x3a",
                "\xaf", "\x38", "\x85", "\xb8", "\x4d", "\x12", "\xbc", "\xa6",
                "\x31", "\xe9", "\xd4", "\xfb", "\x3a", "\x4d", "\x1a", "\x22",
                "\x0a", "\x20", "\x1e", "\x30", "\x1a", "\x03", "\x53", "\xdc",
                "\xe3", "\xdb", "\xe7", "\x68", "\x4c", "\xb8", "\x33", "\x6e",
                "\x85", "\x13", "\x6c", "\xdc", "\x0e", "\xe9", "\x62", "\x19",
                "\x49", "\x4a", "\xda", "\x30", "\x5d", "\x62", "\xa7", "\xbd",
                "\x61", "\xdf",
            ]);
        }
        return static::$BOB_SCANNABLE_FINGERPRINT_V1;
    }
    
	private const VERSION_2 = 2;
	private const DISPLAYABLE_FINGERPRINT_V2 = self::DISPLAYABLE_FINGERPRINT_V1;
    private static $ALICE_SCANNABLE_FINGERPRINT_V2;
    public static function ALICE_SCANNABLE_FINGERPRINT_V2(){
        if(!static::$ALICE_SCANNABLE_FINGERPRINT_V2){
            static::$ALICE_SCANNABLE_FINGERPRINT_V2 = implode([
                "\x08", "\x02", "\x12", "\x22", "\x0a", "\x20", "\x1e", "\x30",
                "\x1a", "\x03", "\x53", "\xdc", "\xe3", "\xdb", "\xe7", "\x68",
                "\x4c", "\xb8", "\x33", "\x6e", "\x85", "\x13", "\x6c", "\xdc",
                "\x0e", "\xe9", "\x62", "\x19", "\x49", "\x4a", "\xda", "\x30",
                "\x5d", "\x62", "\xa7", "\xbd", "\x61", "\xdf", "\x1a", "\x22",
                "\x0a", "\x20", "\xd6", "\x2c", "\xbf", "\x73", "\xa1", "\x15",
                "\x92", "\x01", "\x5b", "\x6b", "\x9f", "\x16", "\x82", "\xac",
                "\x30", "\x6f", "\xea", "\x3a", "\xaf", "\x38", "\x85", "\xb8",
                "\x4d", "\x12", "\xbc", "\xa6", "\x31", "\xe9", "\xd4", "\xfb",
                "\x3a", "\x4d",
            ]);
        }
        return static::$ALICE_SCANNABLE_FINGERPRINT_V2;
    }
    private static $BOB_SCANNABLE_FINGERPRINT_V2;
    public static function BOB_SCANNABLE_FINGERPRINT_V2(){
        if(!static::$BOB_SCANNABLE_FINGERPRINT_V2){
            static::$BOB_SCANNABLE_FINGERPRINT_V2 = implode([
                "\x08", "\x02", "\x12", "\x22", "\x0a", "\x20", "\xd6", "\x2c",
                "\xbf", "\x73", "\xa1", "\x15", "\x92", "\x01", "\x5b", "\x6b",
                "\x9f", "\x16", "\x82", "\xac", "\x30", "\x6f", "\xea", "\x3a",
                "\xaf", "\x38", "\x85", "\xb8", "\x4d", "\x12", "\xbc", "\xa6",
                "\x31", "\xe9", "\xd4", "\xfb", "\x3a", "\x4d", "\x1a", "\x22",
                "\x0a", "\x20", "\x1e", "\x30", "\x1a", "\x03", "\x53", "\xdc",
                "\xe3", "\xdb", "\xe7", "\x68", "\x4c", "\xb8", "\x33", "\x6e",
                "\x85", "\x13", "\x6c", "\xdc", "\x0e", "\xe9", "\x62", "\x19",
                "\x49", "\x4a", "\xda", "\x30", "\x5d", "\x62", "\xa7", "\xbd",
                "\x61", "\xdf",
            ]);
        }
        return static::$BOB_SCANNABLE_FINGERPRINT_V2;
    }

    /**
     * @covers NumericFingerprintGenerator
     */
    public function testVectorsVersion1(): void{
        $aliceIdentityKey = new IdentityKey(self::ALICE_IDENTITY(),0);
        $bobIdentityKey = new IdentityKey(self::BOB_IDENTITY(),0);
        $aliceStableId = '+14152222222';
        $bobStableId = '+14153333333';

        $generator = new NumericFingerprintGenerator(5200);

        $aliceFingerprint = $generator->createFor(self::VERSION_1,$aliceStableId,$aliceIdentityKey,$bobStableId,$bobIdentityKey);

        $bobFingerprint = $generator->createFor(self::VERSION_1,$bobStableId,$bobIdentityKey,$aliceStableId,$aliceIdentityKey);

        $this->assertEquals(self::DISPLAYABLE_FINGERPRINT_V1,$aliceFingerprint->getDisplayableFingerprint()->getDisplayText());
        $this->assertEquals(self::DISPLAYABLE_FINGERPRINT_V1,$bobFingerprint->getDisplayableFingerprint()->getDisplayText());

        $this->assertTrue($aliceFingerprint->getScannableFingerprint()->getSerialized()===self::ALICE_SCANNABLE_FINGERPRINT_V1());
        $this->assertTrue($bobFingerprint->getScannableFingerprint()->getSerialized()===self::BOB_SCANNABLE_FINGERPRINT_V1());
    }

    /**
     * @covers NumericFingerprintGenerator
     */
    public function testVectorsVersion2(): void{
        $aliceIdentityKey = new IdentityKey(self::ALICE_IDENTITY(),0);
        $bobIdentityKey = new IdentityKey(self::BOB_IDENTITY(),0);
        $aliceStableId = '+14152222222';
        $bobStableId = '+14153333333';

        $generator = new NumericFingerprintGenerator(5200);

        $aliceFingerprint = $generator->createFor(self::VERSION_2,$aliceStableId,$aliceIdentityKey,$bobStableId,$bobIdentityKey);

        $bobFingerprint = $generator->createFor(self::VERSION_2,$bobStableId,$bobIdentityKey,$aliceStableId,$aliceIdentityKey);

        $this->assertEquals(self::DISPLAYABLE_FINGERPRINT_V2,$aliceFingerprint->getDisplayableFingerprint()->getDisplayText());
        $this->assertEquals(self::DISPLAYABLE_FINGERPRINT_V2,$bobFingerprint->getDisplayableFingerprint()->getDisplayText());

        $this->assertTrue($aliceFingerprint->getScannableFingerprint()->getSerialized()===self::ALICE_SCANNABLE_FINGERPRINT_V2());
        $this->assertTrue($bobFingerprint->getScannableFingerprint()->getSerialized()===self::BOB_SCANNABLE_FINGERPRINT_V2());
    }

    /**
     * @covers NumericFingerprintGenerator
     * @throws FingerprintParsingException
     */
    public function testMatchingFingerprints(){
        $aliceKeyPair = Curve::generateKeyPair();
        $bobKeyPair = Curve::generateKeyPair();

        $aliceIdentityKey = new IdentityKey($aliceKeyPair->getPublicKey());
        $bobIdentityKey = new IdentityKey($bobKeyPair->getPublicKey());

        $generator = new NumericFingerprintGenerator(1024);
        $aliceFingerprint = $generator->createFor(self::VERSION_1,'+14152222222',$aliceIdentityKey,'+14153333333', $bobIdentityKey);

        $bobFingerprint = $generator->createFor(self::VERSION_1,'+14153333333',$bobIdentityKey,'+14152222222',$aliceIdentityKey);

        $this->assertEquals($aliceFingerprint->getDisplayableFingerprint()->getDisplayText(),$bobFingerprint->getDisplayableFingerprint()->getDisplayText());

        $this->assertTrue($aliceFingerprint->getScannableFingerprint()->compareTo($bobFingerprint->getScannableFingerprint()->getSerialized()));
        $this->assertTrue($bobFingerprint->getScannableFingerprint()->compareTo($aliceFingerprint->getScannableFingerprint()->getSerialized()));

        $this->assertEquals(strlen($aliceFingerprint->getDisplayableFingerprint()->getDisplayText()), 60);
}

    /**
     * @covers NumericFingerprintGenerator
     * @throws FingerprintParsingException
     */
    public function testMismatchingFingerprints(): void{
        $aliceKeyPair = Curve::generateKeyPair();
        $bobKeyPair = Curve::generateKeyPair();
        $mitmKeyPair  = Curve::generateKeyPair();

        $aliceIdentityKey = new IdentityKey($aliceKeyPair->getPublicKey());
        $bobIdentityKey = new IdentityKey($bobKeyPair->getPublicKey());
        $mitmIdentityKey  = new IdentityKey($mitmKeyPair->getPublicKey());

        $generator = new NumericFingerprintGenerator(1024);
        $aliceFingerprint = $generator->createFor(self::VERSION_1,'+14152222222',$aliceIdentityKey,'+14153333333',$mitmIdentityKey);

        $bobFingerprint = $generator->createFor(self::VERSION_1,'+14153333333',$bobIdentityKey,'+14152222222',$aliceIdentityKey);

        $this->assertFalse($aliceFingerprint->getDisplayableFingerprint()->getDisplayText()===$bobFingerprint->getDisplayableFingerprint()->getDisplayText());

        $this->assertFalse($aliceFingerprint->getScannableFingerprint()->compareTo($bobFingerprint->getScannableFingerprint()->getSerialized()));
        $this->assertFalse($bobFingerprint->getScannableFingerprint()->compareTo($aliceFingerprint->getScannableFingerprint()->getSerialized()));
    }

    /**
     * @covers NumericFingerprintGenerator
     * @throws FingerprintParsingException
     */
    public function testMismatchingIdentifiers(): void{
        $aliceKeyPair = Curve::generateKeyPair();
        $bobKeyPair   = Curve::generateKeyPair();

        $aliceIdentityKey = new IdentityKey($aliceKeyPair->getPublicKey());
        $bobIdentityKey = new IdentityKey($bobKeyPair->getPublicKey());

        $generator = new NumericFingerprintGenerator(1024);
        $aliceFingerprint = $generator->createFor(self::VERSION_1,'+141512222222',$aliceIdentityKey,'+14153333333',$bobIdentityKey);

        $bobFingerprint = $generator->createFor(self::VERSION_1,'+14153333333',$bobIdentityKey,'+14152222222',$aliceIdentityKey);

        $this->assertFalse($aliceFingerprint->getDisplayableFingerprint()->getDisplayText()===$bobFingerprint->getDisplayableFingerprint()->getDisplayText());

        $this->assertFalse($aliceFingerprint->getScannableFingerprint()->compareTo($bobFingerprint->getScannableFingerprint()->getSerialized()));
        $this->assertFalse($bobFingerprint->getScannableFingerprint()->compareTo($aliceFingerprint->getScannableFingerprint()->getSerialized()));
    }

    /**
     * @covers NumericFingerprintGenerator
     */
    public function testDifferentVersionsMakeSameFingerPrintsButDifferentScannable(): void{
        $aliceIdentityKey = new IdentityKey(self::ALICE_IDENTITY(),0);
        $bobIdentityKey = new IdentityKey(self::BOB_IDENTITY(),0);
        $aliceStableId = '+14152222222';
        $bobStableId = '+14153333333';

        $generator = new NumericFingerprintGenerator(5200);

        $aliceFingerprintV1 = $generator->createFor(self::VERSION_1,$aliceStableId,$aliceIdentityKey,$bobStableId,$bobIdentityKey);

        $aliceFingerprintV2 = $generator->createFor(self::VERSION_2,$aliceStableId,$aliceIdentityKey,$bobStableId,$bobIdentityKey);

        $this->assertTrue($aliceFingerprintV1->getDisplayableFingerprint()->getDisplayText()===$aliceFingerprintV2->getDisplayableFingerprint()->getDisplayText());

        $this->assertFalse($aliceFingerprintV1->getScannableFingerprint()->getSerialized()===$aliceFingerprintV2->getScannableFingerprint()->getSerialized());
    }

}
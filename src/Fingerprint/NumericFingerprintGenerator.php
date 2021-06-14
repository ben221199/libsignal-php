<?php
namespace WhisperSystems\LibSignal\Fingerprint;

use AssertionError;
use Exception;
use WhisperSystems\LibSignal\IdentityKey;
use WhisperSystems\LibSignal\Util\ByteUtil;

class NumericFingerprintGenerator implements FingerprintGenerator{

    private const FINGERPRINT_VERSION = 0;

    /**
     * @var int $iterations
     */
    private $iterations;

    public function __construct(int $iterations){
        $this->iterations = $iterations;
    }

    /**
     * @param int $version
     * @param string $localStableIdentifier
     * @param IdentityKey|IdentityKey[] $localIdentityKey
     * @param string $remoteStableIdentifier
     * @param IdentityKey|IdentityKey[] $remoteIdentityKey
     * @return Fingerprint
     */
    public function createFor(int $version,string $localStableIdentifier,IdentityKey $localIdentityKey,string $remoteStableIdentifier,IdentityKey $remoteIdentityKey): Fingerprint{
        return $this->createForMultiple($version,$localStableIdentifier,[$localIdentityKey],$remoteStableIdentifier,[$remoteIdentityKey]);
    }

    /**
     * @param int $version
     * @param string $localStableIdentifier
     * @param IdentityKey[]|array $localIdentityKeys
     * @param string $remoteStableIdentifier
     * @param IdentityKey[]|array $remoteIdentityKeys
     * @return Fingerprint
     */
    public function createForMultiple(int $version,string $localStableIdentifier,array $localIdentityKeys,string $remoteStableIdentifier,array $remoteIdentityKeys): Fingerprint{
        $localFingerprint = $this->getFingerprint($this->iterations,$localStableIdentifier,$localIdentityKeys);
        $remoteFingerprint = $this->getFingerprint($this->iterations,$remoteStableIdentifier,$remoteIdentityKeys);

        $displayableFingerprint = new DisplayableFingerprint($localFingerprint,$remoteFingerprint);
        $scannableFingerprint = new ScannableFingerprint($version,$localFingerprint,$remoteFingerprint);

        return new Fingerprint($displayableFingerprint,$scannableFingerprint);
    }

    private function getFingerprint(int $iterations,string $stableIdentifier,array $unsortedIdentityKeys): string{
        try{
            $publicKey = $this->getLogicalKeyBytes($unsortedIdentityKeys);
            $hash = ByteUtil::combine(ByteUtil::shortToByteArray(self::FINGERPRINT_VERSION),$publicKey,$stableIdentifier);

            for($i=0;$i<$iterations;$i++){
                $hash = hash('sha512',$hash.$publicKey,true);
            }

            return $hash;
        }catch(Exception $e){
            throw new AssertionError($e);
        }
    }

    private function getLogicalKeyBytes(array $identityKeys): string{
        $sortedIdentityKeys = $identityKeys;
        sort($sortedIdentityKeys);//TODO new IdentityKeyComparator

        $baos = '';

        foreach($sortedIdentityKeys AS $identityKey){
            $publicKeyBytes = $identityKey->getPublicKey()->serialize();
            $baos .= $publicKeyBytes;
        }

        return $baos;
    }

}
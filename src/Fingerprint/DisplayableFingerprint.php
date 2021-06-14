<?php
namespace WhisperSystems\LibSignal\Fingerprint;

use WhisperSystems\LibSignal\Util\ByteUtil;

class DisplayableFingerprint{

    /**
     * @var string $localFingerprintNumbers
     */
    private $localFingerprintNumbers;
    /**
     * @var string $remoteFingerprintNumbers
     */
    private $remoteFingerprintNumbers;

    function __construct(string $localFingerprint,string $remoteFingerprint){
        $this->localFingerprintNumbers  = $this->getDisplayStringFor($localFingerprint);
        $this->remoteFingerprintNumbers = $this->getDisplayStringFor($remoteFingerprint);
    }

    public function getDisplayText(): string{
        if(strcmp($this->localFingerprintNumbers,$this->remoteFingerprintNumbers) <= 0){
            return $this->localFingerprintNumbers . $this->remoteFingerprintNumbers;
        }else{
            return $this->remoteFingerprintNumbers . $this->localFingerprintNumbers;
        }
    }

    private function getDisplayStringFor(string $fingerprint): string{
        return $this->getEncodedChunk($fingerprint, 0) .
            $this->getEncodedChunk($fingerprint, 5) .
            $this->getEncodedChunk($fingerprint, 10) .
            $this->getEncodedChunk($fingerprint, 15) .
            $this->getEncodedChunk($fingerprint, 20) .
            $this->getEncodedChunk($fingerprint, 25);
    }

    private function getEncodedChunk(string $hash,int $offset): string{
        $chunk = ByteUtil::byteArray5ToLong($hash,$offset) % 100000;
        return sprintf('%05d', $chunk);
    }

}
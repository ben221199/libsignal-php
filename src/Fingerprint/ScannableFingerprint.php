<?php
namespace WhisperSystems\LibSignal\Fingerprint;

use Exception;
use WhisperSystems\LibSignal\Fingerprint\FingerprintProtos\CombinedFingerprints;
use WhisperSystems\LibSignal\Fingerprint\FingerprintProtos\LogicalFingerprint;
use WhisperSystems\LibSignal\Util\ByteUtil;

class ScannableFingerprint{

    /**
     * @var int $version
     */
    private $version;
    /**
     * @var CombinedFingerprints $fingerprints
     */
    private $fingerprints;

    function __construct(int $version,string $localFingerprintData,string $remoteFingerprintData){
        $localFingerprint = (new LogicalFingerprint)
            ->setContent(ByteUtil::trim($localFingerprintData,32));
        $remoteFingerprint = (new LogicalFingerprint)
            ->setContent(ByteUtil::trim($remoteFingerprintData,32));

        $this->version = $version;
        $this->fingerprints = (new CombinedFingerprints)
            ->setVersion($version)
            ->setLocalFingerprint($localFingerprint)
            ->setRemoteFingerprint($remoteFingerprint);
    }

    public function getSerialized(): string{
        return $this->fingerprints->serializeToString();
    }

    //throws FingerprintVersionMismatchException,
    //FingerprintParsingException

    /**
     * @param string $scannedFingerprintData
     * @return bool
     * @throws FingerprintParsingException
     * @throws Exception
     */
    public function compareTo(string $scannedFingerprintData){
        try{
            $scanned = new CombinedFingerprints;
            $scanned->mergeFromString($scannedFingerprintData);

            if(!$scanned->hasRemoteFingerprint() || !$scanned->hasLocalFingerprint() || !$scanned->hasVersion() || $scanned->getVersion() != $this->version){
                throw new FingerprintVersionMismatchException($scanned->getVersion(),$this->version);
            }

            return $this->fingerprints->getLocalFingerprint()->getContent()===$scanned->getRemoteFingerprint()->getContent() && $this->fingerprints->getRemoteFingerprint()->getContent()===$scanned->getLocalFingerprint()->getContent();
        }catch(Exception $e){
            throw new FingerprintParsingException($e);
        }
    }

}
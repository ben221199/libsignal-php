<?php
namespace WhisperSystems\LibSignal\Fingerprint;

class Fingerprint{

    /**
     * @var DisplayableFingerprint $displayableFingerprint
     */
    private $displayableFingerprint;

    /**
     * @var ScannableFingerprint $scannableFingerprint
     */
    private $scannableFingerprint;

    public function __construct(DisplayableFingerprint $displayableFingerprint,ScannableFingerprint $scannableFingerprint){
        $this->displayableFingerprint = $displayableFingerprint;
        $this->scannableFingerprint = $scannableFingerprint;
    }

    /**
     * @return DisplayableFingerprint A text fingerprint that can be displayed and compared remotely.
     */
    public function getDisplayableFingerprint(): DisplayableFingerprint{
        return $this->displayableFingerprint;
    }

    /**
     * @return ScannableFingerprint A scannable fingerprint that can be scanned and compared locally.
     */
    public function getScannableFingerprint(): ScannableFingerprint{
        return $this->scannableFingerprint;
    }

}
<?php
namespace WhisperSystems\LibSignal\Ratchet;

use Exception;
use WhisperSystems\LibSignal\ECC\ECKeyPair;
use WhisperSystems\LibSignal\ECC\ECPublicKey;
use WhisperSystems\LibSignal\IdentityKey;
use WhisperSystems\LibSignal\IdentityKeyPair;

class SymmetricSignalProtocolParameters{

    /**
     * @var ECKeyPair $ourBaseKey
     */
    private $ourBaseKey;
    /**
     * @var ECKeyPair $ourRatchetKey
     */
    private $ourRatchetKey;
    /**
     * @var IdentityKeyPair $ourIdentityKey
     */
    private $ourIdentityKey;

    /**
     * @var ECPublicKey $theirBaseKey
     */
    private $theirBaseKey;
    /**
     * @var ECPublicKey
     */
    private $theirRatchetKey;
    /**
     * @var IdentityKey
     */
    private $theirIdentityKey;

    /**
     * SymmetricSignalProtocolParameters constructor.
     * @param ECKeyPair $ourBaseKey
     * @param ECKeyPair $ourRatchetKey
     * @param IdentityKeyPair $ourIdentityKey
     * @param ECPublicKey $theirBaseKey
     * @param ECPublicKey $theirRatchetKey
     * @param IdentityKey $theirIdentityKey
     * @throws Exception
     */
    function __construct(ECKeyPair $ourBaseKey,ECKeyPair $ourRatchetKey,IdentityKeyPair $ourIdentityKey,ECPublicKey $theirBaseKey,ECPublicKey $theirRatchetKey,IdentityKey $theirIdentityKey){
        $this->ourBaseKey       = $ourBaseKey;
        $this->ourRatchetKey    = $ourRatchetKey;
        $this->ourIdentityKey   = $ourIdentityKey;
        $this->theirBaseKey     = $theirBaseKey;
        $this->theirRatchetKey  = $theirRatchetKey;
        $this->theirIdentityKey = $theirIdentityKey;

        if($ourBaseKey===null || $ourRatchetKey===null || $ourIdentityKey===null || $theirBaseKey===null || $theirRatchetKey===null || $theirIdentityKey===null){
            throw new Exception("Null values!");
        }
    }

    public function getOurBaseKey(): ECKeyPair{
        return $this->ourBaseKey;
    }

    public function getOurRatchetKey(): ECKeyPair{
        return $this->ourRatchetKey;
    }

    public function getOurIdentityKey(): IdentityKeyPair{
        return $this->ourIdentityKey;
    }

    public function getTheirBaseKey(): ECPublicKey{
        return $this->theirBaseKey;
    }

    public function getTheirRatchetKey(): ECPublicKey{
        return $this->theirRatchetKey;
    }

    public function getTheirIdentityKey(): IdentityKey{
        return $this->theirIdentityKey;
    }

    public static function newBuilder(): SymmetricSignalProtocolParameters_Builder{
        return new SymmetricSignalProtocolParameters_Builder();
    }

}
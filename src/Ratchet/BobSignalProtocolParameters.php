<?php
namespace WhisperSystems\LibSignal\Ratchet;

use Exception;
use WhisperSystems\LibSignal\ECC\ECKeyPair;
use WhisperSystems\LibSignal\ECC\ECPublicKey;
use WhisperSystems\LibSignal\IdentityKey;
use WhisperSystems\LibSignal\IdentityKeyPair;
use WhisperSystems\LibSignal\Util\Guava\Optional;

class BobSignalProtocolParameters{

    /**
     * @var IdentityKeyPair $ourIdentityKey
     */
    private $ourIdentityKey;
    /**
     * @var ECKeyPair $ourSignedPreKey
     */
    private $ourSignedPreKey;
    /**
     * @var Optional $ourOneTimePreKey
     */
    private $ourOneTimePreKey;
    /**
     * @var ECKeyPair $ourRatchetKey
     */
    private $ourRatchetKey;

    /**
     * @var IdentityKey $theirIdentityKey
     */

    private $theirIdentityKey;
    /**
     * @var ECPublicKey $theirBaseKey
     */
    private $theirBaseKey;

    /**
     * BobSignalProtocolParameters constructor.
     * @param IdentityKeyPair $ourIdentityKey
     * @param ECKeyPair $ourSignedPreKey
     * @param ECKeyPair $ourRatchetKey
     * @param Optional $ourOneTimePreKey
     * @param IdentityKey $theirIdentityKey
     * @param ECPublicKey $theirBaseKey
     * @throws Exception
     */
    function __construct(IdentityKeyPair $ourIdentityKey,ECKeyPair $ourSignedPreKey,ECKeyPair $ourRatchetKey,Optional $ourOneTimePreKey,IdentityKey $theirIdentityKey,ECPublicKey $theirBaseKey){
        $this->ourIdentityKey = $ourIdentityKey;
        $this->ourSignedPreKey = $ourSignedPreKey;
        $this->ourRatchetKey = $ourRatchetKey;
        $this->ourOneTimePreKey = $ourOneTimePreKey;
        $this->theirIdentityKey = $theirIdentityKey;
        $this->theirBaseKey = $theirBaseKey;

        if($ourIdentityKey===null || $ourSignedPreKey===null || $ourRatchetKey===null || $ourOneTimePreKey===null || $theirIdentityKey===null || $theirBaseKey===null){
            throw new Exception("Null value!");
        }
    }

    public function getOurIdentityKey(): IdentityKeyPair{
        return $this->ourIdentityKey;
    }

    public function getOurSignedPreKey(): ECKeyPair{
        return $this->ourSignedPreKey;
    }

    public function getOurOneTimePreKey(): Optional{
        return $this->ourOneTimePreKey;
    }

    public function getTheirIdentityKey(): IdentityKey{
        return $this->theirIdentityKey;
    }

    public function getTheirBaseKey(): ECPublicKey{
        return $this->theirBaseKey;
    }

    public static function newBuilder(): BobSignalProtocolParameters_Builder{
        return new BobSignalProtocolParameters_Builder();
    }

    public function getOurRatchetKey(): ECKeyPair{
        return $this->ourRatchetKey;
    }

}
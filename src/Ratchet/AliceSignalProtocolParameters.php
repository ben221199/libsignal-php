<?php
namespace WhisperSystems\LibSignal\Ratchet;

use Exception;
use WhisperSystems\LibSignal\ECC\ECKeyPair;
use WhisperSystems\LibSignal\ECC\ECPublicKey;
use WhisperSystems\LibSignal\IdentityKey;
use WhisperSystems\LibSignal\IdentityKeyPair;
use WhisperSystems\LibSignal\Util\Guava\Optional;

class AliceSignalProtocolParameters{

    /**
     * @var IdentityKeyPair $ourIdentityKey
     */
    private $ourIdentityKey;
    /**
     * @var ECKeyPair $ourBaseKey
     */
    private $ourBaseKey;

    /**
     * @var IdentityKey $theirIdentityKey
     */
    private $theirIdentityKey;
    /**
     * @var ECPublicKey $theirSignedPreKey
     */
    private $theirSignedPreKey;
    /**
     * @var Optional $theirOneTimePreKey
     */
    private $theirOneTimePreKey;
    /**
     * @var ECPublicKey $theirRatchetKey
     */
    private $theirRatchetKey;

    /**
     * AliceSignalProtocolParameters constructor.
     * @param IdentityKeyPair $ourIdentityKey
     * @param ECKeyPair $ourBaseKey
     * @param IdentityKey $theirIdentityKey
     * @param ECPublicKey $theirSignedPreKey
     * @param ECPublicKey $theirRatchetKey
     * @param Optional $theirOneTimePreKey
     * @throws Exception
     */
    function __construct(IdentityKeyPair $ourIdentityKey,ECKeyPair $ourBaseKey,IdentityKey $theirIdentityKey,ECPublicKey $theirSignedPreKey,ECPublicKey $theirRatchetKey,Optional $theirOneTimePreKey){
        $this->ourIdentityKey = $ourIdentityKey;
        $this->ourBaseKey = $ourBaseKey;
        $this->theirIdentityKey = $theirIdentityKey;
        $this->theirSignedPreKey = $theirSignedPreKey;
        $this->theirRatchetKey = $theirRatchetKey;
        $this->theirOneTimePreKey = $theirOneTimePreKey;

        if($ourIdentityKey === null || $ourBaseKey === null || $theirIdentityKey === null || $theirSignedPreKey === null || $theirRatchetKey === null || $theirOneTimePreKey === null){
            throw new Exception("Null values!");
        }
    }

    public function getOurIdentityKey(): IdentityKeyPair{
        return $this->ourIdentityKey;
    }

    public function getOurBaseKey(): ECKeyPair{
        return $this->ourBaseKey;
    }

    public function getTheirIdentityKey(): IdentityKey{
        return $this->theirIdentityKey;
    }

    public function getTheirSignedPreKey(): ECPublicKey{
        return $this->theirSignedPreKey;
    }

    public function getTheirOneTimePreKey(): Optional{
        return $this->theirOneTimePreKey;
    }

    public static function newBuilder(): AliceSignalProtocolParameters_Builder{
        return new AliceSignalProtocolParameters_Builder();
    }

    public function getTheirRatchetKey(): ECPublicKey{
        return $this->theirRatchetKey;
    }

}
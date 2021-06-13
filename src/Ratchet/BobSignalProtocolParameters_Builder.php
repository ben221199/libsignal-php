<?php
namespace WhisperSystems\LibSignal\Ratchet;

use Exception;
use WhisperSystems\LibSignal\ECC\ECKeyPair;
use WhisperSystems\LibSignal\ECC\ECPublicKey;
use WhisperSystems\LibSignal\IdentityKey;
use WhisperSystems\LibSignal\IdentityKeyPair;
use WhisperSystems\LibSignal\Util\Guava\Optional;

class BobSignalProtocolParameters_Builder{

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
     * @var ECPublicKey
     */
    private $theirBaseKey;

    public function setOurIdentityKey(IdentityKeyPair $ourIdentityKey): self{
        $this->ourIdentityKey = $ourIdentityKey;
        return $this;
    }

    public function setOurSignedPreKey(ECKeyPair $ourSignedPreKey): self{
        $this->ourSignedPreKey = $ourSignedPreKey;
        return $this;
    }

    public function setOurOneTimePreKey(Optional $ourOneTimePreKey): self{
        $this->ourOneTimePreKey = $ourOneTimePreKey;
        return $this;
    }

    public function setTheirIdentityKey(IdentityKey $theirIdentityKey): self{
        $this->theirIdentityKey = $theirIdentityKey;
        return $this;
    }

    public function setTheirBaseKey(ECPublicKey $theirBaseKey): self{
        $this->theirBaseKey = $theirBaseKey;
        return $this;
    }

    public function setOurRatchetKey(ECKeyPair $ourRatchetKey): self{
        $this->ourRatchetKey = $ourRatchetKey;
        return $this;
    }

    /**
     * @return BobSignalProtocolParameters
     * @throws Exception
     */
    public function create(): BobSignalProtocolParameters{
        return new BobSignalProtocolParameters($this->ourIdentityKey, $this->ourSignedPreKey, $this->ourRatchetKey,
            $this->ourOneTimePreKey, $this->theirIdentityKey, $this->theirBaseKey);
    }

}
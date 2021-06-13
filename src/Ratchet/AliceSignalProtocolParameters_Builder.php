<?php
namespace WhisperSystems\LibSignal\Ratchet;

use Exception;
use WhisperSystems\LibSignal\ECC\ECKeyPair;
use WhisperSystems\LibSignal\ECC\ECPublicKey;
use WhisperSystems\LibSignal\IdentityKey;
use WhisperSystems\LibSignal\IdentityKeyPair;
use WhisperSystems\LibSignal\Util\Guava\Optional;

class AliceSignalProtocolParameters_Builder{

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
     * @var ECPublicKey $theirRatchetKey
     */
    private $theirRatchetKey;
    /**
     * @var Optional $theirOneTimePreKey
     */
    private $theirOneTimePreKey;

    public function setOurIdentityKey(IdentityKeyPair $ourIdentityKey): self{
        $this->ourIdentityKey = $ourIdentityKey;
        return $this;
    }

    public function setOurBaseKey(ECKeyPair $ourBaseKey): self{
        $this->ourBaseKey = $ourBaseKey;
        return $this;
    }

    public function setTheirRatchetKey(ECPublicKey $theirRatchetKey): self{
        $this->theirRatchetKey = $theirRatchetKey;
        return $this;
    }

    public function setTheirIdentityKey(IdentityKey $theirIdentityKey): self{
        $this->theirIdentityKey = $theirIdentityKey;
        return $this;
    }

    public function setTheirSignedPreKey(ECPublicKey $theirSignedPreKey): self{
        $this->theirSignedPreKey = $theirSignedPreKey;
        return $this;
    }

    public function setTheirOneTimePreKey(Optional $theirOneTimePreKey): self{
        $this->theirOneTimePreKey = $theirOneTimePreKey;
        return $this;
    }

    /**
     * @return AliceSignalProtocolParameters
     * @throws Exception
     */
    public function create(): AliceSignalProtocolParameters{
        return new AliceSignalProtocolParameters($this->ourIdentityKey, $this->ourBaseKey, $this->theirIdentityKey,
            $this->theirSignedPreKey, $this->theirRatchetKey, $this->theirOneTimePreKey);
    }

}
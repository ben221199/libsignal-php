<?php
namespace WhisperSystems\LibSignal\Ratchet;

use Exception;
use WhisperSystems\LibSignal\ECC\ECKeyPair;
use WhisperSystems\LibSignal\ECC\ECPublicKey;
use WhisperSystems\LibSignal\IdentityKey;
use WhisperSystems\LibSignal\IdentityKeyPair;

class SymmetricSignalProtocolParameters_Builder{

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
     * @var ECPublicKey $theirRatchetKey
     */
    private $theirRatchetKey;
    /**
     * @var IdentityKey
     */
    private $theirIdentityKey;

    public function setOurBaseKey(ECKeyPair $ourBaseKey): self{
        $this->ourBaseKey = $ourBaseKey;
        return $this;
    }

    public function setOurRatchetKey(ECKeyPair $ourRatchetKey): self{
        $this->ourRatchetKey = $ourRatchetKey;
        return $this;
    }

    public function setOurIdentityKey(IdentityKeyPair $ourIdentityKey): self{
        $this->ourIdentityKey = $ourIdentityKey;
        return $this;
    }

    public function setTheirBaseKey(ECPublicKey $theirBaseKey): self{
        $this->theirBaseKey = $theirBaseKey;
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

    /**
     * @return SymmetricSignalProtocolParameters
     * @throws Exception
     */
    public function create(): SymmetricSignalProtocolParameters{
        return new SymmetricSignalProtocolParameters($this->ourBaseKey, $this->ourRatchetKey, $this->ourIdentityKey,
            $this->theirBaseKey, $this->theirRatchetKey, $this->theirIdentityKey);
    }

}
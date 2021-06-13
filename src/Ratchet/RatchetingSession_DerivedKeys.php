<?php
namespace WhisperSystems\LibSignal\Ratchet;

class RatchetingSession_DerivedKeys{

    /**
     * @var RootKey $rootKey
     */
    private $rootKey;
    /**
     * @var ChainKey $chainKey
     */
    private $chainKey;

    function __construct(RootKey $rootKey,ChainKey $chainKey){
        $this->rootKey = $rootKey;
        $this->chainKey = $chainKey;
    }

    public function getRootKey(): RootKey{
        return $this->rootKey;
    }

    public function getChainKey(): ChainKey{
        return $this->chainKey;
    }

}
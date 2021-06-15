<?php
namespace WhisperSystems\LibSignal\State;

use WhisperSystems\LibSignal\ECC\ECPublicKey;
use WhisperSystems\LibSignal\Util\Guava\Optional;

class SessionState_UnacknowledgedPreKeyMessageItems{

    /**
     * @var Optional $preKeyId
     */
    private $preKeyId;
    /**
     * @var int $signedPreKeyId
     */
    private $signedPreKeyId;
    /**
     * @var ECPublicKey $baseKey
     */
    private $baseKey;

    public function __construct(Optional $preKeyId,int $signedPreKeyId,ECPublicKey $baseKey){
        $this->preKeyId = $preKeyId;
        $this->signedPreKeyId = $signedPreKeyId;
        $this->baseKey = $baseKey;
    }

    public function getPreKeyId(): Optional{
        return $this->preKeyId;
    }

    public function getSignedPreKeyId(): int{
        return $this->signedPreKeyId;
    }

    public function getBaseKey(): ECPublicKey{
        return $this->baseKey;
    }

}
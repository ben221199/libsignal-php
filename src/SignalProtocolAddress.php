<?php
namespace WhisperSystems\LibSignal;

class SignalProtocolAddress{

    /**
     * @var string $name
     */
    private $name;
    /**
     * @var int $deviceId
     */
    private $deviceId;

    public function __construct(string $name,int $deviceId){
        $this->name = $name;
        $this->deviceId = $deviceId;
    }

    public function getName(): string{
        return $this->name;
    }

    public function getDeviceId(): int{
        return $this->deviceId;
    }

    public function __toString(): string{
        return $this->name . ":" . $this->deviceId;
    }

    public function equals($other): bool{
        if($other===null){
            return false;
        }
        if(!($other instanceof SignalProtocolAddress)){
            return false;
        }
        /**@var SignalProtocolAddress $that*/
        $that = $other;
        return $this->name===$that->name && $this->deviceId===$that->deviceId;
    }

}
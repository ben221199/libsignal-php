<?php
namespace WhisperSystems\LibSignal\Devices;

class DeviceConsistencySignature{

    /**
     * @var string $signature
     */
    private $signature;
    /**
     * @var string $vrfOutput
     */
    private $vrfOutput;

    public function __construct(string $signature,string $vrfOutput){
        $this->signature = $signature;
        $this->vrfOutput = $vrfOutput;
    }

    public function getVrfOutput(): string{
        return $this->vrfOutput;
    }

    public function getSignature(): string{
        return $this->signature;
    }

}
<?php
namespace WhisperSystems\LibSignal\Devices;

use WhisperSystems\LibSignal\Util\ByteArrayComparator;

class DeviceConsistencyCodeGenerator_SignatureComparator extends ByteArrayComparator{

    public function compare0(DeviceConsistencySignature $first,DeviceConsistencySignature $second): int{
        return $this->compare($first->getVrfOutput(),$second->getVrfOutput());
    }

}
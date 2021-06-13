<?php
namespace WhisperSystems\LibSignal\KDF;

class HKDFv3 extends HKDF{

    public function getIterationStartOffset(): int{
        return 1;
    }

}
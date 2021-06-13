<?php
namespace WhisperSystems\LibSignal\KDF;

class HKDFv2 extends HKDF{

    public function getIterationStartOffset(): int{
        return 0;
    }

}
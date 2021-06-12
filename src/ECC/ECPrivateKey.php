<?php
namespace WhisperSystems\LibSignal\ECC;

interface ECPrivateKey{

    public function serialize(): string;
    public function getType(): int;

}
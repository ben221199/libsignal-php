<?php
namespace WhisperSystems\LibSignal\ECC2;

interface ECPrivateKey{

    public function serialize(): string;
    public function getType(): int;

}
<?php
namespace WhisperSystems\LibSignal\ECC2;

interface ECPublicKey{

    public const KEY_SIZE = 33;

    public function serialize(): string;

    public function getType(): int;

}
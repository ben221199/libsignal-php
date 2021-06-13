<?php
namespace WhisperSystems\LibSignal;

interface DecryptionCallback{

    public function handlePlaintext(string $plaintext): void;

}
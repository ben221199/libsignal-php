<?php
namespace WhisperSystems\LibSignal;

class SessionCipher_NullDecryptionCallback implements DecryptionCallback{

    public function handlePlaintext(string $plaintext): void{
    }

}
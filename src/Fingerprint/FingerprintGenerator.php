<?php
namespace WhisperSystems\LibSignal\Fingerprint;

use WhisperSystems\LibSignal\IdentityKey;

interface FingerprintGenerator{

    public function createFor(int $version,
                              string $localStableIdentifier,
                              IdentityKey $localIdentityKey,
                              string $remoteStableIdentifier,
                              IdentityKey $remoteIdentityKey): Fingerprint;

    public function createForMultiple(int $version,
                              string $localStableIdentifier,
                              array $localIdentityKey,
                              string $remoteStableIdentifier,
                              array $remoteIdentityKey): Fingerprint;

}
<?php
namespace WhisperSystems\LibSignal\Protocol;

interface CiphertextMessage{

    public const CURRENT_VERSION     = 3;

    public const WHISPER_TYPE                = 2;
    public const PREKEY_TYPE                 = 3;
    public const SENDERKEY_TYPE              = 4;
    public const SENDERKEY_DISTRIBUTION_TYPE = 5;

    public const ENCRYPTED_MESSAGE_OVERHEAD = 53;

    public function serialize(): string;
    public function getType(): int;

}
<?php
namespace WhisperSystems\LibSignal\Logging;

interface SignalProtocolLogger{

    public const VERBOSE = 2;
    public const DEBUG   = 3;
    public const INFO    = 4;
    public const WARN    = 5;
    public const ERROR   = 6;
    public const ASSERT  = 7;

    public function log(int $priority,string $tag,string $message): void;

}
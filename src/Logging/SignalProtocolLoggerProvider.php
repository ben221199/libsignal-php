<?php
namespace WhisperSystems\LibSignal\Logging;

class SignalProtocolLoggerProvider{

    /**
     * @var SignalProtocolLogger $provider
     */
    private static $provider;

    public static function getProvider(): SignalProtocolLogger{
        return self::$provider;
    }

    public static function setProvider(SignalProtocolLogger $provider): void{
        self::$provider = $provider;
    }

}
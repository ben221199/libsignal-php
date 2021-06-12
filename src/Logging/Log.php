<?php
namespace WhisperSystems\LibSignal\Logging;

use Throwable;

class Log{

    private function __construct(){
    }

    public static function v(string $tag,string $msg,?Throwable $tr=null): void{
        self::log(SignalProtocolLogger::VERBOSE,$tag,$msg . "\n" . self::getStackTraceString($tr));
    }

    public static function d(string $tag,string $msg,?Throwable $tr=null): void{
        self::log(SignalProtocolLogger::DEBUG,$tag,$msg . "\n" . self::getStackTraceString($tr));
    }

    public static function i(string $tag,string $msg,?Throwable $tr=null): void{
        self::log(SignalProtocolLogger::INFO,$tag,$msg . '\n' . self::getStackTraceString($tr));
    }

    /**
     * @param string $tag
     * @param string|Throwable $msgOrTr
     * @param Throwable|null $trOrNull
     */
    public static function w(string $tag,$msgOrTr,$trOrNull=null){
        $msg = null;
        $tr = null;
        if(is_string($msgOrTr)){
            $msg = $msgOrTr;
        }
        if($msgOrTr instanceof Throwable && $trOrNull==null){
            $tr = $msgOrTr;
        }
        if($trOrNull instanceof Throwable){
            $tr = $trOrNull;
        }
        self::log(SignalProtocolLogger::WARN, $tag, $msg . "\n" . self::getStackTraceString($tr));
    }

    public static function e(string $tag,string $msg,?Throwable $tr=null): void{
        self::log(SignalProtocolLogger::ERROR,$tag, $msg . "\n" . self::getStackTraceString($tr));
    }

    private static function getStackTraceString(Throwable $tr): string{
        if($tr===null){
            return "";
        }

        return $tr->getTraceAsString();
    }

    private static function log(int $priority,string $tag,string $msg): void{
        $logger = SignalProtocolLoggerProvider::getProvider();

        if($logger!==null){
            $logger->log($priority, $tag, $msg);
        }
    }

}
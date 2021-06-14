<?php
namespace WhisperSystems\LibSignal\Devices;

use AssertionError;
use Exception;
use WhisperSystems\LibSignal\Util\ByteUtil;

class DeviceConsistencyCodeGenerator{

    private const CODE_VERSION = 0;

    /**
     * @param DeviceConsistencyCommitment $commitment
     * @param DeviceConsistencySignature[]|array $signatures
     * @return string
     */
    public static function generateFor(DeviceConsistencyCommitment $commitment,array $signatures): string{
        try{
            $sortedSignatures = $signatures;
            sort($sortedSignatures);//TODO new SignatureComparator()

            $data = '';
            $data .= ByteUtil::shortToByteArray(self::CODE_VERSION);
            $data .= $commitment->toByteArray();

            foreach($sortedSignatures AS $signature){
                $data .= $signature->getVrfOutput();
            }

            $hash = hash('sha512',$data,true);

            $digits = self::getEncodedChunk($hash,0) . self::getEncodedChunk($hash,5);
            return substr($digits,0,6);
        }catch(Exception $e){
            throw new AssertionError($e);
        }
    }

    private static function getEncodedChunk(string $hash,int $offset): string{
        $chunk = ByteUtil::byteArray5ToLong($hash,$offset)%100000;
        return sprintf('%05d',$chunk);
    }

}
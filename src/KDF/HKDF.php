<?php
namespace WhisperSystems\LibSignal\KDF;

use AssertionError;
use WhisperSystems\LibSignal\InvalidKeyException;

abstract class HKDF{

    private const HASH_OUTPUT_SIZE  = 32;

    /**
     * @param int $messageVersion
     * @return HKDFv2|HKDFv3
     */
    public static function createFor(int $messageVersion){
        switch($messageVersion){
            case 2:
                return new HKDFv2();
            case 3:
                return new HKDFv3();
            default:
                throw new AssertionError("Unknown version: ".$messageVersion);
        }
    }

    public function deriveSecrets0(string $inputKeyMaterial, string $info,int $outputLength): string{
        $salt = str_repeat("\0",self::HASH_OUTPUT_SIZE);
        return $this->deriveSecrets($inputKeyMaterial,$salt,$info,$outputLength);
    }

    public function deriveSecrets(string $inputKeyMaterial,string $salt,string $info,int $outputLength){
        $prk = $this->extract($salt,$inputKeyMaterial);
        return $this->expand($prk,$info,$outputLength);
    }

    private function extract(string $salt,string $inputKeyMaterial): string{
        return hash_hmac('sha256',$inputKeyMaterial,$salt,true);
    }

    //TODO Implement
    private function expand(string $prk,string $info,int $outputSize): string{
        try{
            $iterations = (int) ceil($outputSize / self::HASH_OUTPUT_SIZE);
            $mixin = '';
            $results = '';
            $remainingBytes = $outputSize;

            for($i=$this->getIterationStartOffset();$i<$iterations+$this->getIterationStartOffset();$i++){
                $data = '';
                $data .= $mixin;
                if($info!==null){
                    $data .= $info;
                }
                $data .= chr($i);

                $stepResult = hash_hmac('sha256',$data,$prk,true);
                $stepSize = min($remainingBytes,strlen($stepResult));

                $results .= substr($stepResult,0,$stepSize);

                $mixin = $stepResult;
                $remainingBytes -= $stepSize;
            }

            return $results;
        }catch(InvalidKeyException $e){
            throw new AssertionError($e);
        }
  }

    protected abstract function getIterationStartOffset(): int;

}
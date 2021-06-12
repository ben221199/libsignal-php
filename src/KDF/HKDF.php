<?php
namespace WhisperSystems\LibSignal\KDF;

use AssertionError;
use Libsignal\exceptions\InvalidKeyException;

abstract class HKDF{

    private const HASH_OUTPUT_SIZE  = 32;

    /**
     * @param int $messageVersion
     * @return HKDFv2|HKDFv3
     */
    public function createFor(int $messageVersion){
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
            //int                   remainingBytes = outputSize;
            for($i=$this->getIterationStartOffset();$i<$iterations+$this->getIterationStartOffset();$i++){
//                hash_hmac()
//                Mac mac = Mac.getInstance("HmacSHA256");
//                mac.init(new SecretKeySpec(prk, "HmacSHA256"));
//
//                mac.update(mixin);
//                if (info != null) {
//                    mac.update(info);
//                }
//                mac.update((byte)i);
//
//                byte[] stepResult = mac.doFinal();
//                int    stepSize   = Math.min(remainingBytes, stepResult.length);
//
//                results.write(stepResult, 0, stepSize);
//
//                mixin          = stepResult;
//                remainingBytes -= stepSize;
            }

            return $results;
        }catch(NoSuchAlgorithmException | InvalidKeyException $e){
            throw new AssertionError($e);
        }
  }

    protected abstract function getIterationStartOffset(): int;

}
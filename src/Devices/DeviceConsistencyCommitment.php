<?php
namespace WhisperSystems\LibSignal\Devices;

use AssertionError;
use Exception;
use WhisperSystems\LibSignal\IdentityKey;
use WhisperSystems\LibSignal\Util\ByteUtil;

class DeviceConsistencyCommitment{

    private const VERSION = 'DeviceConsistencyCommitment_V0';

    /**
     * @var int $generation
     */
    private $generation;
    /**
     * @var string $serialized
     */
    private $serialized;


    /**
     * DeviceConsistencyCommitment constructor.
     * @param int $generation
     * @param IdentityKey[]|array $identityKeys
     */
    public function __construct(int $generation,array $identityKeys){
        try{
            $sortedIdentityKeys = $identityKeys;
            sort($sortedIdentityKeys);//TODO new IdentityKeyComparator()

            $data = '';
            $data .= self::VERSION;
            $data .= ByteUtil::intToByteArray($generation);

            foreach($sortedIdentityKeys AS $commitment){
                $data .= $commitment->getPublicKey()->serialize();
            }

            $this->generation = $generation;
            $this->serialized = hash('sha512',$data,true);
        }catch(Exception $e){
            throw new AssertionError($e);
        }
    }

    public function toByteArray(): string{
        return $this->serialized;
    }

    public function getGeneration(): int{
        return $this->generation;
    }

}
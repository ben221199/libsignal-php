<?php
namespace WhisperSystems\LibSignal\State\Impl;

use AssertionError;
use Exception;
use WhisperSystems\LibSignal\InvalidKeyIdException;
use WhisperSystems\LibSignal\State\PreKeyRecord;
use WhisperSystems\LibSignal\State\PreKeyStore;

class InMemoryPreKeyStore implements PreKeyStore{

    private $store = [];

    public function loadPreKey(int $preKeyId) : PreKeyRecord{
        try{
            if(!array_key_exists($preKeyId,$this->store)){
                throw new InvalidKeyIdException("No such prekeyrecord!");
            }

            return new PreKeyRecord($this->store[$preKeyId]);
        }catch(Exception $e){
            throw new AssertionError($e);
        }
    }

    public function storePreKey(int $preKeyId,PreKeyRecord $record): void{
        $this->store[$preKeyId] = $record->serialize();
    }

    public function containsPreKey(int $preKeyId): bool{
        return array_key_exists($preKeyId,$this->store);
    }

    public function removePreKey(int $preKeyId): void{
        unset($this->store[$preKeyId]);
    }

}
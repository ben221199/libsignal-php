<?php
namespace WhisperSystems\LibSignal\State\Impl;

use AssertionError;
use Exception;
use WhisperSystems\LibSignal\InvalidKeyIdException;
use WhisperSystems\LibSignal\State\SignedPreKeyRecord;
use WhisperSystems\LibSignal\State\SignedPreKeyStore;

class InMemorySignedPreKeyStore implements SignedPreKeyStore{

    /**
     * @var string[]|array
     */
    private $store = [];

    /**
     * Load a local SignedPreKeyRecord.
     * @param int $signedPreKeyId the ID of the local SignedPreKeyRecord.
     * @return SignedPreKeyRecord the corresponding SignedPreKeyRecord.
     * @throws InvalidKeyIdException when there is no corresponding SignedPreKeyRecord.
     */
    public function loadSignedPreKey(int $signedPreKeyId): SignedPreKeyRecord{
        try{
            if(!isset($this->store[$signedPreKeyId])){
                throw new InvalidKeyIdException("No such signedprekeyrecord! " . $signedPreKeyId);
            }

            return new SignedPreKeyRecord($this->store[$signedPreKeyId]);
        }catch(Exception $e){
            throw new AssertionError($e);
        }
    }

    /**
     * Load all local SignedPreKeyRecords.
     * @return SignedPreKeyRecord[]|array All stored SignedPreKeyRecords.
     */
    public function loadSignedPreKeys(): array{
        try{
            $results = [];

            foreach($this->store AS $serialized){
                $results[] = new SignedPreKeyRecord($serialized);
            }

            return $results;
        }catch(Exception $e) {
            throw new AssertionError($e);
        }
    }

    /**
     * Store a local SignedPreKeyRecord.
     * @param int $signedPreKeyId the ID of the SignedPreKeyRecord to store.
     * @param SignedPreKeyRecord $record the SignedPreKeyRecord.
     */
    public function storeSignedPreKey(int $signedPreKeyId,SignedPreKeyRecord $record): void{
        $this->store[$signedPreKeyId] = $record->serialize();
    }

    /**
     * @param int $signedPreKeyId A SignedPreKeyRecord ID.
     * @return bool true if the store has a record for the signedPreKeyId, otherwise false.
     */
    public function containsSignedPreKey(int $signedPreKeyId): bool{
        return array_key_exists($signedPreKeyId,$this->store);
    }

    /**
     * Delete a SignedPreKeyRecord from local storage.
     * @param int $signedPreKeyId The ID of the SignedPreKeyRecord to remove.
     */
    public function removeSignedPreKey(int $signedPreKeyId): void{
        unset($this->store[$signedPreKeyId]);
    }

}
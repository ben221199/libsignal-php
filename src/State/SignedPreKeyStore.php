<?php
namespace WhisperSystems\LibSignal\State;

use Libsignal\exceptions\InvalidKeyIdException;

interface SignedPreKeyStore{

    /**
     * Load a local SignedPreKeyRecord.
     * @param int $signedPreKeyId the ID of the local SignedPreKeyRecord.
     * @return SignedPreKeyRecord the corresponding SignedPreKeyRecord.
     * @throws InvalidKeyIdException when there is no corresponding SignedPreKeyRecord.
     */
    public function loadSignedPreKey(int $signedPreKeyId): SignedPreKeyRecord;

    /**
     * Load all local SignedPreKeyRecords.
     * @return SignedPreKeyRecord[]|array All stored SignedPreKeyRecords.
     */
    public function loadSignedPreKeys(): array;

    /**
     * Store a local SignedPreKeyRecord.
     * @param int $signedPreKeyId the ID of the SignedPreKeyRecord to store.
     * @param SignedPreKeyRecord $record the SignedPreKeyRecord.
     */
    public function storeSignedPreKey(int $signedPreKeyId,SignedPreKeyRecord $record): void;

    /**
     * @param int $signedPreKeyId A SignedPreKeyRecord ID.
     * @return bool true if the store has a record for the signedPreKeyId, otherwise false.
     */
    public function containsSignedPreKey(int $signedPreKeyId): bool;

    /**
     * Delete a SignedPreKeyRecord from local storage.
     * @param int $signedPreKeyId The ID of the SignedPreKeyRecord to remove.
     */
    public function removeSignedPreKey(int $signedPreKeyId): void;

}
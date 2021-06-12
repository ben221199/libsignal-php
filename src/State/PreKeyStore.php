<?php
namespace WhisperSystems\LibSignal\State;

interface PreKeyStore{

    /**
     * Load a local PreKeyRecord.
     * @param int $preKeyId the ID of the local PreKeyRecord.
     * @return PreKeyRecord
     * @throws InvalidKeyIdException when there is no corresponding PreKeyRecord.
     */
    public function loadPreKey(int $preKeyId): PreKeyRecord;

    /**
     * Store a local PreKeyRecord.
     * @param int $preKeyId the ID of the PreKeyRecord to store.
     * @param PreKeyRecord $record the PreKeyRecord.
     */
    public function storePreKey(int $preKeyId,PreKeyRecord $record): void;

    /**
     * @param int $preKeyId A PreKeyRecord ID.
     * @return bool true if the store has a record for the preKeyId, otherwise false.
     */
    public function containsPreKey(int $preKeyId): bool;

    /**
     * Delete a PreKeyRecord from local storage.
     * @param int $preKeyId The ID of the PreKeyRecord to remove.
     */
    public function removePreKey(int $preKeyId): void;

}
<?php
namespace WhisperSystems\LibSignal\State;

use WhisperSystems\LibSignal\SignalProtocolAddress;

interface SessionStore{

    /**
     * Returns a copy of the {@link SessionRecord} corresponding to the recipientId + deviceId tuple,
     * or a new SessionRecord if one does not currently exist.
     * <p>
     * It is important that implementations return a copy of the current durable information.  The
     * returned SessionRecord may be modified, but those changes should not have an effect on the
     * durable session state (what is returned by subsequent calls to this method) without the
     * store method being called here first.
     * @param SignalProtocolAddress $address The name and device ID of the remote client.
     * @return SessionRecord a copy of the SessionRecord corresponding to the recipientId + deviceId tuple, or
     * a new SessionRecord if one does not currently exist.
     */
    public function loadSession(SignalProtocolAddress $address): SessionRecord;

    /**
     * Returns all known devices with active sessions for a recipient
     * @param string $name the name of the client.
     * @return int[]|array all known sub-devices with active sessions.
     */
    public function getSubDeviceSessions(string $name): array;

    /**
     * Commit to storage the {@link SessionRecord} for a given recipientId + deviceId tuple.
     * @param SignalProtocolAddress $address the address of the remote client.
     * @param SessionRecord $record the current SessionRecord for the remote client.
     */
    public function storeSession(SignalProtocolAddress $address, SessionRecord $record): void;

    /**
     * Determine whether there is a committed {@link SessionRecord} for a recipientId + deviceId tuple.
     * @param SignalProtocolAddress $address the address of the remote client.
     * @return bool true if a {@link SessionRecord} exists, false otherwise.
     */
    public function containsSession(SignalProtocolAddress $address): bool;

    /**
     * Remove a {@link SessionRecord} for a recipientId + deviceId tuple.
     * @param SignalProtocolAddress $address the address of the remote client.
     */
    public function deleteSession(SignalProtocolAddress $address): void;

    /**
     * Remove the {@link SessionRecord}s corresponding to all devices of a recipientId.
     * @param String $name the name of the remote client.
     */
    public function deleteAllSessions(String $name): void;

}
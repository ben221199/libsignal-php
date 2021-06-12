<?php
namespace WhisperSystems\LibSignal\State;

interface IdentityKeyStore{

    /**
     * Get the local client's identity key pair.
     * @return IdentityKeyPair The local client's persistent identity key pair.
     */
    public function getIdentityKeyPair(): IdentityKeyPair;

    /**
     * Return the local client's registration ID.
     * <p>
     * Clients should maintain a registration ID, a random number
     * between 1 and 16380 that's generated once at install time.
     * @return int the local client's registration ID.
     */
    public function getLocalRegistrationId(): int;

    /**
     * Save a remote client's identity key
     * <p>
     * Store a remote client's identity key as trusted.
     * @param SignalProtocolAddress $address The address of the remote client.
     * @param IdentityKey $identityKey The remote client's identity key.
     * @return bool True if the identity key replaces a previous identity, false if not
     */
    public function saveIdentity(SignalProtocolAddress $address,IdentityKey $identityKey): bool;

    /**
     * Verify a remote client's identity key.
     * <p>
     * Determine whether a remote client's identity is trusted.  Convention is
     * that the Signal Protocol is 'trust on first use.'  This means that
     * an identity key is considered 'trusted' if there is no entry for the recipient
     * in the local store, or if it matches the saved key for a recipient in the local
     * store.  Only if it mismatches an entry in the local store is it considered
     * 'untrusted.'
     *
     * Clients may wish to make a distinction as to how keys are trusted based on the
     * direction of travel. For instance, clients may wish to accept all 'incoming' identity
     * key changes, while only blocking identity key changes when sending a message.
     * @param SignalProtocolAddress $address The address of the remote client.
     * @param IdentityKey $identityKey The identity key to verify.
     * @param IdentityKeyStore_Direction $direction The direction (sending or receiving) this identity is being used for.
     * @return bool true if trusted, false if untrusted.
     */
    public function isTrustedIdentity(SignalProtocolAddress $address,IdentityKey $identityKey,IdentityKeyStore_Direction $direction): bool;

    /**
     * Return the saved public identity key for a remote client
     * @param SignalProtocolAddress $address The address of the remote client
     * @return IdentityKey The public identity key, or null if absent
     */
    public function getIdentity(SignalProtocolAddress $address): IdentityKey;

}
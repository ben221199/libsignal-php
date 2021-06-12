<?php
namespace WhisperSystems\LibSignal\State;

interface SignalProtocolStore extends IdentityKeyStore,PreKeyStore,SessionStore,SignedPreKeyStore{

}
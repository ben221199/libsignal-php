<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: protobuf/WhisperTextProtocol.proto

namespace GPBMetadata\Protobuf;

class WhisperTextProtocol
{
    public static $is_initialized = false;

    public static function initOnce() {
        $pool = \Google\Protobuf\Internal\DescriptorPool::getGeneratedPool();

        if (static::$is_initialized == true) {
          return;
        }
        $pool->internalAddGeneratedFile(
            '
Κ	
"protobuf/WhisperTextProtocol.proto
textsecure"³
SignalMessage

ratchetKey (H 
counter (H
previousCounter (H

ciphertext (HB
_ratchetKeyB

_counterB
_previousCounterB
_ciphertext"
PreKeySignalMessage
registrationId (H 
preKeyId (H
signedPreKeyId (H
baseKey (H
identityKey (H
message (HB
_registrationIdB
	_preKeyIdB
_signedPreKeyIdB

_baseKeyB
_identityKeyB

_message"Τ
KeyExchangeMessage
id (H 
baseKey (H

ratchetKey (H
identityKey (H
baseKeySignature (HB
_idB

_baseKeyB
_ratchetKeyB
_identityKeyB
_baseKeySignature"x
SenderKeyMessage
id (H 
	iteration (H

ciphertext (HB
_idB

_iterationB
_ciphertext"¨
SenderKeyDistributionMessage
id (H 
	iteration (H
chainKey (H

signingKey (HB
_idB

_iterationB
	_chainKeyB
_signingKey"l
DeviceConsistencyCodeMessage

generation (H 
	signature (HB
_generationB

_signatureBf
%org.whispersystems.libsignal.protocolBSignalProtosΚ.WhisperSystems\\LibSignal\\Protocol\\SignalProtosbproto3'
        , true);

        static::$is_initialized = true;
    }
}


<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: protobuf/WhisperTextProtocol.proto

namespace WhisperSystems\LibSignal\Protocol\SignalProtos;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>textsecure.PreKeySignalMessage</code>
 */
class PreKeySignalMessage extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>optional uint32 registrationId = 5;</code>
     */
    protected $registrationId = null;
    /**
     * Generated from protobuf field <code>optional uint32 preKeyId = 1;</code>
     */
    protected $preKeyId = null;
    /**
     * Generated from protobuf field <code>optional uint32 signedPreKeyId = 6;</code>
     */
    protected $signedPreKeyId = null;
    /**
     * Generated from protobuf field <code>optional bytes baseKey = 2;</code>
     */
    protected $baseKey = null;
    /**
     * Generated from protobuf field <code>optional bytes identityKey = 3;</code>
     */
    protected $identityKey = null;
    /**
     * SignalMessage
     *
     * Generated from protobuf field <code>optional bytes message = 4;</code>
     */
    protected $message = null;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type int $registrationId
     *     @type int $preKeyId
     *     @type int $signedPreKeyId
     *     @type string $baseKey
     *     @type string $identityKey
     *     @type string $message
     *           SignalMessage
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Protobuf\WhisperTextProtocol::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>optional uint32 registrationId = 5;</code>
     * @return int
     */
    public function getRegistrationId()
    {
        return isset($this->registrationId) ? $this->registrationId : 0;
    }

    public function hasRegistrationId()
    {
        return isset($this->registrationId);
    }

    public function clearRegistrationId()
    {
        unset($this->registrationId);
    }

    /**
     * Generated from protobuf field <code>optional uint32 registrationId = 5;</code>
     * @param int $var
     * @return $this
     */
    public function setRegistrationId($var)
    {
        GPBUtil::checkUint32($var);
        $this->registrationId = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>optional uint32 preKeyId = 1;</code>
     * @return int
     */
    public function getPreKeyId()
    {
        return isset($this->preKeyId) ? $this->preKeyId : 0;
    }

    public function hasPreKeyId()
    {
        return isset($this->preKeyId);
    }

    public function clearPreKeyId()
    {
        unset($this->preKeyId);
    }

    /**
     * Generated from protobuf field <code>optional uint32 preKeyId = 1;</code>
     * @param int $var
     * @return $this
     */
    public function setPreKeyId($var)
    {
        GPBUtil::checkUint32($var);
        $this->preKeyId = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>optional uint32 signedPreKeyId = 6;</code>
     * @return int
     */
    public function getSignedPreKeyId()
    {
        return isset($this->signedPreKeyId) ? $this->signedPreKeyId : 0;
    }

    public function hasSignedPreKeyId()
    {
        return isset($this->signedPreKeyId);
    }

    public function clearSignedPreKeyId()
    {
        unset($this->signedPreKeyId);
    }

    /**
     * Generated from protobuf field <code>optional uint32 signedPreKeyId = 6;</code>
     * @param int $var
     * @return $this
     */
    public function setSignedPreKeyId($var)
    {
        GPBUtil::checkUint32($var);
        $this->signedPreKeyId = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>optional bytes baseKey = 2;</code>
     * @return string
     */
    public function getBaseKey()
    {
        return isset($this->baseKey) ? $this->baseKey : '';
    }

    public function hasBaseKey()
    {
        return isset($this->baseKey);
    }

    public function clearBaseKey()
    {
        unset($this->baseKey);
    }

    /**
     * Generated from protobuf field <code>optional bytes baseKey = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setBaseKey($var)
    {
        GPBUtil::checkString($var, False);
        $this->baseKey = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>optional bytes identityKey = 3;</code>
     * @return string
     */
    public function getIdentityKey()
    {
        return isset($this->identityKey) ? $this->identityKey : '';
    }

    public function hasIdentityKey()
    {
        return isset($this->identityKey);
    }

    public function clearIdentityKey()
    {
        unset($this->identityKey);
    }

    /**
     * Generated from protobuf field <code>optional bytes identityKey = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setIdentityKey($var)
    {
        GPBUtil::checkString($var, False);
        $this->identityKey = $var;

        return $this;
    }

    /**
     * SignalMessage
     *
     * Generated from protobuf field <code>optional bytes message = 4;</code>
     * @return string
     */
    public function getMessage()
    {
        return isset($this->message) ? $this->message : '';
    }

    public function hasMessage()
    {
        return isset($this->message);
    }

    public function clearMessage()
    {
        unset($this->message);
    }

    /**
     * SignalMessage
     *
     * Generated from protobuf field <code>optional bytes message = 4;</code>
     * @param string $var
     * @return $this
     */
    public function setMessage($var)
    {
        GPBUtil::checkString($var, False);
        $this->message = $var;

        return $this;
    }

}


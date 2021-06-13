<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: protobuf/LocalStorageProtocol.proto

namespace WhisperSystems\LibSignal\State\StorageProtos\SessionStructure;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>textsecure.SessionStructure.PendingPreKey</code>
 */
class PendingPreKey extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>optional uint32 preKeyId = 1;</code>
     */
    protected $preKeyId = null;
    /**
     * Generated from protobuf field <code>optional int32 signedPreKeyId = 3;</code>
     */
    protected $signedPreKeyId = null;
    /**
     * Generated from protobuf field <code>optional bytes baseKey = 2;</code>
     */
    protected $baseKey = null;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type int $preKeyId
     *     @type int $signedPreKeyId
     *     @type string $baseKey
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Protobuf\LocalStorageProtocol::initOnce();
        parent::__construct($data);
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
     * Generated from protobuf field <code>optional int32 signedPreKeyId = 3;</code>
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
     * Generated from protobuf field <code>optional int32 signedPreKeyId = 3;</code>
     * @param int $var
     * @return $this
     */
    public function setSignedPreKeyId($var)
    {
        GPBUtil::checkInt32($var);
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

}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PendingPreKey::class, \WhisperSystems\LibSignal\State\StorageProtos\SessionStructure_PendingPreKey::class);


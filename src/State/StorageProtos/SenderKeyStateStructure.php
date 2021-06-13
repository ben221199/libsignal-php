<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: protobuf/LocalStorageProtocol.proto

namespace WhisperSystems\LibSignal\State\StorageProtos;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>textsecure.SenderKeyStateStructure</code>
 */
class SenderKeyStateStructure extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>optional uint32 senderKeyId = 1;</code>
     */
    protected $senderKeyId = null;
    /**
     * Generated from protobuf field <code>optional .textsecure.SenderKeyStateStructure.SenderChainKey senderChainKey = 2;</code>
     */
    protected $senderChainKey = null;
    /**
     * Generated from protobuf field <code>optional .textsecure.SenderKeyStateStructure.SenderSigningKey senderSigningKey = 3;</code>
     */
    protected $senderSigningKey = null;
    /**
     * Generated from protobuf field <code>repeated .textsecure.SenderKeyStateStructure.SenderMessageKey senderMessageKeys = 4;</code>
     */
    private $senderMessageKeys;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type int $senderKeyId
     *     @type \WhisperSystems\LibSignal\State\StorageProtos\SenderKeyStateStructure\SenderChainKey $senderChainKey
     *     @type \WhisperSystems\LibSignal\State\StorageProtos\SenderKeyStateStructure\SenderSigningKey $senderSigningKey
     *     @type \WhisperSystems\LibSignal\State\StorageProtos\SenderKeyStateStructure\SenderMessageKey[]|\Google\Protobuf\Internal\RepeatedField $senderMessageKeys
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Protobuf\LocalStorageProtocol::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>optional uint32 senderKeyId = 1;</code>
     * @return int
     */
    public function getSenderKeyId()
    {
        return isset($this->senderKeyId) ? $this->senderKeyId : 0;
    }

    public function hasSenderKeyId()
    {
        return isset($this->senderKeyId);
    }

    public function clearSenderKeyId()
    {
        unset($this->senderKeyId);
    }

    /**
     * Generated from protobuf field <code>optional uint32 senderKeyId = 1;</code>
     * @param int $var
     * @return $this
     */
    public function setSenderKeyId($var)
    {
        GPBUtil::checkUint32($var);
        $this->senderKeyId = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>optional .textsecure.SenderKeyStateStructure.SenderChainKey senderChainKey = 2;</code>
     * @return \WhisperSystems\LibSignal\State\StorageProtos\SenderKeyStateStructure\SenderChainKey|null
     */
    public function getSenderChainKey()
    {
        return isset($this->senderChainKey) ? $this->senderChainKey : null;
    }

    public function hasSenderChainKey()
    {
        return isset($this->senderChainKey);
    }

    public function clearSenderChainKey()
    {
        unset($this->senderChainKey);
    }

    /**
     * Generated from protobuf field <code>optional .textsecure.SenderKeyStateStructure.SenderChainKey senderChainKey = 2;</code>
     * @param \WhisperSystems\LibSignal\State\StorageProtos\SenderKeyStateStructure\SenderChainKey $var
     * @return $this
     */
    public function setSenderChainKey($var)
    {
        GPBUtil::checkMessage($var, \WhisperSystems\LibSignal\State\StorageProtos\SenderKeyStateStructure\SenderChainKey::class);
        $this->senderChainKey = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>optional .textsecure.SenderKeyStateStructure.SenderSigningKey senderSigningKey = 3;</code>
     * @return \WhisperSystems\LibSignal\State\StorageProtos\SenderKeyStateStructure\SenderSigningKey|null
     */
    public function getSenderSigningKey()
    {
        return isset($this->senderSigningKey) ? $this->senderSigningKey : null;
    }

    public function hasSenderSigningKey()
    {
        return isset($this->senderSigningKey);
    }

    public function clearSenderSigningKey()
    {
        unset($this->senderSigningKey);
    }

    /**
     * Generated from protobuf field <code>optional .textsecure.SenderKeyStateStructure.SenderSigningKey senderSigningKey = 3;</code>
     * @param \WhisperSystems\LibSignal\State\StorageProtos\SenderKeyStateStructure\SenderSigningKey $var
     * @return $this
     */
    public function setSenderSigningKey($var)
    {
        GPBUtil::checkMessage($var, \WhisperSystems\LibSignal\State\StorageProtos\SenderKeyStateStructure\SenderSigningKey::class);
        $this->senderSigningKey = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>repeated .textsecure.SenderKeyStateStructure.SenderMessageKey senderMessageKeys = 4;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getSenderMessageKeys()
    {
        return $this->senderMessageKeys;
    }

    /**
     * Generated from protobuf field <code>repeated .textsecure.SenderKeyStateStructure.SenderMessageKey senderMessageKeys = 4;</code>
     * @param \WhisperSystems\LibSignal\State\StorageProtos\SenderKeyStateStructure\SenderMessageKey[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setSenderMessageKeys($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \WhisperSystems\LibSignal\State\StorageProtos\SenderKeyStateStructure\SenderMessageKey::class);
        $this->senderMessageKeys = $arr;

        return $this;
    }

}


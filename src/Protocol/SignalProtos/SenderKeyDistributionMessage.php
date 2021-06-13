<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: protobuf/WhisperTextProtocol.proto

namespace WhisperSystems\LibSignal\Protocol\SignalProtos;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>textsecure.SenderKeyDistributionMessage</code>
 */
class SenderKeyDistributionMessage extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>optional uint32 id = 1;</code>
     */
    protected $id = null;
    /**
     * Generated from protobuf field <code>optional uint32 iteration = 2;</code>
     */
    protected $iteration = null;
    /**
     * Generated from protobuf field <code>optional bytes chainKey = 3;</code>
     */
    protected $chainKey = null;
    /**
     * Generated from protobuf field <code>optional bytes signingKey = 4;</code>
     */
    protected $signingKey = null;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type int $id
     *     @type int $iteration
     *     @type string $chainKey
     *     @type string $signingKey
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Protobuf\WhisperTextProtocol::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>optional uint32 id = 1;</code>
     * @return int
     */
    public function getId()
    {
        return isset($this->id) ? $this->id : 0;
    }

    public function hasId()
    {
        return isset($this->id);
    }

    public function clearId()
    {
        unset($this->id);
    }

    /**
     * Generated from protobuf field <code>optional uint32 id = 1;</code>
     * @param int $var
     * @return $this
     */
    public function setId($var)
    {
        GPBUtil::checkUint32($var);
        $this->id = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>optional uint32 iteration = 2;</code>
     * @return int
     */
    public function getIteration()
    {
        return isset($this->iteration) ? $this->iteration : 0;
    }

    public function hasIteration()
    {
        return isset($this->iteration);
    }

    public function clearIteration()
    {
        unset($this->iteration);
    }

    /**
     * Generated from protobuf field <code>optional uint32 iteration = 2;</code>
     * @param int $var
     * @return $this
     */
    public function setIteration($var)
    {
        GPBUtil::checkUint32($var);
        $this->iteration = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>optional bytes chainKey = 3;</code>
     * @return string
     */
    public function getChainKey()
    {
        return isset($this->chainKey) ? $this->chainKey : '';
    }

    public function hasChainKey()
    {
        return isset($this->chainKey);
    }

    public function clearChainKey()
    {
        unset($this->chainKey);
    }

    /**
     * Generated from protobuf field <code>optional bytes chainKey = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setChainKey($var)
    {
        GPBUtil::checkString($var, False);
        $this->chainKey = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>optional bytes signingKey = 4;</code>
     * @return string
     */
    public function getSigningKey()
    {
        return isset($this->signingKey) ? $this->signingKey : '';
    }

    public function hasSigningKey()
    {
        return isset($this->signingKey);
    }

    public function clearSigningKey()
    {
        unset($this->signingKey);
    }

    /**
     * Generated from protobuf field <code>optional bytes signingKey = 4;</code>
     * @param string $var
     * @return $this
     */
    public function setSigningKey($var)
    {
        GPBUtil::checkString($var, False);
        $this->signingKey = $var;

        return $this;
    }

}


<?php
namespace WhisperSystems\LibSignal\State\Impl;

use AssertionError;
use Exception;
use WhisperSystems\LibSignal\SignalProtocolAddress;
use WhisperSystems\LibSignal\State\SessionRecord;
use WhisperSystems\LibSignal\State\SessionStore;

class InMemorySessionStore implements SessionStore{

    private $sessions = [];

    public function __construct(){}

    public function loadSession(SignalProtocolAddress $remoteAddress): SessionRecord{
        try{
            if($this->containsSession($remoteAddress)){
                return new SessionRecord($this->sessions[$remoteAddress->getDeviceId()]);
            }else{
                return new SessionRecord();
            }
        }catch(Exception $e){
            throw new AssertionError($e);
        }
    }

    public function getSubDeviceSessions(String $name): array{
        $deviceIds = [];

        /***@var SignalProtocolAddress $key*/
        foreach($this->sessions AS $key){
            if($key->getName()===$name && $key->getDeviceId()!==1){
                $deviceIds[] = $key->getDeviceId();
            }
        }

        return $deviceIds;
    }

    public function storeSession(SignalProtocolAddress $address,SessionRecord $record): void{
        $this->sessions[$address->getDeviceId()] = $record->serialize();
    }

    public function containsSession(SignalProtocolAddress $address): bool{
        return array_key_exists($address->getDeviceId(),$this->sessions);
    }

    public function deleteSession(SignalProtocolAddress $address): void{
        unset($this->sessions[$address->getDeviceId()]);
    }

    public function deleteAllSessions(String $name): void{
        foreach($this->sessions AS $key){
            if($key->getName()===$name){
                unset($this->sessions[$key]);
            }
        }
    }

}
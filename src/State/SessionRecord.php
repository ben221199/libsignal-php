<?php
namespace WhisperSystems\LibSignal\State;

use WhisperSystems\LibSignal\State\StorageProtos\RecordStructure;

class SessionRecord{

    private const ARCHIVED_STATES_MAX_LENGTH = 40;

    /**
     * @var SessionState $sessionState
     */
    private $sessionState;
    /**
     * @var SessionState[]|array $previousStates
     */
    private $previousStates = [];
    /**
     * @var bool $fresh
     */
    private $fresh = false;

    public function __construct($sessionStateOrSerialized=null){
        if($sessionStateOrSerialized===null){
            $this->sessionState = new SessionState;
            $this->fresh = false;
        }elseif($sessionStateOrSerialized instanceof SessionState){
            $this->sessionState = $sessionStateOrSerialized;
            $this->fresh = false;
        }elseif(is_string($sessionStateOrSerialized)){
            $record = new RecordStructure;
            $record->mergeFrom($sessionStateOrSerialized);
            $this->sessionState = new SessionState($record->getCurrentSession());
            $this->fresh = false;

            foreach($record->getPreviousSessions() AS $previousStructure){
                $this->previousStates[] = new SessionState($previousStructure);
            }
        }
    }

    public function hasSessionState(int $version,string $aliceBaseKey): bool{
        if($this->sessionState->getSessionVersion()===$version && $aliceBaseKey===$this->sessionState->getAliceBaseKey()){
            return true;
        }

        /**@var SessionState $state*/
        foreach($this->previousStates AS $state){
            if ($state->getSessionVersion()===$version && $aliceBaseKey===$state->getAliceBaseKey()){
                return true;
            }
        }

        return false;
    }

    public function getSessionState(): SessionState{
        return $this->sessionState;
    }

    /**
     * @return SessionState[]|array the list of all currently maintained "previous" session states.
     */
    public function getPreviousSessionStates(): array{
        return $this->previousStates;
    }

    public function removePreviousSessionStates(): void{
        $this->previousStates = [];
    }

    public function isFresh(): bool{
        return $this->fresh;
    }

    /**
     * Move the current {@link SessionState} into the list of "previous" session states,
     * and replace the current {@link org.whispersystems.libsignal.state.SessionState}
     * with a fresh reset instance.
     */
    public function archiveCurrentState(): void{
        $this->promoteState(new SessionState());
    }

    public function promoteState(SessionState $promotedState): void{
        array_unshift($this->previousStates,$this->sessionState);
        $this->sessionState = $promotedState;

        if(count($this->previousStates) > self::ARCHIVED_STATES_MAX_LENGTH){
            array_pop($this->previousStates);
        }
    }

    public function setState(SessionState $sessionState): void{
        $this->sessionState = $sessionState;
    }

    /**
     * @return string a serialized version of the current SessionRecord.
     */
    public function serialize(): string{
        $previousStructures = [];

        foreach($this->previousStates AS $previousState){
            $previousStructures[] = $previousState->getStructure();
        }

        $record = (new RecordStructure)
            ->setCurrentSession($this->sessionState->getStructure())
            ->setPreviousSessions($previousStructures);

        return $record->serializeToString();
    }

}
<?php
namespace WhisperSystems\LibSignal\Util\Guava;

use Exception;
use Serializable;

abstract class Optional implements Serializable{

    public static function absent(): Optional{
        return Absent::INSTANCE();
    }

    /**
     * @param $reference
     * @return Optional
     * @throws Exception
     */
    public static function of($reference): Optional{
        return new Present(Preconditions::checkNotNull($reference));
    }

    public static function fromNullable($nullableReference): Optional{
        return ($nullableReference===null)?Optional::absent():new Present($nullableReference);
    }

    function __construct(){
    }

    public abstract function isPresent(): bool;

    public abstract function get();

    public abstract function or($defaultValue);

    public abstract function or2(Optional $secondChoice): Optional;

    public abstract function or3(Supplier $supplier);

    public abstract function orNull();

//    public abstract function asSet(): Set;

    public abstract function transform(Function0 $function): Optional;

    public abstract function equals($object): bool;

    public abstract function __toString();

    public function serialize(){}
    public function unserialize($serialized){}

}
<?php
namespace WhisperSystems\LibSignal\Util\Guava;

use Exception;

class Absent extends Optional{

    static $INSTANCE;

    static function INSTANCE(){
        if(!static::$INSTANCE){
            static::$INSTANCE = new Absent();
        }
        return static::$INSTANCE;
    }

    public function isPresent(): bool{
        return false;
    }

    /**
     * @throws Exception
     */
    public function get(){
        throw new Exception("value is absent");
    }

    /**
     * @param $defaultValue
     * @return mixed
     * @throws Exception
     */
    public function or($defaultValue) {
        return Preconditions::checkNotNull($defaultValue,"use orNull() instead of or(null)");
    }

    /**
     * @param Optional $secondChoice
     * @return Optional
     * @throws Exception
     */
    public function or2(Optional $secondChoice): Optional{
        return Preconditions::checkNotNull($secondChoice);
    }

    /**
     * @param Supplier $supplier
     * @return mixed
     * @throws Exception
     */
    public function or3(Supplier $supplier){
        return Preconditions::checkNotNull($supplier->get(),
            "use orNull() instead of a Supplier that returns null");
    }

    public function orNull(){
        return null;
    }

//    public function asSet(): Set{
//        return Collections::emptySet();
//    }

    /**
     * @param Function0 $function
     * @return Optional
     * @throws Exception
     */
    public function transform(Function0 $function): Optional{
        Preconditions::checkNotNull($function);
        return Optional::absent();
    }

    public function equals($object): bool{
        return $object===$this;
    }

    public function __toString(){
        return "Optional.absent()";
    }

}
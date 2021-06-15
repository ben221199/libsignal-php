<?php
namespace WhisperSystems\LibSignal\Util\Guava;

use Exception;

class Present extends Optional{

    private $reference;

    function __construct($reference){
        $this->reference = $reference;
    }

    public function isPresent(): bool{
        return true;
    }

    public function get(){
        return $this->reference;
    }

    /**
     * @param $defaultValue
     * @return mixed
     * @throws Exception
     */
    public function or($defaultValue){
        Preconditions::checkNotNull($defaultValue, "use orNull() instead of or(null)");
        return $this->reference;
    }

    /**
     * @param Optional $secondChoice
     * @return Optional
     * @throws Exception
     */
    public function or2(Optional $secondChoice): Optional{
        Preconditions::checkNotNull($secondChoice);
        return $this;
    }

    /**
     * @param Supplier $supplier
     * @return mixed
     * @throws Exception
     */
    public function or3(Supplier $supplier){
        Preconditions::checkNotNull($supplier);
        return $this->reference;
    }

    public function orNull(){
        return $this->reference;
    }

//    public function asSet(): Set{
//        return Collections::singleton($this->reference);
//    }

    /**
     * @param Function0 $function
     * @return Optional
     * @throws Exception
     */
    public function transform(Function0 $function): Optional{
        return new Present(Preconditions::checkNotNull($function->apply($this->reference),
            "Transformation function cannot return null."));
    }

    public function equals($object): bool{
        if($object instanceof Present){
            /**@var Present $other*/
            $other = $object;
            return $this->reference->equals($other->reference);
        }
        return false;
    }

    public function __toString(): string{
        return "Optional.of(" . $this->reference . ")";
    }

}
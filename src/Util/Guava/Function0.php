<?php
namespace WhisperSystems\LibSignal\Util\Guava;

interface Function0{

    function apply($input);

    function equals($object): bool;

}
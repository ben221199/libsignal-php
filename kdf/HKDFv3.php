<?php
class HKDFv3 extends HKDF {
    protected function getIterationStartOffset ()
    {
        return 1;
    }
}

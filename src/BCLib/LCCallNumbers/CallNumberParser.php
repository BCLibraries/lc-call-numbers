<?php

namespace BCLib\LCCallNumbers;

use BCLib\LCCallNumbers\LCCallNumber;

interface CallNumberParser
{
    public function parse($input_string, LCCallNumber $call_number);
}

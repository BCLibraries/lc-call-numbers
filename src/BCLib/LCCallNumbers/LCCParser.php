<?php

namespace BCLib\LCCallNumbers;

interface LCCParser
{
    public function parse(LCCallNumber $lccn);
}

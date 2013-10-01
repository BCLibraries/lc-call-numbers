<?php

namespace BCLib\BiblioTools\LCC;

interface LCCParser
{
    public function parse(LCCallNumber $lccn);
}

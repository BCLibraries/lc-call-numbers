<?php

namespace BCLib\LCCallNumbers;

class CallNumberFactory
{
    public function create()
    {
        return new LCCallNumber();
    }

    public function createParser()
    {
        return new RegExCallNumberParser();
    }
}
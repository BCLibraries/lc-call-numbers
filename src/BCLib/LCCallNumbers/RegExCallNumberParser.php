<?php

namespace BCLib\LCCallNumbers;

/**
 * Parses LC call numbers using a regular expression
 *
 * Steals liberally from a regular expression developed by Bill Dueber
 * (https://code.google.com/p/library-callnumber-lc/wiki/Home).
 *
 * Class RegExCallNumberParser
 * @package BCLib\LCCallNumbers
 */
class RegExCallNumberParser implements CallNumberParser
{
    protected $_matches = array();

    public function parse($input_string, LCCallNumber $call)
    {
        $regex = <<<REGEX
        /^\s*
        ([A-Z]{1,3})  # alpha
        \s*
        (?:         # optional numbers with optional decimal point
          (\d+)
          (?:\s*?\.\s*?(\d+))?
        )?
        \s*
        (\d+[stndrh]*)? # optional extra numbering including suffixes (1st, 2nd, etc.)
            \s*
            (?:               # optional cutter
          \.? \s*
              ([A-Z])      # cutter letter
              \s*
              (\d+\w? | \Z)??        # cutter numbers
        )?
        \s*
        (?:               # optional cutter
          \.? \s*
              ([A-Z])      # cutter letter
              \s*
              (\d+[\w|\Z]|\d)?        # cutter numbers
        )?
        \s*
        (?:               # optional cutter
          \.? \s*
              ([A-Z])      # cutter letter
              \s*
              (\d+[\w|\Z]|\d)?        # cutter numbers
        )?
        (\s+.+?)?        # everthing else
            \s*$
  /x
REGEX;
        $this->_matches = array_fill(0, 12, '');
        preg_match($regex, $input_string, $this->_matches);

        $call->letters = isset($this->_matches[1]) ? $this->_matches[1] : false;

        $call->number = isset($this->_matches[2]) ? $this->_matches[2] : false;
        if (isset($this->_matches[3])) {
            $call->number .= "." . $this->_matches[3];
        }

        $call->class_year = isset($this->_matches[4]) ? $this->_matches[4] : false;

        $call->cutter_1 = $this->_buildCutter(5, 6);
        $call->cutter_2 = $this->_buildCutter(7, 8);
        $call->cutter_3 = $this->_buildCutter(9, 10);
        $call->remainder = isset($this->_matches[11]) ? trim($this->_matches[11]) : false;

        $this->_matches = array();

        return ($call->letters && preg_match(
                '/^[A-Z]/',
                $call->letters
            ) && $call->number && $call->cutter_1);

    }

    protected function _buildCutter($letter_index, $number_index)
    {
        if (isset($this->_matches[$letter_index]) && isset($this->_matches[$number_index])) {
            return $this->_matches[$letter_index] . $this->_matches[$number_index];
        }
        return false;
    }

}
<?php

namespace BCLib\BiblioTools\LCC;

/**
 * Class LCCParser
 * @package BCLib\BiblioTools\LCC
 *
 * Call number regular expression lifted from Bill Dueber
 * https://code.google.com/p/library-callnumber-lc/wiki/Home
 */
class LCCRegexParser implements LCCParser
{
    protected $_matches = array();

    public function parse(LCCallNumber $lccn)
    {
        if (is_null($lccn->input_string)) {
            throw new \Exception("No LCCN string to parse");
        }

        $lccregex = <<<REGEX
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
              (\d+\w | \Z)?        # cutter numbers
        )?
        \s*
        (?:               # optional cutter
          \.? \s*
              ([A-Z])      # cutter letter
              \s*
              (\d+\w | \Z)?        # cutter numbers
        )?
        (\s+.+?)?        # everthing else
            \s*$
  /x
REGEX;
        $this->_matches = array_fill(0, 12, '');
        preg_match($lccregex, $lccn->input_string, $this->_matches);

        $lccn->letters = isset($this->_matches[1]) ? $this->_matches[1] : false;
        $lccn->number = isset($this->_matches[2]) ? $this->_matches[2] : false;
        if (isset($this->_matches[3])) {
            $lccn->number .= "." . $this->_matches[3];
        }
        $lccn->cutter_1 = $this->_buildCutter(5, 6);
        $lccn->cutter_2 = $this->_buildCutter(7, 8);
        $lccn->cutter_3 = $this->_buildCutter(9, 10);
        $lccn->remainder = isset($this->_matches[11]) ? $this->_matches[11] : false;

        return $lccn->letters && preg_match('/^[A-Z]/', $lccn->letters) && $lccn->number && $lccn->cutter_1;
    }

    protected function _buildCutter($letter_index, $number_index)
    {
        if (isset($this->_matches[$letter_index]) && isset($this->_matches[$number_index])) {
            return $this->_matches[$letter_index] . $this->_matches[$number_index];
        }
        return false;
    }
}
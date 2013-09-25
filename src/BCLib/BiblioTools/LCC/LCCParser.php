<?php

namespace BCLib\BiblioTools\LCC;

/**
 * Class LCCParser
 * @package BCLib\BiblioTools\LCC
 *
 * Call number regular expression lifted from Bill Dueber
 * https://code.google.com/p/library-callnumber-lc/wiki/Home
 *
 * @property string letters
 * @property string number
 * @property string cutter_1
 * @property string cutter_2
 * @property string cutter_3
 * @property string remainder
 */
class LCCParser
{
    public function parse(LCCallNumber $lccn)
    {
        if (is_null($lccn->input_string)) {
            throw new \Exception("No LCCN string to parse");
        }

        $lccregex = <<<REGEX
        /\s*
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
        $matches = array_fill(0, 12, '');
        $is_lccn = preg_match($lccregex, $lccn->input_string, $matches);

        $lccn->letters = $matches[1];
        $lccn->number = $matches[2] . "." . $matches[3];
        $lccn->cutter_1 = $this->_buildCutter(5, 6);
        $lccn->cutter_2 = $this->_buildCutter(7, 8);
        $lccn->cutter_3 = $this->_buildCutter(9, 10);
        $lccn->remainder = isset($matches[11]) ? $matches[11] : false;

        return $is_lccn;
    }

    protected function _buildCutter($letter_index, $number_index)
    {
        if (isset($this->_matches[$letter_index])) {
            return $this->_matches[$letter_index] . $this->_matches[$number_index];
        }

        return false;
    }
}
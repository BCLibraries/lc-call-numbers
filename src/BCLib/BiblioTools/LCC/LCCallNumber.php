<?php

namespace BCLib\BiblioTools\LCC;

/**
 * Class LCCN
 * @package BCLib\BiblioTools\LCC
 *
 * Parses and normalizes Library of Congress Call Numbers, using an
 * implementaiton shamelessly stolen from Bill Deuber.
 *
 * @property string      letters
 * @property string      number
 * @property string      class_year
 * @property string      cutter_1
 * @property string      cutter_2
 * @property string      cutter_3
 * @property string      remainder
 */
class LCCallNumber
{
    protected $_is_parsed = false;

    protected $_letters;
    protected $_number;
    protected $_class_year;
    protected $_cutters = array();
    protected $_remainder;

    protected $_input_string;
    protected $_normalized_string;

    protected $_is_valid = false;

    protected $_matches = array();

    const HI_SORT_CHAR = '~';
    const LOW_SORT_CHAR = ' ';

    public function normalize()
    {
        return str_pad($this->_letters, 3, LCCallNumber::LOW_SORT_CHAR, STR_PAD_RIGHT) .
        $this->_normalizeNumber() .
        $this->_normalizeCutters() .
        $this->_normalizeRemainder();
    }

    protected function _normalizeNumber()
    {
        // Only normalize numbers to \d\d\d\d.\d\d
        list($pre_dec, $post_dec) = explode('.', $this->_number);
        $pre_dec = substr($pre_dec, 0, 5);
        $post_dec = substr($post_dec, 0, 3);
        $normalized = str_pad($pre_dec, 5, LCCallNumber::LOW_SORT_CHAR, STR_PAD_LEFT);
        return $normalized . str_pad($post_dec, 3, LCCallNumber::LOW_SORT_CHAR, STR_PAD_RIGHT);
    }

    protected function _normalizeCutters()
    {
        $normalized = '';

        for ($i = 1; $i <= 3; $i++) {
            if (isset($this->_cutters[$i])) {
                $cutter = substr($this->_cutters[$i], 0, 4);
                $normalized .= str_pad($cutter, 4, LCCallNumber::LOW_SORT_CHAR, STR_PAD_RIGHT);
            } else {
                $normalized .= str_repeat(LCCallNumber::LOW_SORT_CHAR, 4);
            }
        }
        return $normalized;
    }

    protected function _normalizeRemainder()
    {
        $remainder = substr($this->_remainder, 0, 5);
        return str_pad($remainder, 5, LCCallNumber::LOW_SORT_CHAR, STR_PAD_RIGHT);
    }

    public function isValid()
    {
        return $this->_is_valid;
    }

    public function parse($input_string)
    {

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
        preg_match($lccregex, $input_string, $this->_matches);

        $this->letters = isset($this->_matches[1]) ? $this->_matches[1] : false;
        $this->number = isset($this->_matches[2]) ? $this->_matches[2] : false;
        if (isset($this->_matches[3])) {
            $this->number .= "." . $this->_matches[3];
        }

        $this->class_year = isset($this->_matches[4]) ? $this->_matches[4] : false;

        $this->cutter_1 = $this->_buildCutter(5, 6);
        $this->cutter_2 = $this->_buildCutter(7, 8);
        $this->cutter_3 = $this->_buildCutter(9, 10);
        $this->remainder = isset($this->_matches[11]) ? $this->_matches[11] : false;

        $this->_is_valid = ($this->letters && preg_match(
                '/^[A-Z]/',
                $this->letters
            ) && $this->number && $this->cutter_1);
    }

    protected function _buildCutter($letter_index, $number_index)
    {
        if (isset($this->_matches[$letter_index]) && isset($this->_matches[$number_index])) {
            return $this->_matches[$letter_index] . $this->_matches[$number_index];
        }
        return false;
    }

    protected function _setCutter($index, $cutter)
    {
        $this->_cutters[$index] = $cutter;
    }

    public function __get($name)
    {
        switch ($name) {
            case 'letters':
                return $this->_letters;
            case 'number':
                return $this->_number;
            case 'class_year':
                return $this->_class_year;
            case 'cutter_1':
                return isset($this->_cutters[1]) ? $this->_cutters[1] : false;
            case 'cutter_2':
                return isset($this->_cutters[2]) ? $this->_cutters[2] : false;
            case 'cutter_3':
                return isset($this->_cutters[3]) ? $this->_cutters[3] : false;
            case 'remainder':
                return $this->_remainder;
            default:
                throw new \Exception("$name is not a LCCN property");
        }
    }

    public function __set($name, $value)
    {
        switch ($name) {
            case 'letters':
                $this->_letters = strtoupper($value);
                break;
            case 'number':
                $this->_number = $value;
                break;
            case 'class_year':
                $this->_class_year = $value;
                break;
            case 'cutter_1':
                $this->_setCutter(1, $value);
                break;
            case 'cutter_2':
                $this->_setCutter(2, $value);
                break;
            case 'cutter_3':
                $this->_setCutter(3, $value);
                break;
            case 'remainder':
                $this->_remainder = $value;
                break;
        }
    }
}
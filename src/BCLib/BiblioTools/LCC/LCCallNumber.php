<?php

namespace BCLib\BiblioTools\LCC;

/**
 * Class LCCN
 * @package BCLib\BiblioTools\LCC
 *
 * @property string      letters
 * @property string      number
 * @property string      cutter_1
 * @property string      cutter_2
 * @property string      cutter_3
 * @property string      remainder
 * @property-read string input_string
 */
class LCCallNumber
{
    protected $_is_parsed = false;

    protected $_letters;
    protected $_number;
    protected $_cutters = array();
    protected $_remainder;

    protected $_input_string;
    protected $_normalized_string;

    protected $_is_valid = false;

    const HI_SORT_CHAR = '~';
    const LOW_SORT_CHAR = ' ';

    /** @var  LCCParser */
    protected $_parser;

    public function __construct(LCCParser $parser)
    {
        $this->_parser = $parser;
    }

    public function normalize()
    {
        $normalized = str_pad($this->_letters, 3, LCCallNumber::LOW_SORT_CHAR, STR_PAD_RIGHT);

        // Only normalize numbers to \d\d\d\d.\d\d
        list($pre_dec, $post_dec) = explode('.', $this->_number);
        $pre_dec = substr($pre_dec, 0, 5);
        $post_dec = substr($post_dec, 0, 3);
        $normalized .= str_pad($pre_dec, 5, LCCallNumber::LOW_SORT_CHAR, STR_PAD_LEFT);
        $normalized .= str_pad($post_dec, 3, LCCallNumber::LOW_SORT_CHAR, STR_PAD_RIGHT);

        for ($i = 1; $i <= 3; $i++) {
            if (isset($this->_cutters[$i])) {
                $normalized .= $this->_normalizeCutter($i);
            }
            else {
                $normalized .= str_repeat(LCCallNumber::LOW_SORT_CHAR, 4);
            }
        }

        $remainder = substr($this->_remainder, 0, 5);
        $normalized .= str_pad($remainder, 5, LCCallNumber::LOW_SORT_CHAR, STR_PAD_RIGHT);

        return $normalized;
    }

    protected function _normalizeCutter($number)
    {
        $cutter = substr($this->_cutters[$number], 0, 4);
        return str_pad($cutter, 4, LCCallNumber::LOW_SORT_CHAR, STR_PAD_RIGHT);
    }

    public function isValid()
    {
        return $this->_is_valid;
    }

    protected function _parse()
    {
        if (!isset($this->_input_string)) {
            throw new \Exception('No LCCN entered');
        }

        if (!$this->_is_parsed) {
            $this->_is_valid = $this->_parser->parse($this);
        }

        $this->_is_parsed = true;
    }

    protected function _setCutter($index, $cutter)
    {
        $this->_cutters[$index] = $cutter;
    }

    protected function _setInputString($input_string)
    {
        if (isset ($this->input_string)) {
            throw new \Exception("Input string already set");
        }
        $this->_input_string = $input_string;
        $this->_parse($input_string);
    }

    public function __get($name)
    {
        if ($name == 'input_string') {
            return $this->_input_string;
        }

        switch ($name) {
            case 'letters':
                return $this->_letters;
            case 'number':
                return $this->_number;
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
            case 'input_string':
                $this->_setInputString($value);
                break;
            default:
                throw new \Exception("$name is not an LCCN property");
        }
    }
}
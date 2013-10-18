<?php

namespace BCLib\LCCallNumbers;

/**
 * Represents a Library of Congress call number
 *
 * Handles call number validation and normalization.
 *
 * Class LCCN
 * @package BCLib\LCCallNumbers
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

    const HI_SORT_CHAR = '~';
    const LOW_SORT_CHAR = '!';

    public function normalize($sort_char = LCCallNumber::LOW_SORT_CHAR)
    {
        return str_pad($this->_letters, 3, $sort_char, STR_PAD_RIGHT) .
        $this->_normalizeNumber($sort_char) .
        $this->_normalizeClassYear($sort_char) .
        $this->_normalizeCutters($sort_char) .
        $this->_normalizeRemainder($sort_char);
    }

    public function normalizeClass($sort_char = LCCallNumber::LOW_SORT_CHAR)
    {
        return str_pad($this->_letters, 3, $sort_char, STR_PAD_RIGHT) .
        $this->_normalizeNumber($sort_char) .
        $this->_normalizeCutters($sort_char, 1);
    }

    protected function _normalizeNumber($sort_char)
    {
        // Only normalize numbers to \d\d\d\d.\d\d\d\d\d
        $num_parts = explode('.', $this->_number);
        $pre_dec = substr($num_parts[0], 0, 5);
        $post_dec = isset($num_parts[1]) ? substr($num_parts[1], 0, 5) : '';
        $normalized = str_pad($pre_dec, 5, LCCallNumber::LOW_SORT_CHAR, STR_PAD_LEFT);
        return $normalized . str_pad($post_dec, 5, $sort_char, STR_PAD_RIGHT);
    }

    protected function _normalizeClassYear($sort_char)
    {
        $class_year = substr($this->_class_year, 0, 5);
        return str_pad($class_year, 5, $sort_char, STR_PAD_RIGHT);
    }

    protected function _normalizeCutters($sort_char, $num_cutters = 3)
    {
        $normalized = '';

        for ($i = 1; $i <= $num_cutters; $i++) {
            if (isset($this->_cutters[$i])) {
                $cutter = substr($this->_cutters[$i], 0, 5);
                $normalized .= str_pad($cutter, 5, $sort_char, STR_PAD_RIGHT);
            } else {
                $normalized .= str_repeat($sort_char, 5);
            }
        }
        return $normalized;
    }

    protected function _normalizeRemainder($sort_char)
    {
        $remainder = substr($this->_remainder, 0, 5);
        return str_pad($remainder, 5, $sort_char, STR_PAD_RIGHT);
    }

    public function isValid()
    {
        return $this->_is_valid;
    }

    public function __get($name)
    {
        switch ($name) {
            case 'letters':
            case 'number':
            case 'remainder':
            case 'class_year':
                $property = '_' . $name;
                return $this->$property;
            case 'cutter_1':
            case 'cutter_2':
            case 'cutter_3':
                $number = substr($name, -1);
                return isset($this->_cutters[$number]) ? $this->_cutters[$number] : false;
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
            case 'class_year':
            case 'remainder':
                $property = '_' . $name;
                $this->$property = $value;
                break;
            case 'cutter_1':
            case 'cutter_2':
            case 'cutter_3':
                $number = substr($name, -1);
                $this->_cutters[$number] = $value;
        }
    }
}
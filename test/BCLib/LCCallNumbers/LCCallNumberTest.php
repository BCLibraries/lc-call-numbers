<?php

namespace BCLib\LCCallNumbers;

class LCCallNumberTest extends \PHPUnit_Framework_TestCase
{

    /** @var  LCCallNumber */
    protected $_cno;

    public function setUp()
    {
        $this->_cno = new LCCallNumber();
    }

    public function testOneCutterNormalization()
    {
        $this->_cno->letters = 'PS';
        $this->_cno->number = '379';
        $this->_cno->cutter_1 = 'L5';
        $expected = 'PS!!!379!!!!!!!!!!L5!!!!!!!!!!!!!!!!!!';
        $this->assertEquals($expected, $this->_cno->normalize());
    }

    public function testTwoCutterNormalization()
    {
        $this->_cno->letters = 'BX';
        $this->_cno->number = '4700';
        $this->_cno->cutter_1 = 'F5';
        $this->_cno->cutter_2 = 'C21';
        $expected = 'BX!!4700!!!!!!!!!!F5!!!C21!!!!!!!!!!!!';
        $this->assertEquals($expected, $this->_cno->normalize());
    }

    public function testThreeCutterNormalization()
    {
        $this->_cno->letters = 'BR';
        $this->_cno->number = '65';
        $this->_cno->cutter_1 = 'A6';
        $this->_cno->cutter_2 = 'E5';
        $this->_cno->cutter_3 = 'O8';
        $expected = 'BR!!!!65!!!!!!!!!!A6!!!E5!!!O8!!!!!!!!';
        $this->assertEquals($expected, $this->_cno->normalize());
    }

    public function testRemainderNormalization()
    {
        $this->_cno->letters = 'KKM';
        $this->_cno->number = '110';
        $this->_cno->cutter_1 = 'B9';
        $this->_cno->cutter_2 = 'N8';
        $this->_cno->remainder = '1869 Hoeflich Collection';
        $expected = 'KKM!!110!!!!!!!!!!B9!!!N8!!!!!!!!1869 ';
        $this->assertEquals($expected, $this->_cno->normalize());
    }

    public function testHighSortCharSortsHigh()
    {
        $this->_cno->letters = 'KKM';
        $this->_cno->number = '110';
        $this->_cno->cutter_1 = 'B9';
        $this->_cno->cutter_2 = 'N8';
        $this->_cno->remainder = '1869 Hoeflich Collection';
        $expected = 'KKM!!110~~~~~~~~~~B9~~~N8~~~~~~~~1869 ';
        $this->assertEquals($expected, $this->_cno->normalize(LCCallNumber::HI_SORT_CHAR));
    }

    public function testNormalizeClassOnly()
    {
        $this->_cno->letters = 'KKM';
        $this->_cno->number = '110';
        $this->_cno->cutter_1 = 'B9';
        $this->_cno->cutter_2 = 'N8';
        $this->_cno->remainder = '1869 Hoeflich Collection';
        $expected = 'KKM!!110~~~~~B9~~~';
        $this->assertEquals($expected, $this->_cno->normalizeClass(LCCallNumber::HI_SORT_CHAR));
    }

    public function testNoLettersIsInvalid()
    {
        $this->_cno->number = '123';
        $this->_cno->cutter_1 = 'F3';
        $this->assertFalse($this->_cno->isValid());
    }

    public function testNoNumberIsInvalid()
    {
        $this->_cno->letters = 'BX';
        $this->_cno->cutter_1 = 'F4';
        $this->assertFalse($this->_cno->isValid());
    }

    public function testNoCutter1IsInvalid()
    {
        $this->_cno->letters = 'PS';
        $this->_cno->number = '1424';
        $this->assertFalse($this->_cno->isValid());
    }

    public function testMinimalValidCallNumberIsValid()
    {
        $this->_cno->letters = 'BX';
        $this->_cno->number = '131';
        $this->_cno->cutter_1 = 'F4';
        $this->assertTrue($this->_cno->isValid());
    }
}

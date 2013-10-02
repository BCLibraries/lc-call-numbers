<?php

namespace BCLib\BiblioTools\LCC;

class LCCallNumberTest extends \PHPUnit_Framework_TestCase
{

    /** @var  LCCallNumber */
    protected $_cno;

    public function setUp()
    {
        $this->_cno = new LCCallNumber();
    }

    public function testOneCutterParses()
    {
        $this->_cno->parse('QA21 .C12');
        $this->assertEquals('QA', $this->_cno->letters);
        $this->assertEquals('21', $this->_cno->number);
        $this->assertEquals('C12', $this->_cno->cutter_1);
    }

    public function testTwoCutterParses()
    {
        $this->_cno->parse('D13.5 .E85 K467 2011');
        $this->assertEquals('D', $this->_cno->letters);
        $this->assertEquals('13.5', $this->_cno->number);
        $this->assertEquals('E85', $this->_cno->cutter_1);
        $this->assertEquals('K467', $this->_cno->cutter_2);
        $this->assertEquals('2011', $this->_cno->remainder);
    }

    public function testThreeCutterParses()
    {
        $this->_cno->parse('M947 .C48 P37 A73 1987');
        $this->assertEquals('M', $this->_cno->letters);
        $this->assertEquals('947', $this->_cno->number);
        $this->assertEquals('C48', $this->_cno->cutter_1);
        $this->assertEquals('P37', $this->_cno->cutter_2);
        $this->assertEquals('A73', $this->_cno->cutter_3);
    }

    public function testCallNumberWithYear()
    {
        $this->_cno->parse('BX830 1545 .O43 2013');
        $this->assertEquals('BX', $this->_cno->letters);
        $this->assertEquals('830', $this->_cno->number);
        $this->assertEquals('1545', $this->_cno->class_year);
        $this->assertEquals('O43', $this->_cno->cutter_1);
        $this->assertEquals('2013', $this->_cno->remainder);
    }

    public function testCallNumberWithOrdinal()
    {
        $this->_cno->parse('E513.5 20th .M555 2005');
        $this->assertEquals('E', $this->_cno->letters);
        $this->assertEquals('513.5', $this->_cno->number);
        $this->assertEquals('20th', $this->_cno->class_year);
        $this->assertEquals('M555', $this->_cno->cutter_1);
        $this->assertEquals('2005', $this->_cno->remainder);
    }

    public function testCallNumberWithNoCutter()
    {
        $this->_cno->parse('BP109 2005b');
        $this->assertEquals('BP', $this->_cno->letters);
        $this->assertEquals('109', $this->_cno->number);
        $this->assertEquals('2005b', $this->_cno->remainder);
    }

    public function testCallNumberWithLongCutter()
    {
        $this->_cno->parse('HN740 .Z9 C63783 2011');
        $this->assertEquals('HN', $this->_cno->letters);
        $this->assertEquals('740', $this->_cno->number);
        $this->assertEquals('Z9', $this->_cno->cutter_1);
        $this->assertEquals('C63783', $this->_cno->cutter_2);
        $this->assertEquals('2011', $this->_cno->remainder);
    }

    public function testCallNumberWithNoSpaceBeforeCutter()
    {
        $this->_cno->parse('BX810.C393 V65 1993');
        $this->assertEquals('BX', $this->_cno->letters);
        $this->assertEquals('810', $this->_cno->number);
        $this->assertEquals('C393', $this->_cno->cutter_1);
        $this->assertEquals('V65', $this->_cno->cutter_2);
        $this->assertEquals('1993', $this->_cno->remainder);
    }

    public function testCallNumberWithSingleDigitCutter()
    {
        $this->_cno->parse('PS 379 .K3');
        $this->assertEquals('PS', $this->_cno->letters);
        $this->assertEquals('379', $this->_cno->number);
        $this->assertEquals('K3', $this->_cno->cutter_1);
    }

    public function testCallNumberWithSpaceAfterLetters()
    {
        $this->_cno->parse('PS 3523 .O862 B8 1920');
        //print_r($this->_cno); exit();
        $this->assertEquals('PS', $this->_cno->letters);
        $this->assertEquals('3523', $this->_cno->number);
        $this->assertEquals('O862', $this->_cno->cutter_1);
        $this->assertEquals('B8', $this->_cno->cutter_2);
        $this->assertEquals('1920', $this->_cno->remainder);
    }
}

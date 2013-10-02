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
}

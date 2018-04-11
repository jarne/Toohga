<?php
/**
 * Created by PhpStorm.
 * User: jarne
 * Date: 26.01.18
 * Time: 14:19
 */

namespace jarne\toohga\service;

use PHPUnit\Framework\TestCase;

class DecimalConverterTest extends TestCase {
    /**
     * Test the function to convert a string to a number
     */
    public function testStringToNumber(): void {
        $this->assertEquals(0, DecimalConverter::stringToNumber("1"));
        $this->assertEquals(1, DecimalConverter::stringToNumber("2"));
        $this->assertEquals(2, DecimalConverter::stringToNumber("3"));
        $this->assertEquals(3, DecimalConverter::stringToNumber("4"));
        $this->assertEquals(4, DecimalConverter::stringToNumber("5"));

        $this->assertEquals(9, DecimalConverter::stringToNumber("A"));
        $this->assertEquals(10, DecimalConverter::stringToNumber("B"));
        $this->assertEquals(11, DecimalConverter::stringToNumber("C"));
        $this->assertEquals(12, DecimalConverter::stringToNumber("D"));
        $this->assertEquals(13, DecimalConverter::stringToNumber("E"));

        $this->assertEquals(297, DecimalConverter::stringToNumber("A1"));
        $this->assertEquals(306, DecimalConverter::stringToNumber("AA"));
        $this->assertEquals(331, DecimalConverter::stringToNumber("B2"));
        $this->assertEquals(340, DecimalConverter::stringToNumber("BB"));

        $this->assertEquals(16756940, DecimalConverter::stringToNumber("F5AG3"));
        $this->assertEquals(29405517, DecimalConverter::stringToNumber("RT9BA"));
        $this->assertEquals(22871561, DecimalConverter::stringToNumber("LAFCM"));
        $this->assertEquals(4749132, DecimalConverter::stringToNumber("51614"));
        $this->assertEquals(1798217, DecimalConverter::stringToNumber("2J29F"));

        $this->assertEquals(null, DecimalConverter::stringToNumber("F5A?3"));
        $this->assertEquals(null, DecimalConverter::stringToNumber("RT9B!"));
        $this->assertEquals(null, DecimalConverter::stringToNumber("LRR%1"));
        $this->assertEquals(null, DecimalConverter::stringToNumber("!$!/("));
        $this->assertEquals(null, DecimalConverter::stringToNumber(""));
    }

    /**
     * Test the function to convert a number to a string
     */
    public function testNumberToString(): void {
        $this->assertEquals("1", DecimalConverter::numberToString(0));
        $this->assertEquals("2", DecimalConverter::numberToString(1));
        $this->assertEquals("3", DecimalConverter::numberToString(2));
        $this->assertEquals("4", DecimalConverter::numberToString(3));
        $this->assertEquals("5", DecimalConverter::numberToString(4));

        $this->assertEquals("A", DecimalConverter::numberToString(9));
        $this->assertEquals("B", DecimalConverter::numberToString(10));
        $this->assertEquals("C", DecimalConverter::numberToString(11));
        $this->assertEquals("D", DecimalConverter::numberToString(12));
        $this->assertEquals("E", DecimalConverter::numberToString(13));

        $this->assertEquals("A1", DecimalConverter::numberToString(297));
        $this->assertEquals("AA", DecimalConverter::numberToString(306));
        $this->assertEquals("B2", DecimalConverter::numberToString(331));
        $this->assertEquals("BB", DecimalConverter::numberToString(340));

        $this->assertEquals("F5AG3", DecimalConverter::numberToString(16756940));
        $this->assertEquals("RT9BA", DecimalConverter::numberToString(29405517));
        $this->assertEquals("LAFCM", DecimalConverter::numberToString(22871561));
        $this->assertEquals("51614", DecimalConverter::numberToString(4749132));
        $this->assertEquals("2J29F", DecimalConverter::numberToString(1798217));
    }
}

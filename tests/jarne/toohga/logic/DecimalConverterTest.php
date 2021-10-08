<?php
/**
 * Toohga | decimal converter tests
 */

namespace jarne\toohga\tests\logic;

use jarne\toohga\logic\DecimalConverter;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \jarne\toohga\logic\DecimalConverter
 */
class DecimalConverterTest extends TestCase
{
    /**
     * Test the function to convert a string to a number
     *
     * @covers ::stringToNumber
     */
    public function testStringToNumber(): void
    {
        $this->assertEquals(0, DecimalConverter::stringToNumber("1"));
        $this->assertEquals(1, DecimalConverter::stringToNumber("2"));
        $this->assertEquals(2, DecimalConverter::stringToNumber("3"));
        $this->assertEquals(3, DecimalConverter::stringToNumber("4"));
        $this->assertEquals(4, DecimalConverter::stringToNumber("5"));

        $this->assertEquals(9, DecimalConverter::stringToNumber("a"));
        $this->assertEquals(10, DecimalConverter::stringToNumber("b"));
        $this->assertEquals(11, DecimalConverter::stringToNumber("c"));
        $this->assertEquals(12, DecimalConverter::stringToNumber("d"));
        $this->assertEquals(13, DecimalConverter::stringToNumber("e"));

        $this->assertEquals(297, DecimalConverter::stringToNumber("a1"));
        $this->assertEquals(306, DecimalConverter::stringToNumber("aa"));
        $this->assertEquals(331, DecimalConverter::stringToNumber("b2"));
        $this->assertEquals(340, DecimalConverter::stringToNumber("bb"));

        $this->assertEquals(16756940, DecimalConverter::stringToNumber("f5ag3"));
        $this->assertEquals(29405517, DecimalConverter::stringToNumber("rt9ba"));
        $this->assertEquals(22871561, DecimalConverter::stringToNumber("lafcm"));
        $this->assertEquals(4749132, DecimalConverter::stringToNumber("51614"));
        $this->assertEquals(1798217, DecimalConverter::stringToNumber("2j29f"));

        $this->assertEquals(16756940, DecimalConverter::stringToNumber("F5AG3"));
        $this->assertEquals(1798217, DecimalConverter::stringToNumber("2J29F"));

        $this->assertEquals(null, DecimalConverter::stringToNumber("f5a?3"));
        $this->assertEquals(null, DecimalConverter::stringToNumber("rt9b!"));
        $this->assertEquals(null, DecimalConverter::stringToNumber("lrr%1"));
        $this->assertEquals(null, DecimalConverter::stringToNumber("!$!/("));
        $this->assertEquals(null, DecimalConverter::stringToNumber(""));
    }

    /**
     * Test the function to convert a number to a string
     *
     * @covers ::numberToString
     */
    public function testNumberToString(): void
    {
        $this->assertEquals("1", DecimalConverter::numberToString(0));
        $this->assertEquals("2", DecimalConverter::numberToString(1));
        $this->assertEquals("3", DecimalConverter::numberToString(2));
        $this->assertEquals("4", DecimalConverter::numberToString(3));
        $this->assertEquals("5", DecimalConverter::numberToString(4));

        $this->assertEquals("a", DecimalConverter::numberToString(9));
        $this->assertEquals("b", DecimalConverter::numberToString(10));
        $this->assertEquals("c", DecimalConverter::numberToString(11));
        $this->assertEquals("d", DecimalConverter::numberToString(12));
        $this->assertEquals("e", DecimalConverter::numberToString(13));

        $this->assertEquals("a1", DecimalConverter::numberToString(297));
        $this->assertEquals("aa", DecimalConverter::numberToString(306));
        $this->assertEquals("b2", DecimalConverter::numberToString(331));
        $this->assertEquals("bb", DecimalConverter::numberToString(340));

        $this->assertEquals("f5ag3", DecimalConverter::numberToString(16756940));
        $this->assertEquals("rt9ba", DecimalConverter::numberToString(29405517));
        $this->assertEquals("lafcm", DecimalConverter::numberToString(22871561));
        $this->assertEquals("51614", DecimalConverter::numberToString(4749132));
        $this->assertEquals("2j29f", DecimalConverter::numberToString(1798217));
    }
}

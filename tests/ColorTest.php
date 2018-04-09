<?php

use PHPUnit\Framework\TestCase;
use Jdenticon\Color;

final class ColorTest extends TestCase
{
    public function testParseValid(): void
    {
        $this->assertEquals("#00000000", Color::parse("transparent")->__toString());
        $this->assertEquals("#66cdaaff", Color::parse("mediumaquaMarine")->__toString());
        $this->assertEquals("#aabbccdd", Color::parse("#abcd")->__toString());
        $this->assertEquals("#abcdef21", Color::parse("#abcdef21")->__toString());
        $this->assertEquals("#aabbccff", Color::parse("#abc")->__toString());
        $this->assertEquals("#aabbccff", Color::parse("#aabbcc")->__toString());
        $this->assertEquals("#fffefdff", Color::parse("rgb(   255 ,254 ,253  )")->__toString());
        $this->assertEquals("#ff7f00ff", Color::parse("rgb(100%, 50%, 0%)")->__toString());
        $this->assertEquals("#fffefd7f", Color::parse("rgb(255, 254, 253, 0.5)")->__toString());
        $this->assertEquals("#fffefd7f", Color::parse("rgba(255, 254, 253, 0.5)")->__toString());
        $this->assertEquals("#adcbaeff", Color::parse("hsl(123, 23%, 74% )")->__toString());
        $this->assertEquals("#adcbaeff", Color::parse("hsl(123deg, 23%, 74% )")->__toString());
        $this->assertEquals("#adcbaeff", Color::parse("hsl(123.000001deg, 23%, 74% )")->__toString());
        $this->assertEquals("#adcbae7f", Color::parse("hsl(123.000001deg, 23%, 74% , 0.5)")->__toString());
        $this->assertEquals("#adcbae7f", Color::parse("hsla(123.000001deg, 23%, 74% , 0.5)")->__toString());
        $this->assertEquals("#bcbcbcff", Color::parse("hsl(123, 0%, 74% )")->__toString());
        $this->assertEquals("#000000ff", Color::parse("hsl(123deg, 23%, 0% )")->__toString());
        $this->assertEquals("#ffffffff", Color::parse("hsl(123.000001deg, 23%, 100% )")->__toString());
        $this->assertEquals("#adcbaeff", Color::parse("hsl(2.146755rad, 23%, 74% )")->__toString());
        $this->assertEquals("#adcbaeff", Color::parse("hsl(0.3416667turn, 23%, 74% )")->__toString());
        $this->assertEquals("#adcbaeff", Color::parse("hsl(136.66667grad, 23%, 74% )")->__toString());
    }

    public function testParseInvalidHex()
    {
        $this->expectException(InvalidArgumentException::class);
        Color::parse("abc(5, 100%, 0%)");
    }

    public function testParseInvalidRgb()
    {
        $this->expectException(InvalidArgumentException::class);
        Color::parse("rgb(56, 0, 5, 100%, 0%)");
    }

    public function testParseInvalidHsl()
    {
        $this->expectException(InvalidArgumentException::class);
        Color::parse("hsl(1, 11%, 1)");
    }
}
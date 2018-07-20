<?php

use Jdenticon\IdenticonStyle;

final class IdenticonStyleTest extends PHPUnit_Framework_TestCase
{
    // BackgroundGrayscale
    public function testSetBackColorWrongType()
    {
        $this->setExpectedException('InvalidArgumentException');
        $style = new IdenticonStyle();
        $style->setBackgroundColor(56);
    }
    public function testSetBackColorInvalid()
    {
        $this->setExpectedException('InvalidArgumentException');
        $style = new IdenticonStyle();
        $style->backgroundColor = "rbg(1,2,3)";
    }
    public function testSetBackColorValid()
    {
        $style = new IdenticonStyle();
        $style->backgroundColor = "hsl(0, 100%, 50%)";
        $this->assertEquals("#ff0000ff", $style->backgroundColor->__toString());
    }

    // Padding
    public function testSetPaddingWrongType()
    {
        $this->setExpectedException('InvalidArgumentException');
        $style = new IdenticonStyle();
        $style->padding = "15";
    }
    public function testSetPaddingTooLarge()
    {
        $this->setExpectedException('InvalidArgumentException');
        $style = new IdenticonStyle();
        $style->padding = 0.5;
    }
    public function testSetPaddingTooSmall()
    {
        $this->setExpectedException('InvalidArgumentException');
        $style = new IdenticonStyle();
        $style->padding = -0.1;
    }
    public function testSetPaddingValid()
    {
        $style = new IdenticonStyle();
        $style->padding = 0.08;
        $this->assertEquals(0.08, $style->getPadding());
    }

    // Hues
    public function testSetHuesWrongType()
    {
        $this->setExpectedException('InvalidArgumentException');
        $style = new IdenticonStyle();
        $style->hues = "Not a hue";
    }
    public function testSetHuesWrongInnerType()
    {
        $this->setExpectedException('InvalidArgumentException');
        $style = new IdenticonStyle();
        $style->hues = array("Not a hue");
    }
    public function testSetHuesSingle()
    {
        $style = new IdenticonStyle();
        $style->hues = 367;
        $this->assertEquals(array(7), $style->getHues());
    }
    public function testSetHuesMultiple()
    {
        $style = new IdenticonStyle();
        $style->hues = array(-1, 99, 721);
        $this->assertEquals(array(359, 99, 1), $style->getHues());
    }
    public function testSetHuesNull()
    {
        $style = new IdenticonStyle();
        $style->hues = array(-1, 99, 721);
        $style->hues = null;
        $this->assertNull($style->getHues());
    }
    public function testSetHuesEmpty()
    {
        $style = new IdenticonStyle();
        $style->setHues(array(-1, 99, 721));
        $style->setHues(array());
        $this->assertNull($style->getHues());
    }

    // ColorSaturation
    public function testColorSaturationWrongType()
    {
        $this->setExpectedException('InvalidArgumentException');
        $style = new IdenticonStyle();
        $style->colorSaturation = "15";
    }
    public function testColorSaturationTooLarge()
    {
        $this->setExpectedException('InvalidArgumentException');
        $style = new IdenticonStyle();
        $style->colorSaturation = 1.5;
    }
    public function testColorSaturationTooSmall()
    {
        $this->setExpectedException('InvalidArgumentException');
        $style = new IdenticonStyle();
        $style->colorSaturation = -0.1;
    }
    public function testColorSaturationValid()
    {
        $style = new IdenticonStyle();
        $style->colorSaturation = 0.5;
        $this->assertEquals(0.5, $style->getColorSaturation());
    }

    // GrayscaleSaturation
    public function testGrayscaleSaturationWrongType()
    {
        $this->setExpectedException('InvalidArgumentException');
        $style = new IdenticonStyle();
        $style->grayscaleSaturation = "15";
    }
    public function testGrayscaleSaturationTooLarge()
    {
        $this->setExpectedException('InvalidArgumentException');
        $style = new IdenticonStyle();
        $style->grayscaleSaturation = 1.5;
    }
    public function testGrayscaleSaturationTooSmall()
    {
        $this->setExpectedException('InvalidArgumentException');
        $style = new IdenticonStyle();
        $style->grayscaleSaturation = -0.1;
    }
    public function testGrayscaleSaturationValid()
    {
        $style = new IdenticonStyle();
        $style->grayscaleSaturation = 0.5;
        $this->assertEquals(0.5, $style->getGrayscaleSaturation());
    }

    // ColorLightness
    public function testColorLightnessWrongType()
    {
        $this->setExpectedException('InvalidArgumentException');
        $style = new IdenticonStyle();
        $style->colorLightness = "15";
    }
    public function testColorLightnessTooLarge()
    {
        $this->setExpectedException('InvalidArgumentException');
        $style = new IdenticonStyle();
        $style->colorLightness = array(1.1, 0.5);
    }
    public function testColorLightnessTooSmall()
    {
        $this->setExpectedException('InvalidArgumentException');
        $style = new IdenticonStyle();
        $style->colorLightness = array(0.5, -0.01);
    }
    public function testColorLightnessValid()
    {
        $style = new IdenticonStyle();
        $style->colorLightness = array(0.1, 0.3, 55667, "hejsan");
        $this->assertEquals(array(0.1, 0.3), $style->getColorLightness());
    }

    // GrayscaleLightness
    public function testGrayscaleLightnessWrongType()
    {
        $this->setExpectedException('InvalidArgumentException');
        $style = new IdenticonStyle();
        $style->grayscaleLightness = "15";
    }
    public function testGrayscaleLightnessTooLarge()
    {
        $this->setExpectedException('InvalidArgumentException');
        $style = new IdenticonStyle();
        $style->grayscaleLightness = array(1.1, 0.5);
    }
    public function testGrayscaleLightnessTooSmall()
    {
        $this->setExpectedException('InvalidArgumentException');
        $style = new IdenticonStyle();
        $style->grayscaleLightness = array(0.5, -0.01);
    }
    public function testGrayscaleLightnessValid()
    {
        $style = new IdenticonStyle();
        $style->grayscaleLightness = array(0.1, 0.3, 55667, "hejsan");
        $this->assertEquals(array(0.1, 0.3), $style->getGrayscaleLightness());
    }

    // GetOptions / SetOptions
    public function testOptions()
    {
        $options = array(
            'backgroundColor' => '#f00a',
            'colorLightness' => array(0.1, 0.2),
            'grayscaleLightness' => array(0.3, 0.4),
            'colorSaturation' => 0.5,
            'grayscaleSaturation' => 0.6,
            'padding' => 0.16,
            'hues' => array(1, 2, 3)
        );

        $style = new IdenticonStyle($options);
        $options2 = $style->getOptions();

        $options['backgroundColor'] = '#ff0000aa';

        $this->assertEquals($options, $options2);
    }
}
<?php

include_once(__DIR__ . '/InitTests.php');

use Jdenticon\IdenticonStyle;

final class IdenticonStyleTest extends PHPUnit_Framework_TestCase
{
    // BackgroundGrayscale
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetBackColorWrongType()
    {
        $style = new IdenticonStyle();
        $style->setBackgroundColor(56);
    }
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetBackColorInvalid()
    {
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
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetPaddingWrongType()
    {
        $style = new IdenticonStyle();
        $style->padding = "15";
    }
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetPaddingTooLarge()
    {
        $style = new IdenticonStyle();
        $style->padding = 0.5;
    }
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetPaddingTooSmall()
    {
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
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetHuesWrongType()
    {
        $style = new IdenticonStyle();
        $style->hues = "Not a hue";
    }
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetHuesWrongInnerType()
    {
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
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testColorSaturationWrongType()
    {
        $style = new IdenticonStyle();
        $style->colorSaturation = "15";
    }
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testColorSaturationTooLarge()
    {
        $style = new IdenticonStyle();
        $style->colorSaturation = 1.5;
    }
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testColorSaturationTooSmall()
    {
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
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGrayscaleSaturationWrongType()
    {
        $style = new IdenticonStyle();
        $style->grayscaleSaturation = "15";
    }
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGrayscaleSaturationTooLarge()
    {
        $style = new IdenticonStyle();
        $style->grayscaleSaturation = 1.5;
    }
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGrayscaleSaturationTooSmall()
    {
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
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testColorLightnessWrongType()
    {
        $style = new IdenticonStyle();
        $style->colorLightness = "15";
    }
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testColorLightnessTooLarge()
    {
        $style = new IdenticonStyle();
        $style->colorLightness = array(1.1, 0.5);
    }
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testColorLightnessTooSmall()
    {
        $style = new IdenticonStyle();
        $style->colorLightness = array(0.5, -0.01);
    }
    public function testColorLightnessValid()
    {
        $style = new IdenticonStyle();
        $style->colorLightness = array(0.1, 0.3, 55667, "hello");
        $this->assertEquals(array(0.1, 0.3), $style->getColorLightness());
    }

    // GrayscaleLightness
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGrayscaleLightnessWrongType()
    {
        $style = new IdenticonStyle();
        $style->grayscaleLightness = "15";
    }
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGrayscaleLightnessTooLarge()
    {
        $style = new IdenticonStyle();
        $style->grayscaleLightness = array(1.1, 0.5);
    }
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGrayscaleLightnessTooSmall()
    {
        $style = new IdenticonStyle();
        $style->grayscaleLightness = array(0.5, -0.01);
    }
    public function testGrayscaleLightnessValid()
    {
        $style = new IdenticonStyle();
        $style->grayscaleLightness = array(0.1, 0.3, 55667, "hello");
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
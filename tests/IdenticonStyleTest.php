<?php

use PHPUnit\Framework\TestCase;
use Jdenticon\IdenticonStyle;

final class IdenticonStyleTest extends TestCase
{
    // BackgroundGrayscale
    public function testSetBackColorWrongType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $style = new IdenticonStyle();
        $style->setBackgroundColor(56);
    }
    public function testSetBackColorInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $style = new IdenticonStyle();
        $style->setBackgroundColor("rbg(1,2,3)");
    }
    public function testSetBackColorValid(): void
    {
        $style = new IdenticonStyle();
        $style->setBackgroundColor("hsl(0, 100%, 50%)");
        $this->assertEquals("#ff0000ff", $style->getBackgroundColor()->__toString());
    }

    // Padding
    public function testSetPaddingWrongType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $style = new IdenticonStyle();
        $style->setPadding("15");
    }
    public function testSetPaddingTooLarge(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $style = new IdenticonStyle();
        $style->setPadding(0.5);
    }
    public function testSetPaddingTooSmall(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $style = new IdenticonStyle();
        $style->setPadding(-0.1);
    }
    public function testSetPaddingValid(): void
    {
        $style = new IdenticonStyle();
        $style->setPadding(0.08);
        $this->assertEquals(0.08, $style->getPadding());
    }

    // Hues
    public function testSetHuesWrongType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $style = new IdenticonStyle();
        $style->setHues("Not a hue");
    }
    public function testSetHuesWrongInnerType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $style = new IdenticonStyle();
        $style->setHues(array("Not a hue"));
    }
    public function testSetHuesSingle(): void
    {
        $style = new IdenticonStyle();
        $style->setHues(367);
        $this->assertEquals(array(7), $style->getHues());
    }
    public function testSetHuesMultiple(): void
    {
        $style = new IdenticonStyle();
        $style->setHues(array(-1, 99, 721));
        $this->assertEquals(array(359, 99, 1), $style->getHues());
    }
    public function testSetHuesNull(): void
    {
        $style = new IdenticonStyle();
        $style->setHues(array(-1, 99, 721));
        $style->setHues(null);
        $this->assertNull($style->getHues());
    }
    public function testSetHuesEmpty(): void
    {
        $style = new IdenticonStyle();
        $style->setHues(array(-1, 99, 721));
        $style->setHues(array());
        $this->assertNull($style->getHues());
    }

    // ColorSaturation
    public function testColorSaturationWrongType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $style = new IdenticonStyle();
        $style->setColorSaturation("15");
    }
    public function testColorSaturationTooLarge(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $style = new IdenticonStyle();
        $style->setColorSaturation(1.5);
    }
    public function testColorSaturationTooSmall(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $style = new IdenticonStyle();
        $style->setColorSaturation(-0.1);
    }
    public function testColorSaturationValid(): void
    {
        $style = new IdenticonStyle();
        $style->setColorSaturation(0.5);
        $this->assertEquals(0.5, $style->getColorSaturation());
    }

    // GrayscaleSaturation
    public function testGrayscaleSaturationWrongType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $style = new IdenticonStyle();
        $style->setGrayscaleSaturation("15");
    }
    public function testGrayscaleSaturationTooLarge(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $style = new IdenticonStyle();
        $style->setGrayscaleSaturation(1.5);
    }
    public function testGrayscaleSaturationTooSmall(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $style = new IdenticonStyle();
        $style->setGrayscaleSaturation(-0.1);
    }
    public function testGrayscaleSaturationValid(): void
    {
        $style = new IdenticonStyle();
        $style->setGrayscaleSaturation(0.5);
        $this->assertEquals(0.5, $style->getGrayscaleSaturation());
    }

    // ColorLightness
    public function testColorLightnessWrongType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $style = new IdenticonStyle();
        $style->setColorLightness("15");
    }
    public function testColorLightnessTooLarge(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $style = new IdenticonStyle();
        $style->setColorLightness(array(1.1, 0.5));
    }
    public function testColorLightnessTooSmall(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $style = new IdenticonStyle();
        $style->setColorLightness(array(0.5, -0.01));
    }
    public function testColorLightnessValid(): void
    {
        $style = new IdenticonStyle();
        $style->setColorLightness(array(0.1, 0.3, 55667, "hejsan"));
        $this->assertEquals(array(0.1, 0.3), $style->getColorLightness());
    }

    // GrayscaleLightness
    public function testGrayscaleLightnessWrongType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $style = new IdenticonStyle();
        $style->setGrayscaleLightness("15");
    }
    public function testGrayscaleLightnessTooLarge(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $style = new IdenticonStyle();
        $style->setGrayscaleLightness(array(1.1, 0.5));
    }
    public function testGrayscaleLightnessTooSmall(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $style = new IdenticonStyle();
        $style->setGrayscaleLightness(array(0.5, -0.01));
    }
    public function testGrayscaleLightnessValid(): void
    {
        $style = new IdenticonStyle();
        $style->setGrayscaleLightness(array(0.1, 0.3, 55667, "hejsan"));
        $this->assertEquals(array(0.1, 0.3), $style->getGrayscaleLightness());
    }

    // GetOptions / SetOptions
    public function testOptions(): void
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
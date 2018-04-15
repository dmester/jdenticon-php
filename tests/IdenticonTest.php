<?php

use PHPUnit\Framework\TestCase;
use Jdenticon\Identicon;
use Jdenticon\IdenticonStyle;
use Jdenticon\Rendering\IconGenerator;

class AnotherIconGenerator extends IconGenerator 
{
    public function __construct() 
    {
    }
}

final class IdenticonTest extends TestCase
{
    public function testSetValue(): void
    {
        $icon = new Identicon();
        $icon->value = 14.6;
        $this->assertEquals(14.6, $icon->getValue());
        $this->assertEquals('4dbe74c9e554e745b1e199bcb0e19607a44e3a2f', $icon->getHash());

        $options = $icon->getOptions();
        $this->assertEquals(14.6, $options['value']);
        $this->assertEquals(false, isset($options['hash']));
    }
    public function testSetHash(): void
    {
        $icon = new Identicon();
        $icon->hash = '4dbe74c9e554e745b1e199bcb0e19607a44e3a2f';
        $this->assertEquals(null, $icon->getValue());
        $this->assertEquals('4dbe74c9e554e745b1e199bcb0e19607a44e3a2f', $icon->getHash());

        $options = $icon->getOptions();
        $this->assertEquals('4dbe74c9e554e745b1e199bcb0e19607a44e3a2f', $options['hash']);
        $this->assertEquals(false, isset($options['value']));
    }

    public function testSetSizeTooLow(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $icon = new Identicon(array('size' => 0));
    }
    public function testSetSize(): void
    {
        $icon = new Identicon(array('size' => 42.3));
        $this->assertEquals(42, $icon->getOptions()['size']);
    }



    public function testGetDefaultIconGenerator(): void
    {
        $icon = new Identicon();
        $this->assertNotNull($icon->getIconGenerator());
        $this->assertFalse(isset($icon->getOptions()['iconGenerator']));
    }
    public function testGetIconGenerator(): void
    {
        $icon = new Identicon();
        $icon->iconGenerator = new AnotherIconGenerator();
        $this->assertNotNull($icon->getIconGenerator());
        $this->assertNotNull($icon->getOptions()['iconGenerator']);
    }

    public function testSetStyleNull(): void
    {
        $icon = new Identicon();
        $icon->style = null;
        $this->assertEquals(new IdenticonStyle(), $icon->getStyle());
    }
    public function testSetStyleArray(): void
    {
        $icon = new Identicon();
        $icon->style = [
            'backgroundcolor' => '#abcd'
        ];
        $this->assertEquals('#aabbccdd', $icon->getStyle()->getBackgroundColor()->__toString());
    }
    public function testSetStyleInstance(): void
    {
        $style = new IdenticonStyle();
        $style->backgroundColor = '#abcd';
        $icon = new Identicon();
        $icon->style = $style;
        $style->backgroundColor = '#abce';
        $this->assertEquals('#aabbccee', $icon->getStyle()->getBackgroundColor()->__toString());
    }

    public function testOptions(): void
    {
        $options = array(
            'value' => 'hello',
            'size' => 451,
            'style' => array(
                'backgroundColor' => '#aa009911',
                'padding' => 0.1,
                'colorSaturation' => 0.2,
                'grayscaleSaturation' => 0.3,
                'colorLightness' => array(0.4, 0.5),
                'grayscaleLightness' => array(0.6, 0.7)
            )
        );
        $icon = new Identicon($options);
        $this->assertEquals($options, $icon->getOptions());
    }
}
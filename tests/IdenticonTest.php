<?php

include_once(__DIR__ . '/InitTests.php');

use Jdenticon\Identicon;
use Jdenticon\IdenticonStyle;
use Jdenticon\Rendering\IconGenerator;

class AnotherIconGenerator extends IconGenerator 
{
    public function __construct() 
    {
    }
}

final class IdenticonTest extends PHPUnit_Framework_TestCase
{
    public function testSetValue()
    {
        $icon = new Identicon();
        $icon->value = 14.6;
        $this->assertEquals(14.6, $icon->getValue());
        $this->assertEquals('4dbe74c9e554e745b1e199bcb0e19607a44e3a2f', $icon->getHash());

        $options = $icon->getOptions();
        $this->assertEquals(14.6, $options['value']);
        $this->assertEquals(false, isset($options['hash']));
    }
    public function testSetHash()
    {
        $icon = new Identicon();
        $icon->hash = '4dbe74c9e554e745b1e199bcb0e19607a44e3a2f';
        $this->assertEquals(null, $icon->getValue());
        $this->assertEquals('4dbe74c9e554e745b1e199bcb0e19607a44e3a2f', $icon->getHash());

        $options = $icon->getOptions();
        $this->assertEquals('4dbe74c9e554e745b1e199bcb0e19607a44e3a2f', $options['hash']);
        $this->assertEquals(false, isset($options['value']));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetSizeTooLow()
    {
        $icon = new Identicon(array('size' => 0));
    }
    public function testSetSize()
    {
        $icon = new Identicon(array('size' => 42.3));
        $options = $icon->getOptions();
        $this->assertEquals(42, $options['size']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetEnableImageMagickInvalid()
    {
        $icon = new Identicon(array('enableimagemagick' => 55));
    }
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetEnableImageMagickNull()
    {
        $icon = new Identicon(array('enableimagemagick' => null));
    }
    public function testSetEnableImageMagick()
    {
        $icon = new Identicon(array('enableimagemagick' => false));
        $options = $icon->getOptions();
        $this->assertEquals(false, $options['enableImageMagick']);
    }

    public function testGetDefaultIconGenerator()
    {
        $icon = new Identicon();
        $options = $icon->getOptions();
        $this->assertNotNull($icon->getIconGenerator());
        $this->assertFalse(isset($options['iconGenerator']));
    }
    public function testGetIconGenerator()
    {
        $icon = new Identicon();
        $icon->iconGenerator = new AnotherIconGenerator();
        $options = $icon->getOptions();
        $this->assertNotNull($icon->getIconGenerator());
        $this->assertNotNull($options['iconGenerator']);
    }

    public function testSetStyleNull()
    {
        $icon = new Identicon();
        $icon->style = null;
        $this->assertEquals(new IdenticonStyle(), $icon->getStyle());
    }
    public function testSetStyleArray()
    {
        $icon = new Identicon();
        $icon->style = array(
            'backgroundcolor' => '#abcd'
        );
        $this->assertEquals('#aabbccdd', $icon->getStyle()->getBackgroundColor()->__toString());
    }
    public function testSetStyleInstance()
    {
        $style = new IdenticonStyle();
        $style->backgroundColor = '#abcd';
        $icon = new Identicon();
        $icon->style = $style;
        $style->backgroundColor = '#abce';
        $this->assertEquals('#aabbccee', $icon->getStyle()->getBackgroundColor()->__toString());
    }

    public function testOptions()
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
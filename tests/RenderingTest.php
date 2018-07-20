<?php

use PHPUnit\Framework\TestCase;
use Jdenticon\Identicon;
use Jdenticon\IdenticonStyle;
use Jdenticon\Rendering\IconGenerator;
use Jdenticon\Rendering\InternalPngRenderer;

final class RenderingTest extends TestCase
{
    public function testIcon39(): void
    {
        $icon = new Identicon(array(
            'value' => 39,
            'size' => 50,
            // config=ffffffff103c220a41560064
            'style' => array(
                // Originally purple
                'hues' => array(134 /*green*/, 0 /*red*/, 60 /*yellow*/),
                'backgroundColor' => '#ffffffff',
                'colorLightness' => array(0.66, 0.86),
                'grayscaleLightness' => array(0.00, 1.00),
                'colorSaturation' => 0.35,
                'grayscaleSaturation' => 0.10,
            ))
        );

        $this->performTest($icon, 39);
    }


    public function testIcon76(): void
    {
        $icon = new Identicon(array(
            'value' => 76,
            'size' => 50,
            // config=0000002a103c2d481d351328
            'style' => array(
                // Originally blue
                'hues' => array(134 /*green*/, 0 /*red*/, 60 /*yellow*/),
                'backgroundColor' => '#0000002a',
                'colorLightness' => array(0.30, 0.54),
                'grayscaleLightness' => array(0.19, 0.41),
                'colorSaturation' => 0.46,
                'grayscaleSaturation' => 0.72,
            ))
        );

        $this->performTest($icon, 76);
    }

    public function testIcon50(): void
    {
        $icon = new Identicon(array(
            'value' => 50,
            'size' => 50
        ));

        $this->performTest($icon, 50);
    }

    public function testIcon73(): void
    {
        $icon = new Identicon(array(
            'value' => 73,
            'size' => 50
        ));

        $this->performTest($icon, 73);
    }

    private static function formatDataUri($imageFormat, $data) 
    {
        $mimeType = $imageFormat == 'png' ? 'image/png' : 'image/svg+xml';
        $base64 = base64_encode($data);
        return "data:$mimeType;base64,$base64";
    }

    private function performTest($icon, $number)
    {
        $renderer = new InternalPngRenderer($icon->size, $icon->size);
        $icon->draw($renderer);
     
        // Format as data uri so that we can easily investigate failing rendering tests
        $actual = self::formatDataUri('png', $renderer->getData());
        $expected = self::formatDataUri('png', file_get_contents(__DIR__ ."/$number.png"));

        $this->assertEquals($expected, $actual, "PNG rendering test for icon '$number'.");

        $actual = $icon->getImageDataUri('svg');
        $expected = self::formatDataUri('svg', file_get_contents(__DIR__ ."/$number.svg"));

        $this->assertEquals($expected, $actual, "SVG rendering test for icon '$number'.");
    }


}
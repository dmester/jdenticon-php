<?php

use Jdenticon\Identicon;
use Jdenticon\IdenticonStyle;
use Jdenticon\Rendering\IconGenerator;
use Jdenticon\Rendering\InternalPngRenderer;

final class RenderingTest extends PHPUnit_Framework_TestCase
{
    public function testIcon39()
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

    public function testIcon76()
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

    public function testIcon50()
    {
        $icon = new Identicon(array(
            'value' => 50,
            'size' => 50
        ));

        $this->performTest($icon, 50);
    }

    public function testIcon73()
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
     
        $actualRaw = $renderer->getData();
        $expectedRaw = file_get_contents(__DIR__ ."/$number.png");

        $imagick = new \Imagick();

        // Only extract R, G and B channels. Extracting the alpha channel seems 
        // to produce unreliable values for some reason.
        $imagick->readImageBlob($actualRaw);
        $actualChannels = array(
            $imagick->exportImagePixels(0, 0, $icon->size, $icon->size, "R", Imagick::PIXEL_INTEGER),
            $imagick->exportImagePixels(0, 0, $icon->size, $icon->size, "G", Imagick::PIXEL_INTEGER),
            $imagick->exportImagePixels(0, 0, $icon->size, $icon->size, "B", Imagick::PIXEL_INTEGER)
            );
        
        $imagick->readImageBlob($expectedRaw);
        $expectedChannels = array(
            $imagick->exportImagePixels(0, 0, $icon->size, $icon->size, "R", Imagick::PIXEL_INTEGER),
            $imagick->exportImagePixels(0, 0, $icon->size, $icon->size, "G", Imagick::PIXEL_INTEGER),
            $imagick->exportImagePixels(0, 0, $icon->size, $icon->size, "B", Imagick::PIXEL_INTEGER)
            );
        $imagick->destroy();
        
        $isok = true;

        for ($channel = 0; $channel < 3; $channel++) {
            $actual = $actualChannels[$channel];
            $expected = $expectedChannels[$channel];

            $actualCount = count($actual);
            $expectedCount = count($expected);
            $this->assertEquals($expectedCount, $actualCount, "PNG rendering test for icon '$number'. Length diff.");

            for ($i = 0; $i < $actualCount; $i++) {
                $a = $actual[$i] & 0xff;
                $b = $expected[$i] & 0xff;

                // Rounding can produce slightly different values. Allow ~6% error.
                if (abs($a - $b) > 16) {
                    $isok = false;

                    // Format as data uri so that we can easily investigate failing rendering tests.
                    $actual = self::formatDataUri('png', $actualRaw);
                    $expected = self::formatDataUri('png', $expectedRaw);
        
                    $x = $i % $icon->size;
                    $y = (int)($i / $icon->size);

                    $this->assertEquals($expected, $actual, "PNG rendering test for icon '$number'. $a != $b. Failed at pixel x: $x, y: $y.");
                    break;
                }
            }
        }

        // Call assertEquals to register that the test was performed
        if ($isok) {
            $this->assertEquals("", "", "PNG rendering test for icon '$number'.");
        }

        // SVG should always produce exactly the same output
        $actual = $icon->getImageDataUri('svg');
        $expected = self::formatDataUri('svg', file_get_contents(__DIR__ ."/$number.svg"));

        $this->assertEquals($expected, $actual, "SVG rendering test for icon '$number'.");
    }
}
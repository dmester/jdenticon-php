<?php

use Jdenticon\Rendering\RendererInterface;

include_once(__DIR__ . '/InitTests.php');

use Jdenticon\Identicon;
use Jdenticon\IdenticonStyle;
use Jdenticon\Rendering\IconGenerator;
use Jdenticon\Rendering\InternalPngRenderer;
use Jdenticon\Rendering\ImagickRenderer;

final class RenderingTest extends PHPUnit\Framework\TestCase
{
    public function testIcon39()
    {
        $icon = new Identicon([
            'value' => 39,
            'size' => 50,
            // config=ffffffff103c220a41560064
            'style' => [
                // Originally purple
                'hues' => [134 /*green*/, 0 /*red*/, 60 /*yellow*/],
                'backgroundColor' => '#ffffffff',
                'colorLightness' => [0.66, 0.86],
                'grayscaleLightness' => [0.00, 1.00],
                'colorSaturation' => 0.35,
                'grayscaleSaturation' => 0.10,
            ]
        ]);

        $this->performTest($icon, 39);
    }

    public function testIcon76()
    {
        $icon = new Identicon([
            'value' => 76,
            'size' => 50,
            // config=0000002a103c2d481d351328
            'style' => [
                // Originally blue
                'hues' => [134 /*green*/, 0 /*red*/, 60 /*yellow*/],

                'backgroundColor' => '#0000002a',
                'colorLightness' => [0.30, 0.54],
                'grayscaleLightness' => [0.19, 0.41],
                'colorSaturation' => 0.46,
                'grayscaleSaturation' => 0.72,
            ]
        ]);

        $this->performTest($icon, 76);
    }

    /**
     * @dataProvider provideSimpleIcons
     */
    public function testSimpleIcon(int $number)
    {
        $icon = new Identicon([
            'value' => $number,
            'size' => 50
        ]);

        $this->performTest($icon, $number);
    }

    public function provideSimpleIcons(): array
    {
        return [
            [12],
            [17],
            [19],
            [42],
            [44],
            [50],
            [56],
            [61],
            [64],
            [66],
            [67],
            [71],
            [73],
        ];
    }

    private static function formatDataUri(string $imageFormat, string $data)
    {
        $mimeType = $imageFormat == 'png' ? 'image/png' : 'image/svg+xml';
        $base64 = base64_encode($data);
        return "data:$mimeType;base64,$base64";
    }

    private function performTest(Identicon $icon, int $number): void
    {
        $imagickVersion = \Imagick::getVersion();
        $imagickVersion = $imagickVersion['versionString'];

        $this->performTestCore("InternalPngRenderer",
            new InternalPngRenderer($icon->size, $icon->size),
            $icon, $number, 16, 1);

        $this->performTestCore("ImagickRenderer ($imagickVersion)",
            new ImagickRenderer($icon->size, $icon->size),
            $icon, $number, 80, 80);

        // SVG should always produce exactly the same output
        $actual = $icon->getImageDataUri('svg');
        $expected = self::formatDataUri('svg', file_get_contents(__DIR__ ."/$number.svg"));

        $this->assertEquals($expected, $actual, "SVG rendering test for icon '$number'.");
    }

    private function extractPixels(string $data): array
    {
        $image = imagecreatefromstring($data);

        $r = [];
        $g = [];
        $b = [];
        $a = [];

        $width = imagesx($image);
        $height = imagesy($image);

        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $rgb = imagecolorat($image, $x, $y);
                $colors = imagecolorsforindex($image, $rgb);
                $r[] = $colors['red'];
                $g[] = $colors['green'];
                $b[] = $colors['blue'];
                $a[] = $colors['alpha'];
            }
        }

        imagedestroy($image);

        return [
            'R' => $r,
            'G' => $g,
            'B' => $b,
            'A' => $a
        ];
    }

    private function performTestCore(string $rendererName, RendererInterface $renderer, Identicon $icon, int $number, int $errorTolerance, int $errorCount): void
    {
        $icon->draw($renderer);

        $actualRaw = $renderer->getData();
        $expectedRaw = file_get_contents(__DIR__ ."/$number.png");

        $actualChannels = $this->extractPixels($actualRaw);
        $expectedChannels = $this->extractPixels($expectedRaw);

        $errors = 0;

        foreach ($actualChannels as $channel => $actual) {
            $expected = $expectedChannels[$channel];

            $actualCount = count($actual);
            $expectedCount = count($expected);
            $this->assertEquals($expectedCount, $actualCount, "$rendererName: PNG rendering test for icon '$number'. Length diff.");

            for ($i = 0; $i < $actualCount; $i++) {
                $a = $actual[$i] & 0xff;
                $b = $expected[$i] & 0xff;

                if (abs($a - $b) > $errorTolerance) {
                    if (++$errors >= $errorCount) {
                        // Format as data uri so that we can easily investigate failing rendering tests.
                        $actual = self::formatDataUri('png', $actualRaw);
                        $expected = self::formatDataUri('png', $expectedRaw);

                        $x = $i % $icon->size;
                        $y = (int)($i / $icon->size);

                        $this->assertEquals($expected, $actual, "$rendererName: PNG rendering test for icon '$number'. $channel: $a != $b. Failed at pixel x: $x, y: $y.");
                        break;
                    }
                }
            }
        }

        // Call assertEquals to register that the test was performed
        if ($errors < $errorCount) {
            $this->assertEquals("", "", "$rendererName: PNG rendering test for icon '$number'.");
        }
    }
}
<?php

use Jdenticon\Rendering\RendererInterface;

include_once(__DIR__ . '/InitTests.php');

use Jdenticon\Identicon;
use Jdenticon\IdenticonStyle;
use Jdenticon\Rendering\IconGenerator;
use Jdenticon\Rendering\InternalPngRenderer;
use Jdenticon\Rendering\ImagickRenderer;

/**
 * @group benchmark
 */
final class BenchmarkTest extends PHPUnit\Framework\TestCase
{
    public function testImagick(): void {
        $this->performBenchmark(true);
    }

    public function testInternalRenderer(): void {
        $this->performBenchmark(false);
    }

    private function performBenchmark(bool $enableImageMagick): void {
        // Warmup
        $icon = new Identicon([
            'value' => 'warnup',
            'size' => 50
        ]);
        $icon->setEnableImageMagick($enableImageMagick);
        $icon->getImageData();
        
        // Benchmark
        $iterations = 1000;
        $startTime = microtime(true);

        for ($i = 0; $i < $iterations; $i++) {
            $icon = new Identicon([
                'value' => $i,
                'size' => 50
            ]);
            $icon->setEnableImageMagick($enableImageMagick);
            $icon->getImageData();
        }

        $end = microtime(true);
        $durationPerIconMs = 1000 * ($end - $startTime) / $iterations;

        $rendererName = $enableImageMagick ? "Imagick" : "internal renderer";

        echo "\nBenchmark rendering time using $rendererName: $durationPerIconMs ms per icon\n";
        $this->assertLessThan(100, $durationPerIconMs);
    }
}
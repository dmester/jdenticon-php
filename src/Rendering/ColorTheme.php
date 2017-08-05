<?php
namespace Jdenticon\Rendering;

/**
 * Specifies the colors to be used in an identicon.
 */
class ColorTheme
{
    private $darkGray;
    private $midColor;
    private $lightGray;
    private $lightColor;
    private $darkColor;
    
    /**
     * Creates a new ColorTheme.
     *
     * @param float $hue The hue of the colored shapes in the range [0, 1].
     * @param \Jdenticon\IdenticonStyle $style The style that specifies the 
     *      lightness and saturation of the icon.
     */
    public function __construct($hue, \Jdenticon\IdenticonStyle $style)
    {
        $this->darkGray = Color::fromHsl(0, 0, $style->getGrayscaleLightness()[0]);
        $this->midColor = Color::fromHslCompensated($hue, $style->getSaturation(), 
            ($style->getColorLightness()[0] + $style->getColorLightness()[1]) / 2);
        $this->lightGray = Color::fromHsl(0, 0, $style->getGrayscaleLightness()[1]);
        $this->lightColor = Color::fromHslCompensated($hue, $style->getSaturation(), $style->getColorLightness()[1]);
        $this->darkColor = Color::fromHslCompensated($hue, $style->getSaturation(), $style->getColorLightness()[0]);
    }

    /**
     * Gets a color from this color theme by index.
     *
     * @param int $index Color index in the range [0, getCount()).
     * @return Jdenticon\Rendering\Color
     */
    public function getByIndex($index)
    {
        if ($index === 0) return $this->darkGray;
        if ($index === 1) return $this->midColor;
        if ($index === 2) return $this->lightGray;
        if ($index === 3) return $this->lightColor;
        if ($index === 4) return $this->darkColor;
        return null;
    }
    
    /**
     * Gets the number of available colors in this theme.
     *
     * @return int
     */
    public function getCount() 
    {
        return 5;
    }
}

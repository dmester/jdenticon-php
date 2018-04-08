<?php
namespace Jdenticon;

use Jdenticon\Rendering\Color;

/**
 * Specifies the color style of an identicon.
 */
class IdenticonStyle
{
    private $backgroundColor;
    private $padding;
    private $colorSaturation;
    private $grayscaleSaturation;
    private $colorLightness;
    private $grayscaleLightness;
    
    public function __construct()
    {
        $this->backgroundColor = self::getDefaultBackgroundColor();
        $this->padding = self::getDefaultPadding();
        $this->colorSaturation = self::getDefaultColorSaturation();
        $this->grayscaleSaturation = self::getDefaultGrayscaleSaturation();
        $this->colorLightness = self::getDefaultColorLightness();
        $this->grayscaleLightness = self::getDefaultGrayscaleLightness();
    }
    
    /**
     * Gets the default value of the Padding property. Resolves to 0.08.
     *
     * @return
     */
    public function getPadding()
    {
        return $this->padding;
    }
    
    /**
     * Gets the color of the identicon background.
     *
     * @return string
     */
    public function getBackgroundColor()
    {
        return $this->backgroundColor;
    }
    
    /**
     * Sets the color of the identicon background.
     *
     * @param $value string  Color.
     * @return \Jdenticon\IdenticonStyle
     */
    public function setBackgroundColor($value)
    {
        $this->backgroundColor = $value;
        return $this;
    }
    
    /**
     * Gets the saturation of the originally grayscale identicon shapes.
     *
     * @return double  Saturation in the range [0.0, 1.0].
     */
    public function getGrayscaleSaturation()
    {
        return $this->grayscaleSaturation;
    }
    
    /**
     * Sets the saturation of the originally grayscale identicon shapes.
     *
     * @param $value double  Saturation in the range [0.0, 1.0].
     * @return \Jdenticon\IdenticonStyle
     */
    public function setGrayscaleSaturation($value)
    {
        $this->grayscaleSaturation = $value;
        return $this;
    }
    
    /**
     * Gets the saturation of the colored identicon shapes.
     *
     * @return double  Saturation in the range [0.0, 1.0].
     */
    public function getColorSaturation()
    {
        return $this->colorSaturation;
    }
    
    /**
     * Sets the saturation of the colored identicon shapes.
     *
     * @param $value double  Saturation in the range [0.0, 1.0].
     * @return \Jdenticon\IdenticonStyle
     */
    public function setColorSaturation($value)
    {
        $this->colorSaturation = $value;
        return $this;
    }
    
    /**
     * Gets the value of the ColorLightness property.
     *
     * @return array(double, double)
     */
    public function getColorLightness()
    {
        return $this->colorLightness;
    }
    
    /**
     * Sets the value of the ColorLightness property.
     *
     * @param $value array(double, double)  Lightness range.
     * @return \Jdenticon\IdenticonStyle
     */
    public function setColorLightness(array $value)
    {
        $this->grayscaleLightness = $value;
        return $this;
    }
    
    /**
     * Gets the value of the GrayscaleLightness property. Resolves to [0.3f, 0.9f].
     *
     * @return array(double, double)
     */
    public function getGrayscaleLightness()
    {
        return $this->grayscaleLightness;
    }
    
    /**
     * Sets the value of the GrayscaleLightness property.
     *
     * @param $value array(double, double)  Lightness range.
     * @return \Jdenticon\IdenticonStyle
     */
    public function setGrayscaleLightness(array $value)
    {
        if (!is_array($value) ||
            !array_key_exists(0, $value) ||
            !array_key_exists(1, $value) ||
            !is_numeric($value[0]) ||
            !is_numeric($value[1]) ||
            $value[0] < 0 || $value[0] > 1 ||
            $value[1] < 0 || $value[1] > 1
        ) {
            throw new \InvalidArgumentException(
                "The value passed to setGrayscaleLightness was invalid. ".
                "Please check the documentation.");
        }
        $this->grayscaleLightness = $value;
        return $this;
    }
    
    
    
    /**
     * Gets the default value of the BackgroundColor property. Resolves to transparent.
     *
     * @return
     */
    public static function getDefaultBackgroundColor()
    {
        return Color::fromArgb(255, 255, 255, 255);
    }
    
    /**
     * Gets the default value of the Padding property. Resolves to 0.08.
     *
     * @return
     */
    public static function getDefaultPadding()
    {
        return 0.08;
    }
    
    /**
     * Gets the default value of the ColorSaturation property. Resolves to 0.5.
     *
     * @return
     */
    public static function getDefaultColorSaturation()
    {
        return 0.5;
    }
    
    /**
     * Gets the default value of the GrayscaleSaturation property. Resolves to 0.
     *
     * @return
     */
    public static function getDefaultGrayscaleSaturation()
    {
        return 0;
    }
    
    /**
     * Gets the default value of the ColorLightness property. Resolves to [0.4, 0.8].
     *
     * @return
     */
    public static function getDefaultColorLightness()
    {
        return array(0.4, 0.8);
    }
    
    /**
     * Gets the default value of the GrayscaleLightness property. Resolves to [0.3, 0.9].
     *
     * @return
     */
    public static function getDefaultGrayscaleLightness()
    {
        return array(0.3, 0.9);
    }
}

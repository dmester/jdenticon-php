<?php
namespace Jdenticon\Rendering;

/**
 * Represents a 24-bit color with a 8-bit alpha channel.
 */
class Color
{
    private static $lightnessCompensations = array(
        0.55, 0.5, 0.5, 0.46, 0.6, 0.55, 0.55);
    
    /**
     * The red component of the color in the range [0, 255].
     * @var int
     */
    public $r;
    
    /**
     * The green component of the color in the range [0, 255].
     * @var int
     */
    public $g;
    
    /**
     * The blue component of the color in the range [0, 255].
     * @var int
     */
    public $b;
    
    /**
     * The alpha component of the color in the range [0, 255].
     * @var int
     */
    public $a;

    // Users of the struct should use the static factory methods 
    // to create Color value.
    private function __construct()
    {
    }

    /**
     * Creates a Color from an ARGB value.
     *
     * @param int $a Alpha channel value in the range [0,255].
     * @param int $r Red component in the range [0,255].
     * @param int $g GReen component in the range [0,255].
     * @param int $b Blue component in the range [0,255].
     */
    public static function fromArgb($a, $r, $g, $b)
    {
        $color = new Color();
        $color->r = $r;
        $color->g = $g;
        $color->b = $b;
        $color->a = $a;
        return $color;
    }

    /**
     * Creates a Color from an RGB value.
     *
     * @param int $a Alpha channel value in the range [0,255].
     * @param int $r Red component in the range [0,255].
     * @param int $g GReen component in the range [0,255].
     * @param int $b Blue component in the range [0,255].
     */
    public static function fromRgb($r, $g, $b)
    {
        return self::fromArgb(255, $r, $g, $b);
    }

    /**
     * Creates a Color instance from HSL color parameters.
     *
     * @param float $hue Hue in the range [0, 1]
     * @param float $saturation Saturation in the range [0, 1]
     * @param float $lightness Lightness in the range [0, 1]
     */
    public static function fromHsl($hue, $saturation, $lightness)
    {
        if ($hue < 0) $hue = 0;
        if ($hue > 1) $hue = 1;
        
        if ($saturation < 0) $saturation = 0;
        if ($saturation > 1) $saturation = 1;
        
        if ($lightness < 0) $lightness = 0;
        if ($lightness > 1) $lightness = 1;

        // Based on http://www.w3.org/TR/2011/REC-css3-color-20110607/#hsl-color
        if ($saturation == 0) {
            $value = (int)($lightness * 255);
            return self::fromArgb(255, $value, $value, $value);
        }
        else {
            if ($lightness <= 0.5) {
                $m2 = $lightness * ($saturation + 1);
            }
            else {
                $m2 = $lightness + $saturation - $lightness * $saturation;
            }
            
            $m1 = $lightness * 2 - $m2;

            return self::fromArgb(255,
                self::hueToRgb($m1, $m2, $hue * 6 + 2),
                self::hueToRgb($m1, $m2, $hue * 6),
                self::hueToRgb($m1, $m2, $hue * 6 - 2));
        }
    }
    
    /**
     * Creates a Color> instance from HSL color parameters and will compensate 
     * the lightness for hues that appear to be darker than others.
     *
     * @param float $hue Hue in the range [0, 1]
     * @param float $saturation Saturation in the range [0, 1]
     * @param float $lightness Lightness in the range [0, 1]
     */
    public static function fromHslCompensated($hue, $saturation, $lightness)
    {
        if ($hue < 0) $hue = 0;
        if ($hue > 1) $hue = 1;
        
        $lightnessCompensation = self::$lightnessCompensations[(int)($hue * 6 + 0.5)];
        
        // Adjust the input lightness relative to the compensation
        $lightness = $lightness < 0.5 ?
            $lightness * $lightnessCompensation * 2 : 
            $lightnessCompensation + ($lightness - 0.5) * (1 - $lightnessCompensation) * 2;

        return self::fromHsl($hue, $saturation, $lightness);
    }

    // Helper method for FromHsl
    private static function hueToRgb($m1, $m2, $h)
    {
        if ($h < 0) {
            $h = $h + 6;
        }
        elseif ($h > 6) {
            $h = $h - 6;
        }
        
        if ($h < 1) {
            $r = $m1 + ($m2 - $m1) * $h;
        }
        elseif ($h < 3) {
            $r = $m2;
        }
        elseif ($h < 4) {
            $r = $m1 + ($m2 - $m1) * (4 - $h);
        }
        else {
            $r = $m1;
        }
        
        return (int)(255 * $r);
    }

    /**
     * Gets the argb value of this color.
     *
     * @return int
     */
    public function toRgba()
    {
        return 
            ($this->r << 24) |
            ($this->g << 16) |
            ($this->b << 8) |
            ($this->a);
    }

    /**
     * Gets a hexadecimal representation of this color on the format #rrggbbaa.
     *
     * @return string
     */
    public function __toString()
    {
        return '#' . bin2hex(pack('N', $this->toRgba()));
    }
    
    /**
     * Gets a hexadecimal representation of this color on the format #rrggbbaa.
     *
     * @return string
     */
    public function toHexString($length = 8)
    {
        if ($length === 8) {
            return $this->__toString();
        }
        return '#' . substr(bin2hex(pack('N', $this->toRgba())), 0, 6);
    }
}

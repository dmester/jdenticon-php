<?php
namespace Jdenticon\Rendering;

/**
 * Specifies the bounds of a 2D rectangle.
 */
class Rectangle
{
    /**
     * The X coordinate of the left side of the rectangle.
     *
     * @var float
     */
    public $x;
    
    /**
     * The Y coordinate of the top side of the rectangle.
     *
     * @var float
     */
    public $y;
    
    /**
     * The width of the rectangle.
     * @var float
     */
    public $width;
    
    /**
     * The height of the rectangle.
     * @var float
     */
    public $height;

    /**
     * Creates a new Rectangle.
     *
     * @param float $x The X coordinate of the left edge of the rectangle.
     * @param float $y The Y coordinate of the top edge of the rectangle.
     * @param float $width The width of the rectangle.
     * @param float $height The height of the rectangle.
     */
    public function __construct($x, $y, $width, $height)
    {
        $this->x = $x;
        $this->y = $y;
        $this->width = $width;
        $this->height = $height;
    }
}

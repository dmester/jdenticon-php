<?php
namespace Jdenticon\Shapes;

/**
 * Specifies in which cell a shape will be rendered.
 */
class ShapePosition
{
    /**
     * The horizontal cell index measured left to right.
     *
     * @var int
     */
    public $x;
    
    /**
     * The vertical cell index measured from the top.
     *
     * @var int
     */
    public $y;

    /**
     * Creates a new ShapePosition instance.
     *
     * @param int $x The x-coordinate of the containing cell.
     * @param int $y The y-coordinate of the containing cell.
     */
    public function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }
}


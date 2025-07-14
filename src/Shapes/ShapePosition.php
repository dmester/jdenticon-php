<?php
/**
 * This file is part of Jdenticon for PHP.
 * https://github.com/dmester/jdenticon-php/
 * 
 * Copyright (c) 2018 Daniel Mester Pirttijärvi
 * 
 * For full license information, please see the LICENSE file that was 
 * distributed with this source code.
 */

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
    public int $x;
    
    /**
     * The vertical cell index measured from the top.
     *
     * @var int
     */
    public int $y;

    /**
     * Creates a new ShapePosition instance.
     *
     * @param int $x The x-coordinate of the containing cell.
     * @param int $y The y-coordinate of the containing cell.
     */
    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }
}


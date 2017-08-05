<?php

namespace Jdenticon\Canvas\Rasterization;

class EdgeIntersection
{
    public $fromX;
    public $width;
    public $edge;
 
    public function __construct($fromX, $width, $edge) 
    {
        $this->fromX = $fromX;
        $this->width = $width;
        $this->edge = $edge;
    }
}


<?php
namespace Jdenticon\Canvas\Rasterization;

class EdgeSuperSampleIntersection
{
    public $x;
    public $edge;
    
    public function __construct($x, $edge) 
    {
        $this->x = $x;
        $this->edge = $edge;
    }
}
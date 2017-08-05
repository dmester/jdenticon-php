<?php
namespace Jdenticon\Canvas\Rasterization;

class Layer
{
    public $polygonId;
    public $color;
    public $winding;
    public $windingRule;
    
    public $nextLayer;
    
    public function __construct($polygonId, $color, $winding, $windingRule)
    {
        $this->polygonId = $polygonId;
        $this->color = $color;
        $this->winding = $winding;
        $this->windingRule = $windingRule;
    }
}

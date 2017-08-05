<?php
namespace Jdenticon\Canvas\Rasterization;

class SuperSampleRange
{
    public $fromX;
    public $toXExcl;
    public $edges;
    public $width;
    
    public function __construct($fromX, $toXExcl) 
    {
        $this->fromX = $fromX;
        $this->toXExcl = $toXExcl;
        $this->edges = array();
    }
}
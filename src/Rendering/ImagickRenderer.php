<?php
namespace Jdenticon\Rendering;

/**
 * Renders icons as PNG using ImageMagick.
 */
class ImagickRenderer extends AbstractRenderer
{
    private $svg;
    
    /**
     * Gets the MIME type of the renderer output.
     *
     * @return string
     */
    public function getMimeType()
    {
        return 'image/png';
    }

    /**
     * Creates an instance of the class ImagickRenderer.
     *
     * @param int $width The width of the icon in pixels.
     * @param int $height The height of the icon in pixels.
     */
    public function __construct($width, $height)
    {
        parent::__construct();
        $this->svg = new SvgRenderer($width, $height);
    }

    /**
     * Adds a circle without translating its coordinates.
     *
     * @param float $x The x-coordinate of the bounding rectangle 
     *      upper-left corner.
     * @param float $y The y-coordinate of the bounding rectangle 
     *      upper-left corner.
     * @param float $size The size of the bounding rectangle.
     * @param bool $counterClockwise If true the circle will be drawn 
     *      counter clockwise.
     */
    protected function addCircleNoTransform($x, $y, $size, $counterClockwise)
    {
        $this->svg->addCircleNoTransform($x, $y, $size, $counterClockwise);
    }

    /**
     * Adds a polygon without translating its coordinates.
     *
     * @param array $points An array of the points that the polygon consists of.
     */
    protected function addPolygonNoTransform($points)
    {
        $this->svg->addPolygonNoTransform($points);
    }
    
    /**
     * Sets the background color of the icon.
     *
     * @param \Jdenticon\Color $color The background color.
     */
    public function setBackgroundColor(\Jdenticon\Color $color)
    {
        parent::setBackgroundColor($color);
        $this->svg->setBackgroundColor($color);
    }

    /**
     * Begins a new shape. The shape should be ended with a call to endShape.
     *
     * @param \Jdenticon\Color $color The color of the shape.
     */
    public function beginShape(\Jdenticon\Color $color)
    {
        $this->svg->beginShape($color);
    }
    
    /**
     * Ends the currently drawn shape.
     */
    public function endShape()
    {
        $this->svg->endShape();
    }

    /**
     * Gets the output from the renderer.
     *
     * @return string
     */
    public function getData()
    {
        $svg = $this->svg->getData();
        $imagick = new \Imagick();
        $imagick->readImageBlob('<?xml version="1.0" encoding="UTF-8" ?>'.$svg);
        $imagick->setImageFormat('png');
        $data = $imagick->getImageBlob();
        $imagick->destroy();
        return $data;
    }
}

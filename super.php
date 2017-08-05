<?php



abstract class AbstractRenderer implements RendererInterface
{
    private $transform;
    protected $backgroundColor;
    
    public function __construct()
    {
        $this->transform = Transform::getEmpty();
    }
    
    /**
     * Sets the current transform that will be applied on all coordinates before being rendered
     * to the target image.
     * @param  \Jdenticon\Rendering\Transform $transform  The transform to set. If NULL is specified any existing transform is removed.
     */
    public function setTransform(\Jdenticon\Rendering\Transform $transform) 
    {
        $this->transform = $transform === null ? 
            Transform::getEmpty() : $transform;
    }
    
    /**
     * Gets the current transform that will be applied on all coordinates before being rendered
     * to the target image.
     * @return  \Jdenticon\Rendering\Transform
     */
    public function getTransform() 
    {
        return $this->transform;
    }
    
    /**
     * Adds a polygon without translating its coordinates.
     * @param  array $points  An array of the points that the polygon consists of.
     */
    abstract protected function addPolygonNoTransform($points);

    /**
     * Adds a circle without translating its coordinates.
     * @param  float $x  The x-coordinate of the bounding rectangle upper-left corner.
     * @param  float $y  The y-coordinate of the bounding rectangle upper-left corner.
     * @param  float $size  The size of the bounding rectangle.
     * @param  bool $counterClockwise  If true the circle will be drawn counter clockwise.
     */
    abstract protected function addCircleNoTransform($x, $y, $size, $counterClockwise);

    /**
     * Sets the background color of the image.
     * @param  string $color  The image background color.
     */
    public function setBackgroundColor(Color $color)
    {
        $this->backgroundColor = $color;
    }
    
    /**
     * Gets the background color of the image.
     * @return string
     */
    public function getBackgroundColor()
    {
        return $this->backgroundColor;
    }
    
    /**
     * Gets the MIME type of the renderer output.
     * @return string
     */
    abstract public function getMimeType();
    
    /**
     * Begins a new shape. The shape should be ended with a call to endShape.
     * @param  string $color  The color of the shape.
     */
    abstract public function beginShape(Color $color);
    
    /**
     * Ends the currently drawn shape.
     */
    abstract public function endShape();

    private function addPolygonCore(array $points, $invert)
    {
        $transformedPoints = array();
        foreach ($points as $point) {
            $transformedPoints[] = 
                $this->transform->transformPoint($point->x, $point->y);
        }
        
        if ($invert)
        {
            $transformedPoints = array_reverse($transformedPoints);
        }
        
        //var_dump($transformedPoints);
        
        $this->addPolygonNoTransform($transformedPoints);
    }

    /**
     * Adds a rectangle to the image.
     *
     * @param  float $x  The x-coordinate of the rectangle upper-left corner.
     * @param  float $y  The y-coordinate of the rectangle upper-left corner.
     * @param  float $width  The width of the rectangle.
     * @param  float $height  The height of the rectangle.
     * @param  bool $invert  If true the area of the rectangle will be removed from the filled area.
     */
    public function addRectangle($x, $y, $width, $height, $invert = false)
    {
        $this->addPolygonCore(array(
            new Point($x, $y),
            new Point($x + $width, $y),
            new Point($x + $width, $y + $height),
            new Point($x, $y + $height),
        ), $invert);
    }

    /**
     * Adds a circle to the image.
     *
     * @param  float $x  The x-coordinate of the bounding rectangle upper-left corner.
     * @param  float $y  The y-coordinate of the bounding rectangle upper-left corner.
     * @param  float $size  The size of the bounding rectangle.
     * @param  bool $invert  If true the area of the circle will be removed from the filled area.
     */
    public function addCircle($x, $y, $size, $invert = false)
    {
        $northWest = $this->transform->transformPoint($x, $y, $size, $size);
        $this->addCircleNoTransform($northWest->x, $northWest->y, $size, $invert);
    }

    /**
     * Adds a polygon to the image.
     * @param  array $points  Array of points that the polygon consists of.
     * @param  bool $invert  If true the area of the polygon will be removed from the filled area.
     */
    public function addPolygon($points, $invert = false)
    {
        $this->addPolygonCore($points, $invert);
    }

    /**
     * Adds a triangle to the image.
     * @param  float $x  The x-coordinate of the bounding rectangle upper-left corner.
     * @param  float $y  The y-coordinate of the bounding rectangle upper-left corner.
     * @param  float $width  The width of the bounding rectangle.
     * @param  float $height  The height of the bounding rectangle.
     * @param  float $direction  The direction of the 90 degree corner of the triangle.
     * @param  bool $invert  If true the area of the triangle will be removed from the filled area.
     */
    public function addTriangle($x, $y, $width, $height, $direction, $invert = false)
    {
        $points = array(
            new Point($x + $width, $y),
            new Point($x + $width, $y + $height),
            new Point($x, $y + $height),
            new Point($x, $y)
        );

        array_splice($points, $direction, 1);
        
        $this->addPolygonCore($points, $invert);
    }

    /**
     * Adds a rhombus to the image.
     * @param  float $x  The x-coordinate of the bounding rectangle upper-left corner.
     * @param  float $y  The y-coordinate of the bounding rectangle upper-left corner.
     * @param  float $width  The width of the bounding rectangle.
     * @param  float $height  The height of the bounding rectangle.
     * @param  bool $invert  If true the area of the rhombus will be removed from the filled area.
     */
    public function addRhombus($x, $y, $width, $height, $invert = false)
    {
        $this->addPolygonCore(array(
            new Point($x + $width / 2, $y),
            new Point($x + $width, $y + $height / 2),
            new Point($x + $width / 2, $y + $height),
            new Point($x, $y + $height / 2),
        ), $invert);
    }
    
    /**
     * Gets the output from the renderer.
     * @return string
     */
    abstract public function getData();
}

class IconGenerator
{
    private $defaultShapes;
    private static $instance;
    
    protected function __construct()
    {
        $this->defaultShapes = array(
            // Sides
            new ShapeCategory(
                /*$colorIndex=*/ 8,
                /*$shapes=*/ ShapeDefinitions::getOuterShapes(),
                /*$shapeIndex=*/ 2,
                /*$rotationIndex=*/ 3,
                /*$positions=*/ array(1,0, 2,0, 2,3, 1,3, 0,1, 3,1, 3,2, 0,2)
            ),
            
            // Corners
            new ShapeCategory(
                /*$colorIndex=*/ 9,
                /*$shapes=*/ ShapeDefinitions::getOuterShapes(),
                /*$shapeIndex=*/ 4,
                /*$rotationIndex=*/ 5,
                /*$positions=*/ array(0,0, 3,0, 3,3, 0,3)
            ),
            
            // Center
            new ShapeCategory(
                /*$colorIndex=*/ 10,
                /*$shapes=*/ ShapeDefinitions::getCenterShapes(),
                /*$shapeIndex=*/ 1,
                /*$rotationIndex=*/ null,
                /*$positions=*/ array(1,1, 2,1, 2,2, 1,2)
            )
        );
    }
    
    public static function getDefaultGenerator()
    {
        if (self::$instance === null) 
        {
            self::$instance = new IconGenerator();
        }
        return self::$instance;
    }
    
    /**
     * Gets the number of cells in each direction of the icons generated by this IconGenerator.
     * @return int
     */
    public function getCellCount()
    {
        return 4;
    }

    /**
     * Determines the hue to be used in an icon for the specified hash.
     * @return float Hue in the range [0, 1].
     */
    protected static function getHue($hash)
    {
        $value = hexdec(substr($hash, -7));
        return $value / 0xfffffff;
    }

    /**
     * Determines whether $newValue is duplicated in $source if all values 
     * in $duplicateValues are determined to be equal.
     * @return bool
     */
    private static function isDuplicate(array $source, $newValue, array $duplicateValues)
    {
        if (in_array($newValue, $duplicateValues, true)) {
            foreach ($duplicateValues as $value) {
                if (in_array($value, $source, true)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Gets the specified octet from a byte array.
     * @param string $hash The hexstring from which the octet will be retrieved.
     * @param int $index The zero-based index of the octet to be returned.
     * @return int
     */
    protected static function getOctet($hash, $index)
    {
        return hexdec($hash[$index]);
    }

    /**
     * Gets an array of the shape categories to be rendered in icons generated by this IconGenerator".
     * @return array
     */
    protected function getCategories()
    {
        return $this->defaultShapes;
    }

    /**
     * Gets an enumeration of individual shapes to be rendered in an icon for a specific hash.
     * 
     * @param \Jdenticon\Rendering\ColorTheme $colorTheme A color theme specifying the colors to be used in the icon.
     * @param string $hash The hash for which the shapes will be returned.
     * @return array(Jdenticon\Shapes\Shape)
     */
    protected function getShapes($colorTheme, $hash)
    {
        $usedColorThemeIndexes = array();
        $categories = self::getCategories();
        $shapes = array();
        $colorCount = $colorTheme->getCount();
        
        foreach ($categories as $category) 
        {
            $colorThemeIndex = self::getOctet($hash, $category->colorIndex) % $colorCount;

            if (self::isDuplicate($usedColorThemeIndexes, $colorThemeIndex, array(0, 4)) || // Disallow dark gray and dark color combo
                self::isDuplicate($usedColorThemeIndexes, $colorThemeIndex, array(2, 3)))   // Disallow light gray and light color combo
            {
                $colorThemeIndex = 1;
            }

            $usedColorThemeIndexes[] = $colorThemeIndex;

            $startRotationIndex = $category->rotationIndex === null ? 0 : self::getOctet($hash, $category->rotationIndex);
            $shapeIndex = self::getOctet($hash, $category->shapeIndex) % count($category->shapes);
            $shape = $category->shapes[$shapeIndex];
            
            $shapes[] = new Shape(
                /*$definition=*/ $shape,
                /*$color=*/ $colorTheme->getByIndex($colorThemeIndex),
                /*$positions=*/ $category->positions,
                /*$startRotationIndex=*/ $startRotationIndex
            );
        }
        
        return $shapes;
    }

    /**
     * Creates a quadratic copy of the specified {@link \Jdenticon\Rendering\Rectangle} with a 
     * multiple of the cell count as size.
     * @param \Jdenticon\Rendering\Rectangle $rect The rectangle to be normalized.
     */
    protected function normalizeRectangle(\Jdenticon\Rendering\Rectangle $rect)
    {
        $size = (int)min($rect->width, $rect->height);
        
        // Make size a multiple of the cell count
        $size -= $size % $this->getCellCount();
        
        return new Rectangle(
            (int)($rect->x + ($rect->width - $size) / 2),
            (int)($rect->y + ($rect->height - $size) / 2),
            $size,
            $size);
    }

    /**
     * Renders the background of an icon.
     * @param \Jdenticon\Rendering\RendererInterface $renderer The renderer to be used for rendering the icon on the target surface.
     * @param \Jdenticon\Rendering\Rectangle $rect The outer bounds of the icon.
     * @param \Jdenticon\IdenticonStyle $style The style of the icon.
     * @param \Jdenticon\Rendering\ColorTheme $colorTheme A color theme specifying the colors to be used in the icon.
     * @param string $hash The hash to be used as basis for the generated icon.
     */
    protected function renderBackground(
        \Jdenticon\Rendering\RendererInterface $renderer, 
        \Jdenticon\Rendering\Rectangle $rect,
        \Jdenticon\IdenticonStyle $style, 
        \Jdenticon\Rendering\ColorTheme $colorTheme, 
        $hash)
    {
        $renderer->setBackgroundColor($style->getBackgroundColor());
    }
    
    /**
     * Renders the foreground of an icon.
     * @param \Jdenticon\Rendering\RendererInterface $renderer The renderer to be used for rendering the icon on the target surface.
     * @param \Jdenticon\Rendering\Rectangle $rect The outer bounds of the icon.
     * @param \Jdenticon\IdenticonStyle $style The style of the icon.
     * @param \Jdenticon\Rendering\ColorTheme $colorTheme A color theme specifying the colors to be used in the icon.
     * @param string $hash The hash to be used as basis for the generated icon.
     */
    protected function renderForeground(
        \Jdenticon\Rendering\RendererInterface $renderer, 
        \Jdenticon\Rendering\Rectangle $rect,
        \Jdenticon\IdenticonStyle $style, 
        \Jdenticon\Rendering\ColorTheme $colorTheme, 
        $hash)
    {
        // Ensure rect is quadratic and a multiple of the cell count
        $normalizedRect = $this->normalizeRectangle($rect);
        $cellSize = $normalizedRect->width / $this->getCellCount();

        foreach ($this->getShapes($colorTheme, $hash) as $shape) 
        {
            $rotation = $shape->startRotationIndex;
            
            $renderer->beginShape($shape->color);
            
            $positionCount = count($shape->positions);
            for ($i = 0; $i + 1 < $positionCount; $i += 2) {
                $renderer->setTransform(new Transform(
                    $normalizedRect->x + $shape->positions[$i + 0] * $cellSize,
                    $normalizedRect->y + $shape->positions[$i + 1] * $cellSize,
                    $cellSize, $rotation++ % 4));

                $shape->definition->__invoke($renderer, $cellSize, $i / 2);
            }
            
            $renderer->endShape();
        }
    }

    /**
     * Generates an identicon for the specified hash.
     * @param \Jdenticon\Rendering\RendererInterface $renderer The renderer to be used for rendering the icon on the target surface.
     * @param \Jdenticon\Rendering\Rectangle $rect The outer bounds of the icon.
     * @param \Jdenticon\IdenticonStyle $style The style of the icon.
     * @param string $hash The hash to be used as basis for the generated icon.
     */
    public function generate(
        \Jdenticon\Rendering\RendererInterface $renderer, 
        \Jdenticon\Rendering\Rectangle $rect,
        \Jdenticon\IdenticonStyle $style, 
        $hash)
    {
        $hue = self::getHue($hash);
        $colorTheme = new ColorTheme($hue, $style);

        $this->renderBackground($renderer, $rect, $style, $colorTheme, $hash);
        $this->renderForeground($renderer, $rect, $style, $colorTheme, $hash);
    }
}


class PngRenderer extends AbstractRenderer
{
    private $canvas;
    private $ctx;

    /**
     * Creates an instance of the class ImagickRenderer.
     * @param int $width  The width of the icon in pixels.
     * @param int $height  The height of the icon in pixels.
     */
    public function __construct($width, $height)
    {
        parent::__construct();
        $this->canvas = new Canvas($width, $height);
        $this->ctx = $this->canvas->getContext();
    }
    
    /**
     * Gets the MIME type of the renderer output.
     * @return string
     */
    public function getMimeType()
    {
        return 'image/png';
    }

    /**
     * Adds a circle without translating its coordinates.
     * @param float $x  The x-coordinate of the bounding rectangle upper-left corner.
     * @param float $y  The y-coordinate of the bounding rectangle upper-left corner.
     * @param float $size  The size of the bounding rectangle.
     * @param bool $counterClockwise  If true the circle will be drawn counter clockwise.
     */
    protected function addCircleNoTransform($x, $y, $size, $counterClockwise)
    {
        $radius = $size / 2;
        $this->ctx->moveTo($x + $size, $y + $radius);
        $this->ctx->arc($x + $radius, $y + $radius, $radius, 0, M_PI * 2, $counterClockwise);
        $this->ctx->closePath();
    }

    /**
     * Adds a polygon without translating its coordinates.
     * @param array $points  An array of the points that the polygon consists of.
     */
    protected function addPolygonNoTransform($points)
    {
        $pointCount = count($points);
        $this->ctx->moveTo($points[0]->x, $points[0]->y);
        for ($i = 1; $i < $pointCount; $i++) {
            $this->ctx->lineTo($points[$i]->x, $points[$i]->y);
        }
        $this->ctx->closePath();
    }

    /**
     * Sets the background color of the icon.
     * @param \Jdenticon\Rendering\Color $color  The background color.
     */
    public function setBackgroundColor(Color $color)
    {
        parent::setBackgroundColor($color);
        $this->canvas->backColor = $this->backgroundColor->toRgba();
    }

    /**
     * Begins a new shape. The shape should be ended with a call to endShape.
     * @param \Jdenticon\Rendering\Color $color  The color of the shape.
     */
    public function beginShape(Color $color)
    {
        $this->ctx->fillStyle = $color->toRgba();
        $this->ctx->beginPath();
    }
    
    /**
     * Ends the currently drawn shape.
     */
    public function endShape()
    {
        $this->ctx->fill();
    }

    /**
     * Gets the output from the renderer.
     * @return string
     */
    public function getData()
    {
        return $this->canvas->toPng(array('Software' => 'Jdenticon'));
    }
}
class Point
{
    /**
     * Creates a new Point.
     * @param float $x  X coordinate.
     * @param float $y  Y coordinate.
     */
    public function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * The X coordinate of this point.
     * @var float
     */
    public $x;

    /**
     * The Y coordinate of this point.
     * @var float
     */
    public $y;

    /**
     * Gets a string representation of the point.
     * @return string
     */
    public function __toString()
    {
        return $this->x + ", " + $this->y;
    }
}

class Rectangle
{
    /**
     * The X coordinate of the left side of the rectangle.
     * @var float
     */
    public $x;
    
    /**
     * The Y coordinate of the top side of the rectangle.
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


interface RendererInterface
{
    /**
     * Sets the current transform that will be applied on all coordinates before being rendered
     * to the target image.
     * @param \Jdenticon\Rendering\Transform $transform The transform to set. If NULL is specified any existing transform is removed.
     */
    public function setTransform(\Jdenticon\Rendering\Transform $transform);
    
    /**
     * Gets the current transform that will be applied on all coordinates before being rendered
     * to the target image.
     * @return \Jdenticon\Rendering\Transform
     */
    public function getTransform();
    
    /**
     * Sets the background color of the image.
     * @param \Jdenticon\Rendering\Color $color  The image background color.
     */
    public function setBackgroundColor(Color $color);
    
    /**
     * Gets the background color of the image.
     * @return \Jdenticon\Rendering\Color
     */
    public function getBackgroundColor();
    
    /**
     * Gets the MIME type of the renderer output.
     * @return string
     */
    public function getMimeType();
    
    /**
     * Begins a new shape. The shape should be ended with a call to endShape.
     * @param \Jdenticon\Rendering\Color $color  The color of the shape.
     */
    public function beginShape(Color $color);
    
    /**
     * Ends the currently drawn shape.
     */
    public function endShape();

    /**
     * Adds a rectangle to the image.
     * @param float $x  The x-coordinate of the rectangle upper-left corner.
     * @param float $y  The y-coordinate of the rectangle upper-left corner.
     * @param float $width  The width of the rectangle.
     * @param float $height  The height of the rectangle.
     * @param bool $invert  If true the area of the rectangle will be removed from the filled area.
     */
    public function addRectangle($x, $y, $width, $height, $invert = false);

    /**
     * Adds a circle to the image.
     * @param float $x  The x-coordinate of the bounding rectangle upper-left corner.
     * @param float $y  The y-coordinate of the bounding rectangle upper-left corner.
     * @param float $size  The size of the bounding rectangle.
     * @param bool $invert  If true the area of the circle will be removed from the filled area.
     */
    public function addCircle($x, $y, $size, $invert = false);

    /**
     * Adds a polygon to the image.
     * @param array $points  Array of points that the polygon consists of.
     * @param bool $invert  If true the area of the polygon will be removed from the filled area.
     */
    public function addPolygon($points, $invert = false);

    /**
     * Adds a triangle to the image.
     * @param float $x  The x-coordinate of the bounding rectangle upper-left corner.
     * @param float $y  The y-coordinate of the bounding rectangle upper-left corner.
     * @param float $width  The width of the bounding rectangle.
     * @param float $height  The height of the bounding rectangle.
     * @param float $direction  The direction of the 90 degree corner of the triangle.
     * @param bool $invert  If true the area of the triangle will be removed from the filled area.
     */
    public function addTriangle($x, $y, $width, $height, $direction, $invert = false);

    /**
     * Adds a rhombus to the image.
     * @param float $x  The x-coordinate of the bounding rectangle upper-left corner.
     * @param float $y  The y-coordinate of the bounding rectangle upper-left corner.
     * @param float $width  The width of the bounding rectangle.
     * @param float $height  The height of the bounding rectangle.
     * @param bool $invert  If true the area of the rhombus will be removed from the filled area.
     */
    public function addRhombus($x, $y, $width, $height, $invert = false);
    
    /**
     * Gets the output from the renderer.
     * @return string
     */
    public function getData();
}


class SvgPath
{
    private $dataString;
    
    public function __construct() 
    {
        $this->dataString = '';
    }

    /**
     * Adds a circle to the SVG.
     * @param float $x X coordinate of the left side of the containing rectangle.
     * @param float $y Y coordinate of the top side of the containing rectangle.
     * @param float $size The diameter of the circle.
     * @param bool $counterClockwise If true the circle will be drawn counter clockwise. This affects the rendering since the evenodd filling rule is used by Jdenticon.
     */
    public function addCircle($x, $y, $size, $counterClockwise)
    {
        $sweepFlag = $counterClockwise ? '0' : '1';
        $radiusAsString = number_format($size / 2, 2, '.', '');

        $this->dataString .=
            'M'. number_format($x, 2, '.', '') .' '. number_format($y + $size / 2, 2, '.', '').
            'a'. $radiusAsString .','. $radiusAsString .' 0 1,'. $sweepFlag .' '. number_format($size, 2, '.', '') .',0'.
            'a'. $radiusAsString .','. $radiusAsString .' 0 1,'. $sweepFlag .' '. number_format(-$size, 2, '.', '') .',0';
    }

    /**
     * Adds a polygon to the SVG.
     * @param array(\Jdenticon\Rendering\Point) $points The corners of the polygon.
     */
    public function addPolygon($points)
    {
        $pointCount = count($points);

        $this->dataString .= 'M'. number_format($points[0]->x, 2, '.', '') .' '. number_format($points[0]->y, 2, '.', '');

        for ($i = 1; $i < $pointCount; $i++)
        {
            $this->dataString .= 'L'. number_format($points[$i]->x, 2, '.', '') .' '. number_format($points[$i]->y, 2, '.', '');
        }

        $this->dataString .= 'Z';
    }

    /**
     * Gets the path as a SVG path string.
     * @return string
     */
    public function __toString()
    {
        return $this->dataString;
    }
}


class SvgRenderer extends AbstractRenderer
{
    private $pathsByColor = array();
    private $path;
    private $width;
    private $height;

    /**
     * Creates a new SvgRenderer.
     * @param int $width The width of the icon in pixels.
     * @param int $height The height of the icon in pixels.
     */
    public function __construct($width, $height)
    {
        $this->width = $width;
        $this->height = $height;
    }
    
    /**
     * Gets the MIME type of the renderer output.
     * @return string
     */
    public function getMimeType()
    {
        return 'image/svg+xml';
    }

    /**
     * Adds a circle without translating its coordinates.
     * @param float $x  The x-coordinate of the bounding rectangle upper-left corner.
     * @param float $y  The y-coordinate of the bounding rectangle upper-left corner.
     * @param float $size  The size of the bounding rectangle.
     * @param bool $counterClockwise  If true the circle will be drawn counter clockwise.
     */
    protected function addCircleNoTransform($x, $y, $size, $counterClockwise)
    {
        $this->path->addCircle($x, $y, $size, $counterClockwise);
    }

    /**
     * Adds a polygon without translating its coordinates.
     * @param array $points  An array of the points that the polygon consists of.
     */
    protected function addPolygonNoTransform($points)
    {
        $this->path->addPolygon($points);
    }

    /**
     * Begins a new shape. The shape should be ended with a call to endShape.
     * @param \Jdenticon\Rendering\Color $color  The color of the shape.
     */
    public function beginShape(Color $color)
    {
        $colorString = $color->toHexString(6);
        
        if (isset($this->pathsByColor[$colorString])) 
        {
            $this->path = $this->pathsByColor[$colorString];
        }
        else 
        {
            $this->path = new SvgPath();
            $this->pathsByColor[$colorString] = $this->path;
        }
    }
    
    /**
     * Ends the currently drawn shape.
     */
    public function endShape()
    {
    }
    
    /**
     * Generates an SVG string of the renderer output.
     * @param bool $fragment If true an SVG string without the root svg element will be rendered.
     */
    public function getData($fragment = false)
    {
        $svg = '';
        $widthAsString = number_format($this->width, 2, '.', '');
        $heightAsString = number_format($this->height, 2, '.', '');
        
        if (!$fragment)
        {
            $svg .= '<svg xmlns="http://www.w3.org/2000/svg" width="' .
                $widthAsString .'" height="'. $heightAsString .'" viewBox="0 0 '.
                $widthAsString .' '. $heightAsString .'" preserveAspectRatio="xMidYMid meet">';
        }

        if ($this->backgroundColor->a > 0)
        {
            $opacity = (float)$this->backgroundColor->a / 255;
            $svg .= '<rect fill="'. $this->backgroundColor->toHexString(6) .'" fill-opacity="'.
                number_format($opacity, 2, '.', '').
                '" x="0" y="0" width="'. $widthAsString .'" height="'. $heightAsString .'"/>';
        }
        
        foreach ($this->pathsByColor as $color => $path)
        {
            $svg .= "<path fill=\"$color\" d=\"$path\"/>";
        }

        if (!$fragment)
        {
            $svg .= '</svg>';
        }
        
        return $svg;
    }
}

class Transform
{
    private $x;
    private $y;
    private $size;
    private $rotation;

    /**
     * Creates a new Transform.
     * @param float $x The x-coordinate of the upper left corner of the transformed rectangle.
     * @param float $y The y-coordinate of the upper left corner of the transformed rectangle.
     * @param float $size The size of the transformed rectangle.
     * @param integer $rotation Rotation specified as 0 = 0 rad, 1 = 0.5p rad, 2 = p rad, 3 = 1.5p rad.
     */
    public function __construct($x, $y, $size, $rotation)
    {
        $this->x = $x;
        $this->y = $y;
        $this->size = $size;
        $this->rotation = $rotation;
    }
    
    /**
     * Gets a noop transform.
     * @return \Jdenticon\Rendering\Transform
     */
    public static function getEmpty() 
    {
        return new Transform(0, 0, 0, 0);
    }

    /**
     * Transforms the specified point based on the translation and rotation specification for this Transform.
     * @param float $x x-coordinate
     * @param float $y y-coordinate
     * @param float $width The width of the transformed rectangle. If greater than 0, this will ensure the returned point is of the upper left corner of the transformed rectangle.
     * @param float $height The height of the transformed rectangle. If greater than 0, this will ensure the returned point is of the upper left corner of the transformed rectangle.
     * @return \Jdenticon\Rendering\Point
     */
    public function transformPoint($x, $y, $width = 0, $height = 0)
    {
        $right = $this->x + $this->size;
        $bottom = $this->y + $this->size;
        
        switch ($this->rotation) 
        {
            case 1: return new Point($right - $y - $height, $this->y + $x);
            case 2: return new Point($right - $x - $width, $bottom - $y - $height);
            case 3: return new Point($this->x + $y, $bottom - $x - $width);
            default: return new Point($this->x + $x, $this->y + $y);
        }
    }
}


class TriangleDirection
{
    /**
     * The 90 degree angle is pointing to South West.
     */
    const SOUTH_WEST = 0;
    /**
     * The 90 degree angle is pointing to North West.
     */
    const NORTH_WEST = 1;
    /**
     * The 90 degree angle is pointing to North East.
     */ 
    const NORTH_EAST = 2;
    /**
     * The 90 degree angle is pointing to South East.
     */
    const SOUTH_EAST = 3;
}




class Shape
{
    /// <summary>
    /// The shape definition to be used to render the shape.
    /// </summary>
    public $definition;

    /// <summary>
    /// The fill color of the shape.
    /// </summary>
    public $color;

    /// <summary>
    /// The positions in which the shape will be rendered.
    /// </summary>
    public $positions;

    /// <summary>
    /// The rotation index of the icon in the first position.
    /// </summary>
    public $startRotationIndex;
    
    public function __construct($definition, $color, array $positions, $startRotationIndex)
    {
        $this->definition = $definition;
        $this->color = $color;
        $this->positions = $positions;
        $this->startRotationIndex = $startRotationIndex;
    }
}

class ShapeCategory
{
    /// <summary>
    /// The index of the hash octet determining the color of shapes in this category.
    /// </summary>
    public $colorIndex;

    /// <summary>
    /// A list of possible shape definitions in this category.
    /// </summary>
    public $shapes;

    /// <summary>
    /// The index of the hash octet determining which of the shape definitions that will be used 
    /// for a particular hash.
    /// </summary>
    public $shapeIndex;

    /// <summary>
    /// The index of the hash octet determining the rotation index of the shape in the first position.
    /// </summary>
    public $rotationIndex;

    /// <summary>
    /// The positions in which the shapes of this category will be rendered.
    /// </summary>
    public $positions;
    
    public function __construct($colorIndex, array $shapes, $shapeIndex, $rotationIndex, array $positions)
    {
        $this->colorIndex = $colorIndex;
        $this->shapes = $shapes;
        $this->shapeIndex = $shapeIndex;
        $this->rotationIndex = $rotationIndex;
        $this->positions = $positions;
    }
}



/// <summary>
/// Specifies the color style of an identicon.
/// </summary>
class IdenticonStyle
{
    private $backgroundColor;
    private $padding;
    private $saturation;
    private $colorLightness;
    private $grayscaleLightness;
    
    public function __construct()
    {
        $this->backgroundColor = self::getDefaultBackgroundColor();
        $this->padding = self::getDefaultPadding();
        $this->saturation = self::getDefaultSaturation();
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
     * Gets the saturation of the colored identicon shapes.
     *
     * @return double  Saturation in the range [0.0, 1.0].
     */
    public function getSaturation()
    {
        return $this->saturation;
    }
    
    /**
     * Sets the saturation of the colored identicon shapes.
     *
     * @param $value double  Saturation in the range [0.0, 1.0].
     * @return \Jdenticon\IdenticonStyle
     */
    public function setSaturation($value)
    {
        $this->saturation = $value;
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
            $value[1] < 0 || $value[1] > 1) {
            // OOps
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
     * Gets the default value of the Padding property. Resolves to 0.5.
     *
     * @return
     */
    public static function getDefaultSaturation()
    {
        return 0.5;
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


class Identicon
{
    private $hash;
    private $size;
    private $iconGenerator;
    private $style;
    private $renderer;

    /// <summary>
    /// Creates an <see cref="Identicon"/> instance with the specified hash.
    /// </summary>
    /// <param name="hash">The hash that will be used as base for this icon. The hash must contain at least 6 bytes.</param>
    /// <param name="size">The size of the icon in pixels (the icon is quadratic).</param>
    /// <exception cref="ArgumentException"><paramref name="hash"/> does not contain 6 bytes.</exception>
    /// <exception cref="ArgumentNullException"><paramref name="hash"/> is null.</exception>
    /// <exception cref="ArgumentOutOfRangeException"><paramref name="size"/> is less than 1 pixel.</exception>
    public function __construct($hash, $size)
    {
        $this->size = $size;
        $this->hash = $hash;
        $this->iconGenerator = IconGenerator::getDefaultGenerator();
        $this->style = new IdenticonStyle();
    }
    
    public function setRenderer($renderer)
    {
        if ($renderer->width < $this->size ||
            $renderer->height < $this->size) 
        {
            throw new \InvalidArgumentException(
                "The specified renderer canvas is too small. Renderer canvas size: $renderer->width x $renderer->height, Icon size: $this->size.");
        }
        
        $this->renderer = $renderer;
    }
    
    /// <summary>
    /// Creates an <see cref="Identicon"/> instance from a specified hash.
    /// </summary>
    /// <param name="hash">The hash that will be used as base for this icon. The hash must contain at least 6 bytes.</param>
    /// <param name="size">The size of the icon in pixels (the icon is quadratic).</param>
    /// <exception cref="ArgumentException"><paramref name="hash"/> does not contain 6 bytes.</exception>
    /// <exception cref="ArgumentNullException"><paramref name="hash"/> is null.</exception>
    /// <exception cref="ArgumentOutOfRangeException"><paramref name="size"/> is less than 1 pixel.</exception>
    /// <returns>An <see cref="Identicon"/> instance for the specified hash.</returns>
    public static function fromHash($hash, $size)
    {
        return new Identicon($hash, $size);
    }

#pragma warning disable CS1573
    /// <inheritdoc cref="HashGenerator.ComputeHash(object, string)" />
    /// <summary>
    /// Generates a hash for a specified value and creates an <see cref="Identicon"/> instance from the generated hash.
    /// </summary>
    /// <param name="size">The size of the icon in pixels (the icon is quadratic).</param>
    /// <exception cref="ArgumentOutOfRangeException"><paramref name="size"/> is less than 1 pixel.</exception>
    /// <returns>An <see cref="Identicon"/> instance for the hash of <paramref name="value"/>.</returns>
    public static function fromValue($value, $size)
    {
        return new Identicon(sha1("$value"), $size);
    }
#pragma warning restore CS1573

    /// <summary>
    /// Gets or sets the size of the icon.
    /// </summary>
    /// <exception cref="ArgumentOutOfRangeException">The value is less than 1 pixel.</exception>
    public function getSize()
    {
        return $this->size;
    }
    
    public function setSize($size)
    {
        $this->size = $size;
    }
    
    /// <summary>
    /// Gets or sets the <see cref="Jdenticon.Rendering.IconGenerator"/> used to generate icons.
    /// </summary>
    public function getIconGenerator()
    {
        return $this->iconGenerator;
    }
    
    public function setIconGenerator($iconGenerator)
    {
        $this->iconGenerator = $iconGenerator;
        return $this;
    }
    
    /// <summary>
    /// Gets or sets the style of the icon.
    /// </summary>
    public function getStyle()
    {
        return $this->style;
    }
    
    public function setStyle($style)
    {
        $this->style = $style;
        return $this;
    }

    /// <summary>
    /// Draws this icon using a specified renderer.
    /// </summary>
    /// <param name="renderer">The renderer used to render this icon.</param>
    /// <param name="rect">The bounds of the rendered icon. No padding will be applied to the rectangle.</param>
    /// <remarks>
    /// This method is only intended for usage with custom renderers. A custom renderer could as an example 
    /// render an <see cref="Identicon"/> in a file format not natively supported by Jdenticon. To implement
    /// a new file format, implement the abstract <see cref="Renderer"/> class.
    /// </remarks>
    public function draw(
        \Jdenticon\Rendering\RendererInterface $renderer, 
        \Jdenticon\Rendering\Rectangle $rect)
    {
        $this->iconGenerator->generate($renderer, $rect, $this->style, $this->hash);
    }

    /// <summary>
    /// Gets the hash that is used as base for this icon.
    /// </summary>
    /// <remarks>
    /// <para>
    /// The <see cref="Hash"/> property always returns a copy of its internal byte array to avoid accidental 
    /// changes to the icon.
    /// </para>
    /// <para>
    /// This property exposes the internally stored compacted hash. If the hash that was used to construct the
    /// <see cref="Identicon"/> was longer than 10 byte, it has been shortened to 10 byte.
    /// </para>
    /// </remarks>
    public function getHash()
    {
        return $this->hash;
    }
    
    public function setHash($hash)
    {
        $this->hash = $hash;
        return $this;
    }

    /// <summary>
    /// Gets the bounds of the icon excluding its padding.
    /// </summary>
    public function getIconBounds() 
    {
        $padding = $this->style->getPadding();
        
        return new Rectangle(
            (int)($padding * $this->size),
            (int)($padding * $this->size),
            $this->size - (int)($padding * $this->size) * 2,
            $this->size - (int)($padding * $this->size) * 2);
    }
    
    
    private function getRenderer($format) 
    {
        switch (strtolower($format))
        {
            case 'svg':
                return new SvgRenderer($this->size, $this->size);
            default:
                return extension_loaded('imagick') ?
                    new ImagickRenderer($this->size, $this->size) :
                    new PngRenderer($this->size, $this->size);
        }
    }
    
    
    public function displayImage($format = "PNG")
    {
        $renderer = $this->getRenderer($format);
        $this->draw($renderer, $this->getIconBounds());
        $mimeType = $renderer->getMimeType();
        $data = $renderer->getData();
        header("Content-Type: $mimeType");
        echo $data;
    }
    
    public function getImageData($format = "PNG")
    {
        $renderer = $this->getRenderer($format);
        $this->draw($renderer, $this->getIconBounds());
        return $renderer->getData();
    }
    
    public function getImageDataUri($format = "PNG")
    {
        $renderer = $this->getRenderer($format);
        $this->draw($renderer, $this->getIconBounds());
        $mimeType = $renderer->getMimeType();
        $base64 = base64_encode($renderer->getData());
        return "data:$mimeType;base64,$base64";
    }
    
}


class ShapeDefinitions
{
    private static $outerShapes;
    private static $centerShapes;
    
    public static function getOuterShapes()
    {
        if (self::$outerShapes === null) {
            self::$outerShapes = self::createOuterShapes();
        }
        return self::$outerShapes;
    }
    
    public static function getCenterShapes()
    {
        if (self::$centerShapes === null) {
            self::$centerShapes = self::createCenterShapes();
        }
        return self::$centerShapes;
    }
    
    private static function createOuterShapes()
    {
        return array(
            function ($renderer, $cell, $index)
            {
                $renderer->addTriangle(0, 0, $cell, $cell, 0);
            },
            function ($renderer, $cell, $index)
            {
                $renderer->addTriangle(0, $cell / 2, $cell, $cell / 2, 0);
            },
            function ($renderer, $cell, $index)
            {
                $renderer->addRhombus(0, 0, $cell, $cell);
            },
            function ($renderer, $cell, $index)
            {
                $m = $cell / 6;
                $renderer->addCircle($m, $m, $cell - 2 * $m);
            }
        );
    }
    
    public static function createCenterShapes()
    {
        return array(
            function ($renderer, $cell, $index)
            {
                $k = $cell * 0.42;
                $renderer->addPolygon(array(
                    new Point(0, 0),
                    new Point($cell, 0),
                    new Point($cell, $cell - $k * 2),
                    new Point($cell - $k, $cell),
                    new Point(0, $cell)
                ));
            },
            function ($renderer, $cell, $index)
            {
                $w = (int)($cell * 0.5);
                $h = (int)($cell * 0.8);
                $renderer->addTriangle($cell - $w, 0, $w, $h, TriangleDirection::NORTH_EAST);
            },
            function ($renderer, $cell, $index)
            {
                $s = $cell / 3;
                $renderer->addRectangle($s, $s, $cell - $s, $cell - $s);
            },
            function ($renderer, $cell, $index)
            {
                $tmp = $cell * 0.1;

                if ($tmp > 1) {
                    // large icon => truncate decimals
                    $inner = (int)$tmp;
                }
                elseif ($tmp > 0.5) {
                    // medium size icon => fixed width
                    $inner = 1;
                }
                else {
                    // small icon => anti-aliased border
                    $inner = $tmp;
                }

                // Use fixed outer border widths in small icons to ensure the border is drawn
                if ($cell < 6) {
                    $outer = 1;
                }
                elseif ($cell < 8) {
                    $outer = 2;
                }
                else {
                    $outer = (int)($cell / 4);
                }

                $renderer->addRectangle($outer, $outer, $cell - $inner - $outer, $cell - $inner - $outer);
            },
            function ($renderer, $cell, $index)
            {
                $m = (int)($cell * 0.15);
                $s = (int)($cell * 0.5);
                $renderer->addCircle($cell - $s - $m, $cell - $s - $m, $s);
            },
            function ($renderer, $cell, $index)
            {
                $inner = $cell * 0.1;
                $outer = $inner * 4;

                $renderer->addRectangle(0, 0, $cell, $cell);
                $renderer->addPolygon(array(
                    new Point($outer, $outer),
                    new Point($cell - $inner, $outer),
                    new Point($outer + ($cell - $outer - $inner) / 2, $cell - $inner)
                ), true);
            },
            function ($renderer, $cell, $index)
            {
                $renderer->addPolygon(array(
                    new Point(0, 0),
                    new Point($cell, 0),
                    new Point($cell, $cell * 0.7),
                    new Point($cell * 0.4, $cell * 0.4),
                    new Point($cell * 0.7, $cell),
                    new Point(0, $cell)
                ));
            },
            function ($renderer, $cell, $index)
            {
                $renderer->addTriangle($cell / 2, $cell / 2, $cell / 2, $cell / 2, TriangleDirection::SOUTH_EAST);
            },
            function ($renderer, $cell, $index)
            {
                $renderer->addPolygon(array(
                    new Point(0, 0),
                    new Point($cell, 0),
                    new Point($cell, $cell / 2),
                    new Point($cell / 2, $cell),
                    new Point(0, $cell)
                ));
            },
            function ($renderer, $cell, $index)
            {
                $tmp = $cell * 0.14;
                
                if ($cell < 8) {
                     // small icon => anti-aliased border
                     $inner = $tmp;
                }
                else {
                     // large icon => truncate decimals
                     $inner = (int)$tmp;
                }
                
                // Use fixed outer border widths in small icons to ensure the border is drawn
                if ($cell < 4) {
                     $outer = 1;
                }
                elseif ($cell < 6) {
                     $outer = 2;
                }
                else {
                     $outer = (int)($cell * 0.35);
                }

                $renderer->addRectangle(0, 0, $cell, $cell);
                $renderer->addRectangle($outer, $outer, $cell - $outer - $inner, $cell - $outer - $inner, true);
            },
            function ($renderer, $cell, $index)
            {
                $inner = $cell * 0.12;
                $outer = $inner * 3;

                $renderer->addRectangle(0, 0, $cell, $cell);
                $renderer->addCircle($outer, $outer, $cell - $inner - $outer, true);
            },
            function ($renderer, $cell, $index)
            {
                $renderer->addTriangle($cell / 2, $cell / 2, $cell / 2, $cell / 2, TriangleDirection::SOUTH_EAST);
            },
            function ($renderer, $cell, $index)
            {
                $m = $cell * 0.25;

                $renderer->addRectangle(0, 0, $cell, $cell);
                $renderer->addRhombus($m, $m, $cell - $m, $cell - $m, true);
            },
            function ($renderer, $cell, $index)
            {
                $m = $cell * 0.4;
                $s = $cell * 1.2;

                if ($index != 0)
                {
                    $renderer->addCircle($m, $m, $s);
                }
            }
        );
    }
}


class ShapePosition
{
    public $x;
    public $y;

    /// <summary>
    /// Creates a new <see cref="ShapePosition"/> instance.
    /// </summary>
    /// <param name="x">The x-coordinate of the containing cell.</param>
    /// <param name="y">The y-coordinate of the containing cell.</param>
    public function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }
}



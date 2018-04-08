<?php
namespace Jdenticon;

use Jdenticon\IdenticonStyle;
use Jdenticon\Rendering\Rectangle;
use Jdenticon\Rendering\RendererInterface;
use Jdenticon\Rendering\IconGenerator;
use Jdenticon\Rendering\InternalPngRenderer;
use Jdenticon\Rendering\ImagickRenderer;
use Jdenticon\Rendering\SvgRenderer;

/**
 * Represents an identicon and its style. This is the entry class to Jdenticon.
 */
class Identicon
{
    /**
     * @var string
     */
    private $hash;
    
    /**
     * @var integer
     */
    private $size;
    
    /**
     * @var Jdenticon\Rendering\IconGenerator
     */
    private $iconGenerator;
    
    /**
     * @var Jdenticon\IdenticonStyle
     */
    private $style;

    /**
     * Creates an Identicon instance with the specified hash.
     *
     * @param string $hash A binary string containing the hash that will be used 
     *      as base for this icon. The hash must contain at least 6 bytes.
     * @param int|float|double $size The size of the icon in pixels (the icon 
     *      is quadratic).
     */
    public function __construct($hash, $size)
    {
        $this->setSize($size);
        $this->setHash($hash);
        
        $this->iconGenerator = IconGenerator::getDefaultGenerator();
        $this->style = new IdenticonStyle();
    }
    
    /**
     * Creates an Identicon instance from a specified hash.
     *
     * @param string $hash A binary string containing the hash that will be used 
     *      as base for this icon. The hash must contain at least 6 bytes.
     * @param int $size The size of the icon in pixels (the icon is quadratic).
     * @return \Jdenticon\Identicon
     */
    public static function fromHash($hash, $size)
    {
        return new Identicon($hash, $size);
    }

    /**
     * Creates an Identicon instance from a specified value.
     *
     * @param mixed $value The value that will be used as base for this icon. 
     *      The value will be converted to a UTF8 encoded string and then hashed 
     *      using SHA1.
     * @param int $size The size of the icon in pixels (the icon is quadratic).
     * @return \Jdenticon\Identicon
     */
    public static function fromValue($value, $size)
    {
        return new Identicon(sha1(utf8_encode("$value")), $size);
    }

    /** 
     * Gets the size of the icon in pixels.
     */
    public function getSize()
    {
        return $this->size;
    }
    
    /**
     * Sets the size of this icon in pixels.
     *
     * @param int|float|double $size The width and height of the icon.
     */
    public function setSize($size)
    {
        if (!is_numeric($size) || $size < 1) {
            throw new \InvalidArgumentException(
                "setSize got an invalid size. A numeric value greater than or " .
                "equal to 1 was expected. Actual value: $size.");
        }
        
        $this->size = (int)$size;
    }
    
    /**
     * Gets the {@see IconGenerator} used to generate icons.
     *
     * @return \Jdenticon\Rendering\IconGenerator
     */
    public function getIconGenerator()
    {
        return $this->iconGenerator;
    }
    
    /**
     * Sets the {@see IconGenerator} used to generate icons.
     *
     * @param \Jdenticon\Rendering\IconGenerator $iconGenerator Icon generator 
     *      that will render the shapes of the identicon.
     * @return \Jdenticon\Identicon
     */
    public function setIconGenerator(IconGenerator $iconGenerator)
    {
        if ($iconGenerator == null) {
            $iconGenerator = IconGenerator::getDefaultGenerator();
        }
        $this->iconGenerator = $iconGenerator;
        return $this;
    }
    
    /**
     * Gets or sets the style of the icon.
     *
     * @return \Jdenticon\IdenticonStyle
     */
    public function getStyle()
    {
        return $this->style;
    }
    
    /**
     * Gets or sets the style of the icon.
     * @param \Jdenticon\IdenticonStyle $style The new style of the icon. NULL 
     *      will revert the identicon to use the default style.
     * @return \Jdenticon\Identicon
     */
    public function setStyle(IdenticonStyle $style)
    {
        if ($style == null) {
            $style = new IdenticonStyle();
        }
        $this->style = $style;
        return $this;
    }

    /**
     * Draws this icon using a specified renderer.
     *
     * This method is only intended for usage with custom renderers. A custom 
     * renderer could as an example render an Identicon in a file format not 
     * natively supported by Jdenticon. To implement a new file format, 
     * implement {@see \Jdenticon\Rendering\RendererInterface}.
     *
     * @param \Jdenticon\Rendering\RendererInterface $renderer The renderer used 
     *      to render this icon.
     * @param \Jdenticon\Rendering\Rectangle $rect The bounds of the rendered 
     *      icon. No padding will be applied to the rectangle.
     */
    public function draw(
        \Jdenticon\Rendering\RendererInterface $renderer, 
        \Jdenticon\Rendering\Rectangle $rect)
    {
        $this->iconGenerator->generate($renderer, $rect, $this->style, $this->hash);
    }

    /**
     * Gets a binary string containing the hash that is used as base for this 
     * icon.
     */
    public function getHash()
    {
        return $this->hash;
    }
    
    /**
     * Sets a binary string containing the hash that is used as base for this 
     * icon. The string should contain at least 6 bytes.
     *
     * @param string $hash Binary string containing the hash.
     */
    public function setHash($hash)
    {
        if (!is_string($hash)) {
            throw new \InvalidArgumentException(
                "An invalid \$hash was passed to Identicon. " .
                "A binary string was expected.");
        }
        if (strlen($hash) < 6) {
            throw new \InvalidArgumentException(
                "An invalid \$hash was passed to Identicon. " . 
                "The hash was expected to contain at least 6 bytes.");
        }
        
        $this->hash = $hash;
        return $this;
    }

    /**
     * Gets the bounds of the icon excluding its padding.
     *
     * @return \Jdenticon\Rendering\Rectangle
     */
    public function getIconBounds() 
    {
        $padding = $this->style->getPadding();
        
        return new Rectangle(
            (int)($padding * $this->size),
            (int)($padding * $this->size),
            $this->size - (int)($padding * $this->size) * 2,
            $this->size - (int)($padding * $this->size) * 2);
    }
    
    private function getRenderer($imageFormat) 
    {
        switch (strtolower($imageFormat))
        {
            case 'svg':
                return new SvgRenderer($this->size, $this->size);
            default:
                return extension_loaded('imagick') ?
                    new ImagickRenderer($this->size, $this->size) :
                    new InternalPngRenderer($this->size, $this->size);
        }
    }
    
    /**
     * Renders the icon directly to the page output.
     *
     * The method will set the 'Content-Type' HTTP header. You are recommended 
     * to set an appropriate 'Cache-Control' header before calling this method 
     * to ensure the icon is  cached client side.
     *
     * @param string $imageFormat The image format of the output. 
     *      Supported values are 'png' and 'svg'.
     */
    public function displayImage($imageFormat = 'png')
    {
        $renderer = $this->getRenderer($imageFormat);
        $this->draw($renderer, $this->getIconBounds());
        $mimeType = $renderer->getMimeType();
        $data = $renderer->getData();
        header("Content-Type: $mimeType");
        echo $data;
    }
    
    /**
     * Renders the icon to a binary string.
     *
     * @param string $imageFormat The image format of the output string. 
     *      Supported values are 'png' and 'svg'.
     * @return string
     */
    public function getImageData($imageFormat = 'png')
    {
        $renderer = $this->getRenderer($imageFormat);
        $this->draw($renderer, $this->getIconBounds());
        return $renderer->getData();
    }
    
    /**
     * Renders the icon as a data URI. It is recommended to avoid using this 
     * method unless really necessary, since it will effectively disable client 
     * caching of generated icons, and will also cause the same icon to be 
     * rendered multiple times, when used multiple times on a single page.
     *
     * @param string $imageFormat The image format of the data URI. 
     *      Supported values are 'png' and 'svg'.
     * @return string
     */
    public function getImageDataUri($imageFormat = 'png')
    {
        $renderer = $this->getRenderer($imageFormat);
        $this->draw($renderer, $this->getIconBounds());
        $mimeType = $renderer->getMimeType();
        $base64 = base64_encode($renderer->getData());
        return "data:$mimeType;base64,$base64";
    }
}



<?php
//phpinfo(); exit;

// Imagemagick-problem
// https://stackoverflow.com/questions/39609951/cannot-load-imagick-library

include_once("vendor/autoload.php");

use Jdenticon\Identicon;
use Jdenticon\IdenticonStyle;
use Jdenticon\Color;

$icon = new Identicon(array(
    'value' => $_GET['icon'],
    'size' => 100,
    'style' => array(
        'backgroundColor' => 'rgb(155, 214, 44, 1)',
        'colorSaturation' => 1.0,
        'padding' => 0,
        'grayscaleSaturation' => 1.0,
        'colorLightness' => array(0.4, 0.8),
        'grayscaleLightness' => array(0.3, 0.9)
    )));
$icon->displayImage();

//var_dump($icon->getOptions());
    
    exit;

$style = new IdenticonStyle();
$style
    ->setBackgroundColor(Color::fromArgb(255, 255, 255, 255))
    ->setColorSaturation(1.0)
    ->setGrayscaleSaturation(1.0)
    ->setColorLightness(array(0.4, 0.8))
    ->setGrayscaleLightness(array(0.3, 0.9));
    
$style = new IdenticonStyle(array(
    'backgroundColor' => 'rgb(155, 214, 44)',
    'colorSaturation' => 1.0,
    'grayscaleSaturation' => 1.0,
    'colorLightness' => array(0.4, 0.8),
    'grayscaleLightness' => array(0.3, 0.9)
));

$icon = Identicon::fromValue($_GET['icon'], 100);
$icon->setStyle($style);
$icon->displayImage();




Identicon::fromValue($_GET['icon'], 100)
    ->setStyle(array(
        'backgroundColor' => 'rgb(155, 214, 44)',
        'padding' => 0,
        'colorSaturation' => 1.0,
        'grayscaleSaturation' => 1.0,
        'colorLightness' => array(0.4, 0.8),
        'grayscaleLightness' => array(0.3, 0.9)
    ))
    ->displayImage();
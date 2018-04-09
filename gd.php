<?php
//phpinfo(); exit;

// Imagemagick-problem
// https://stackoverflow.com/questions/39609951/cannot-load-imagick-library

include_once("vendor/autoload.php");



use Jdenticon\Identicon;
use Jdenticon\Rendering\GdRenderer2;

$icon = Identicon::fromValue("Hej hej", 100);
$icon->setRenderer(new GdRenderer2(100,100));
$icon->displayImage();



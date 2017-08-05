<?php
//phpinfo(); exit;

// Imagemagick-problem
// https://stackoverflow.com/questions/39609951/cannot-load-imagick-library

include_once("vendor/autoload.php");



use Jdenticon\Identicon;

Identicon::fromValue($_GET['icon'], 100)->displayImage();



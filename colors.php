<?php
//phpinfo(); exit;

// Imagemagick-problem
// https://stackoverflow.com/questions/39609951/cannot-load-imagick-library

include_once("vendor/autoload.php");

use Jdenticon\Color;

echo '<pre>';
echo Color::parse(Color::fromRgb(1,2,3)) . "<br>";
echo Color::parse('#fff') . "<br>";
echo Color::parse('#abc') . "<br>";
echo Color::parse('#abcd') . "<br>";
echo Color::parse('#abcdef') . "<br>";
echo Color::parse('#123456ab') . "<br>";
echo Color::parse('rEd') . "<br>";
echo Color::parse('TRansparent') . "<br>";
echo Color::parse('rgb(214, 12, 33)') . "<br>";
echo Color::parse('hsl(321, 15%, 77%, 1)') . "<br>";
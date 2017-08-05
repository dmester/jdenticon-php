<?php
//phpinfo(); exit;

// Imagemagick-problem
// https://stackoverflow.com/questions/39609951/cannot-load-imagick-library

include_once("vendor/autoload.php");

use Jdenticon\Identicon;

for ($i = 0; $i < 1000; $i++)
{
    echo "<img src=\"icon.php?icon=icon$i\">";
}
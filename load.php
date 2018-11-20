<?php

$twcal = __DIR__.'/vendor/autoload.php';

if (file_exists($twcal) && is_readable($twcal)) {
    include_once "$twcal";
} else {
    throw new Exception('Composer has not been installed, or autoloader is not present.');
}


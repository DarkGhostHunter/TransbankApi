<?php

$autoloader = __DIR__.'/vendor/autoload.php';

if (file_exists($autoloader) && is_readable($autoloader)) {
    include_once "$autoloader";
} else {
    throw new Exception('Composer has not been installed, or autoloader is not present.');
}
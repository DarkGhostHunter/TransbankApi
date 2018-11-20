<?php

include_once __DIR__.'/../load.php';

$classLoader = new \Composer\Autoload\ClassLoader();
$classLoader->addPsr4("Tests\\", __DIR__, true);
$classLoader->register();
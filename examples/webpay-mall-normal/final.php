<?php

include_once '../../vendor/autoload.php';

$webpay = \DarkGhostHunter\TransbankApi\Transbank::make()->webpay();

echo '<pre>';
print_r($_POST);
echo '</pre>';
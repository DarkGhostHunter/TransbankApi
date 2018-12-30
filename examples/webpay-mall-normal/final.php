<?php

include_once '../../vendor/autoload.php';

$webpay = \DarkGhostHunter\TransbankApi\Transbank::environment()->webpay();

echo '<pre>';
print_r($_POST);
echo '</pre>';
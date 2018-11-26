<?php

include_once '../../vendor/autoload.php';

$webpay = \Transbank\Wrapper\TransbankConfig::environment()->webpay();

echo '<pre>';
print_r($_POST);
echo '</pre>';
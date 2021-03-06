<?php

include_once '../../vendor/autoload.php';

$webpay = DarkGhostHunter\TransbankApi\Transbank::make()->webpay();

$result = $webpay->createNullify([
    'authorizationCode' => $_POST['authorizationCode'],
    'buyOrder' => $_POST['buyOrder'],
    'authorizedAmount' => $_POST['authorizedAmount'],
    'nullifyAmount' => $_POST['nullifyAmount'],
]);

// Veamos el resultado.
echo '<pre>';
print_r($result);
echo '</pre>';

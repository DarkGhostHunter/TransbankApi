<?php

include_once '../../vendor/autoload.php';

$webpay = \DarkGhostHunter\TransbankApi\Transbank::environment()->webpay();

$result = $webpay->createUnregistration([
    'tbkUser' => $_POST['tbkUser'],
    'username' => $_POST['username']
]);

echo '<pre>';
print_r($result);
echo '</pre>';

?>
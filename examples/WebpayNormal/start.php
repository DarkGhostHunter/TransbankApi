<?php

include_once '../../vendor/autoload.php';

$webpay = DarkGhostHunter\TransbankApi\Transbank::environment()->webpay();

$result = $webpay->createNormal([
    'returnUrl' => 'http://localhost:8080/WebpayNormal/return.php',
    'finalUrl' => 'http://localhost:8080/WebpayNormal/final.php',
    'buyOrder'  => 'myOrde-16548',
    'amount'    => 1000,
]);

echo '<pre>';
print_r($result);
echo '</pre>';

?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Conectando con Webpay...</title>
</head>
<body>
<form id="redirect" action="<?php echo $result->url ?>" method="POST">
    <input type="hidden" name="<?php echo $result->getTokenName() ?>" value="<?php echo htmlspecialchars($result->token, ENT_HTML5) ?>">
    <button type="submit">Pagar</button>
</form>
</body>
</html>

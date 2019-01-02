<?php

include_once '../../vendor/autoload.php';

$webpay = DarkGhostHunter\TransbankApi\Transbank::make()->webpay();

$result = $webpay->createDefer([
    'returnUrl' => 'http://localhost:8080/webpay-defer-capture-nullify/return.php',
    'finalUrl' => 'http://localhost:8080/webpay-defer-capture-nullify/final.php',
    'buyOrder'  => date('Y-m-d_H-i-s'),
    'amount'    => 9990,
]);

// Veamos el resultado.
echo '<pre>';
print_r($result);
echo '</pre>';

// HTML para redirigir la prueba
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

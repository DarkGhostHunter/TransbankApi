<?php

include_once '../../vendor/autoload.php';

$transbank = DarkGhostHunter\TransbankApi\Transbank::environment();

$transbank->setDefaults('onepay', [
    'channel'               => 'web',
    'generateOttQrCode'     => true,
    'callbackUrl'           => 'http://localhost:8080//Onepay/confirm.php',
    'appScheme'             => 'my-app://onepay/result',
]);

$onepay = $transbank->onepay();

$result = $onepay->createCart([
    'items' => [
        'description' => 'Producto de Prueba',
        'quantity' => 1,
        'amount' => 9990,
    ]
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

<a href="https://onepay.ionix.cl/mobile-payment-emulator/" target="_blank">
    1) Procesar compra de prueba en dashboard
</a>
<ul>
    <li><strong>E-mail:</strong> test@onepay.cl</li>
    <li><strong>Código de compra:</strong> <?php echo $result->ott ?></li>
</ul>
<hr>
    2) Confirmar compra internamente:
<form id="redirect" action="confirm.php" method="POST">
    <input type="hidden" name="occ" value="<?php echo $result->occ ?>">
    <input type="hidden" name="externalUniqueNumber" value="<?php echo $result->externalUniqueNumber ?>">
    <button type="submit">Confirmar</button>
</form>
</body>
</html>

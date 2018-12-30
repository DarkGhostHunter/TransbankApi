<?php

include_once '../load.php';

$transbank = DarkGhostHunter\TransbankApi\Transbank::environment();

$transbank->setDefaults('onepay', [
    'channel'               => 'web',
    'generateOttQrCode'     => true,
    'callbackUrl'           => currentUrlPath('confirm.php'),
]);

$onepay = $transbank->onepay();

$cart = $onepay->makeCart([
    'items' => [
        'description' => 'Producto de Prueba',
        'quantity' => 2,
        'amount' => 9990,
    ]
]);

$result = $cart->commit();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Conectando con Onepay...</title>
    <?php include __DIR__ . '/../_master/header.php' ?>
</head>
<body>
<div class="container">
    <h2>Cesta enviada:</h2>
    <pre><?php print_r($cart->toArray()) ?></pre>
    <h2>Respuesta:</h2>
    <pre><?php print_r($result->toArray()) ?></pre>
    <hr>
    <a href="https://onepay.ionix.cl/mobile-payment-emulator/" target="_blank">
        <h3>1) Procesar compra de prueba en dashboard</h3>
    </a>
    <ul>
        <li><strong>E-mail:</strong> test@onepay.cl</li>
        <li><strong>Código de compra:</strong> <?php echo $result->ott ?></li>
    </ul>
    <hr>
    <h3>2) Confirmar compra internamente:</h3>
    <form id="redirect" action="confirm.php" method="POST">
        <input type="hidden" name="occ" value="<?php echo $result->occ ?>">
        <input type="hidden" name="externalUniqueNumber" value="<?php echo $result->externalUniqueNumber ?>">
        <div class="text-center">
            <button type="submit" class="btn btn-primary btn-lg">Confirmar</button>
        </div>
    </form>
</div>
</body>
</html>

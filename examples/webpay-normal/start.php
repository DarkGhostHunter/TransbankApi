<?php

include_once '../load.php';

$webpay = DarkGhostHunter\TransbankApi\Transbank::environment()->webpay();

$payment = $webpay->makeNormal([
    'returnUrl' => currentUrlPath('return.php'),
    'finalUrl' => currentUrlPath('final.php'),
    'buyOrder'  => 'myOrder-' . rand(1,9999),
    'amount'    => rand(1000, 29999),
]);

$result = $payment->commit();

?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Conectando con Webpay...</title>
    <?php include __DIR__ . '/../_master/header.php' ?>
</head>
<body>
<div class="container">
    <div class="card card-body mb-5">
        <h4>Información de pago enviado</h4>
        <pre><?php print_r($payment->toArray()); ?></pre>
        <h4>Resultado devuelto</h4>
        <pre><?php print_r($result->toArray()); ?></pre>
    </div>
    <form id="redirect" action="<?php echo $result->url ?>" method="POST">
        <input type="hidden" name="<?php echo $result->getTokenName() ?>" value="<?php echo htmlspecialchars($result->token, ENT_HTML5) ?>">
        <div class="text-center">
            <button type="submit" class="btn btn-lg btn-primary mb-3">
                Ir a Webpay <i class="fas fa-arrow-right"></i>
            </button>
            <div class="small text-black-50">
                Esto hará una petición HTTP POST hacia Transbank.
            </div>
        </div>
    </form>
</body>
</html>

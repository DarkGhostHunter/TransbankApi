<?php

include_once '../../vendor/autoload.php';

$webpay = DarkGhostHunter\TransbankApi\Transbank::environment()->webpay();

$result = $webpay->createMallNormal([
    'returnUrl' => 'http://localhost:8080/webpay-mall-normal/return.php',
    'finalUrl' => 'http://localhost:8080/webpay-mall-normal/final.php',
    'sessionId' => 'alpha-session-1',
    'buyOrder' => 10000001,
    'items' => [
        [
            'commerceCode' => 597044444402,
            'amount' => 4990,
            'buyOrder' => 20000001,
        ],
        [
            'commerceCode' => 597044444403,
            'amount' => 9990,
            'buyOrder' => 30000001,
        ],
    ]
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

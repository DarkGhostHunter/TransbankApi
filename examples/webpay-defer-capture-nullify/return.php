<?php

include_once '../../vendor/autoload.php';

$webpay = \DarkGhostHunter\TransbankApi\Transbank::environment()->webpay();

$result = $webpay->retrieveNormal($_POST['token_ws']);

$confirm = $webpay->confirmNormal($_POST['token_ws']);

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
<form action="capture.php" method="POST">
    <input type="hidden" name="authorizationCode"
           value="<?php echo $result->detailOutput->authorizationCode ?>">
    <input type="hidden" name="buyOrder"
           value="<?php echo $result->detailOutput->buyOrder ?>">
    <input type="hidden" name="captureAmount"
           value="<?php echo $result->detailOutput->amount ?>">
    <button type="submit">Capturar</button>
    <p><small>(Nos vamos a saltar el "finalURL")</small></p>
</form>
</body>
</html>

<?php

include_once '../../vendor/autoload.php';

$webpay = DarkGhostHunter\TransbankApi\Transbank::make()->webpay();

$result = $webpay->createCapture([
    'authorizationCode' => $_POST['authorizationCode'],
    'buyOrder' => $_POST['buyOrder'],
    'captureAmount' => $_POST['captureAmount'],
]);

// Veamos el resultado.
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
<form action="nullify.php" method="POST">
    <input type="hidden" name="authorizationCode"
           value="<?php echo $result->authorizationCode ?>">
    <input type="hidden" name="buyOrder"
           value="<?php echo $_POST['buyOrder'] ?>">
    <input type="hidden" name="authorizedAmount"
           value="<?php echo $result->capturedAmount ?>">
    <input type="hidden" name="nullifyAmount"
           value="<?php echo $result->capturedAmount ?>">
    <button type="submit">Anular</button>
</form>
</body>
</html>

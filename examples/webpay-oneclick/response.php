<?php

include_once '../../vendor/autoload.php';

$webpay = \DarkGhostHunter\TransbankApi\Transbank::environment()->webpay();

$result = $webpay->getRegistration($_POST['TBK_TOKEN']);

$username = file_get_contents('username.txt');

echo '<pre>';
print_r($username);
print_r($result->toArray());
echo '</pre>';


?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Conectando con Webpay...</title>
</head>
<body>
<form action="charge.php" method="POST">
    <input type="hidden" name="tbkUser"
           value="<?php echo $result->tbkUser ?>">
    <input type="hidden" name="username"
           value="<?php echo $username ?>">
    <button type="submit">Cargar monto a la tarjeta</button>
    <p><small>(Nos vamos a saltar el "finalURL")</small></p>
</form>
</body>
</html>

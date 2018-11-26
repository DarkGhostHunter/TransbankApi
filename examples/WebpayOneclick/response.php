<?php

include_once '../../vendor/autoload.php';

$webpay = \Transbank\Wrapper\TransbankConfig::environment()->webpay();

$result = $webpay->getRegistration($_POST['TBK_TOKEN']);

echo '<pre>';
print_r($result);
echo '</pre>';

$username = file_get_contents('username.txt');

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

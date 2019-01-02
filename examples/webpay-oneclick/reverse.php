<?php

include_once '../../vendor/autoload.php';

$webpay = \DarkGhostHunter\TransbankApi\Transbank::make()->webpay();

$result = $webpay->createReverseCharge([
    'buyOrder' => $_POST['buyOrder']
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
<form action="unregister.php" method="POST">
    <input type="hidden" name="tbkUser"
           value="<?php echo $_POST['tbkUser'] ?>">
    <input type="hidden" name="username"
           value="<?php echo $_POST['username'] ?>">
    <button type="submit">Eliminar subscripción</button>
</form>
</body>
</html>

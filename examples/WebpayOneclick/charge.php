<?php

include_once '../../vendor/autoload.php';

$webpay = \DarkGhostHunter\TransbankApi\Transbank::environment()->webpay();

$result = $webpay->createCharge([
    'amount' => 9990,
    'buyOrder' => $buyOrder = date('YmdHis') . '000',
    'tbkUser' => $_POST['tbkUser'],
    'username' => $_POST['username'],
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
<form action="reverse.php" method="POST">
    <input type="hidden" name="buyOrder"
           value="<?php echo $buyOrder ?>">
    <input type="hidden" name="tbkUser"
           value="<?php echo $_POST['tbkUser'] ?>">
    <input type="hidden" name="username"
           value="<?php echo $_POST['username'] ?>">
    <button type="submit">Revertir Cargo</button>
</form>
</body>
</html>

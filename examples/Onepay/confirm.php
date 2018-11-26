<?php

include_once '../../vendor/autoload.php';

$onepay = DarkGhostHunter\TransbankApi\Transbank::environment()->onepay();

$result = $onepay->get([
    'occ'                   => $_POST['occ'],
    'externalUniqueNumber'  => $_POST['externalUniqueNumber'],
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
<form id="redirect" action="nullify.php" method="POST">
    <input type="hidden" name="occ" value="<?php echo $result->occ ?>">
    <input type="hidden" name="externalUniqueNumber" value="<?php echo $result->externalUniqueNumber ?>">
    <input type="hidden" name="authorizationCode" value="<?php echo $result->authorizationCode ?>">
    <input type="hidden" name="amount" value="<?php echo $result->amount ?>">
    <button type="submit">Anular</button>
</form>
</body>
</html>

<?php

include_once '../load.php';

$onepay = DarkGhostHunter\TransbankApi\Transbank::environment()->onepay();

$nullify = $onepay->makeNullify([
    'occ'                   => $_POST['occ'],
    'externalUniqueNumber'  => $_POST['externalUniqueNumber'],
    'authorizationCode'     => $_POST['authorizationCode'],
    'nullifyAmount'         => $_POST['amount'],
]);

$result = $nullify->commit();
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
    <h2>Transacción eliminada:</h2>
    <pre><?php print_r($result->toArray()) ?></pre>
    <hr>
    <div class="text-left">
        <a href="<?php echo currentUrlPath('../index.php') ?>" target="_self" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al índice
        </a>
    </div>
</div>
</body>
</html>

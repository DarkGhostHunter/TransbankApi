<?php

include_once '../../vendor/autoload.php';

$onepay = DarkGhostHunter\TransbankApi\Transbank::environment()->onepay();

$result = $onepay->getTransaction([
    'occ'                   => $_POST['occ'],
    'externalUniqueNumber'  => $_POST['externalUniqueNumber'],
]);
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
    <h2>Respuesta:</h2>
    <pre><?php print_r($_POST) ?></pre>
    <h2>Transacción recuperada:</h2>
    <pre><?php print_r($result->toArray()) ?></pre>
    <hr>
    <form id="redirect" action="nullify.php" method="POST">
        <input type="hidden" name="occ" value="<?php echo $result->occ ?>">
        <input type="hidden" name="externalUniqueNumber" value="<?php echo $_POST['externalUniqueNumber'] ?>">
        <input type="hidden" name="authorizationCode" value="<?php echo $result->authorizationCode ?>">
        <input type="hidden" name="amount" value="<?php echo $result->amount ?>">
        <div class="text-center">
            <button type="submit" class="btn btn-danger btn-lg">
                <i class="fa fa-times"></i> Anular transacción
            </button>
        </div>
    </form>
</div>
</body>
</html>

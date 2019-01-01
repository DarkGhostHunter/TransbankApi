<?php

include_once '../load.php';

$webpay = \DarkGhostHunter\TransbankApi\Transbank::environment()->webpay();

$result = $webpay->retrieveNormal($_POST['token_ws']);

$confirm = $webpay->confirmNormal($_POST['token_ws']);
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultado Webpay</title>
    <?php include __DIR__ . '/../_master/header.php' ?>
</head>
<body>
<div class="container">
    <div class="card card-body mb-5">
        <h4>Resultado de la transacción</h4>
        <pre><?php print_r($result->toArray()); ?></pre>
        <div class="alert alert-info small">
            <i class="fas fa-info-circle"></i> La transacción fue confirmada por separado.
        </div>
    </div>

    <form id="redirect" action="<?php echo $result->urlRedirection ?>" method="POST">
        <input type="hidden" name="<?php echo $result->getTokenName() ?>"
               value="<?php echo htmlspecialchars($_POST['token_ws'], ENT_HTML5) ?>">
        <div class="text-center">
            <button type="submit" class="btn btn-primary btn-large">Retornar a Webpay para el detalle</button>
        </div>
    </form>
</div>
</body>
</html>

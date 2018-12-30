<?php

include_once '../load.php';

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
        <h4>Recepción desde Webpay</h4>
        <pre><?php print_r($_POST); ?></pre>
        <div class="alert alert-info small">
            <i class="fas fa-info-circle"></i> La transacción terminó. No podemos volver a usar el token.
        </div>
    </div>

    <div class="text-left">
        <a href="<?php echo currentUrlPath('../index.php') ?>" target="_self" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al índice
        </a>
    </div>
</div>
</body>
</html>

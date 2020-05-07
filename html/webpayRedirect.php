<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Conectando con Webpay...</title>
</head>
<body>
    <form id="redirect" action="<?php echo $result->url ?>" method="POST">
        <input type="hidden" name="<?php echo $result->getTokenName() ?>" value="<?php echo htmlspecialchars($result->token, ENT_HTML5) ?>">
    </form>
    <script>
        // Redirige al usuario inmediatamente a Webpay
        document.getElementById('redirect').submit()
    </script>
</body>
</html>
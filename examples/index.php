<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ejemplos de integración de Servicios Transbank</title>
</head>
<body>

<?php

$json = '{
  "id": {
    "commerceId": "customId"
  },
  "transaction": [
    {
      "type": "webpay.plus.normal",
      "commerceOrderId": "order#322",
      "data": {
        "amount": 9990,
      },
      "meta": {
        "urlReturn": "http://localhost:8080/WebpayNormal/return.php",
        "urlFinal": "http://localhost:8080/WebpayNormal/final.php",
        "description": "This is my custom description, generated from my App."
      }
    }
  ]
}';

$secret = bin2hex(random_bytes(16));

echo $hash = base64_encode(hash_hmac('sha256', hash('sha256', $json), $secret));

?>


<h1>Ejemplos de integración de Servicios Transbank</h1>
<ul>
    <li>
        <a href="WebpayNormal/start.php" target="_self">Webpay Plus Normal</a>
    </li>
    <li>
        <a href="WebpayMallNormal/start.php" target="_self">Webpay Plus Mall Normal</a>
    </li>
    <li>
        <a href="WebpayDeferCaptureNullify/start.php" target="_self">Webpay Plus Diferida, Captura y Anulación</a>
    </li>
    <li>
        <a href="WebpayOneclick/start.php" target="_self">Webpay Oneclick Registro, Cargo, Revertir y Dar de baja (<em>desregistrar</em>).</a>
    </li>
    <li>
        <a href="Onepay/start.php" target="_self">Oneclick Web/Mobile</a>
    </li>
</ul>
</body>
</html>
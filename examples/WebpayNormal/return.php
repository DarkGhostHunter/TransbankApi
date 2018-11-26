<?php

include_once '../../vendor/autoload.php';

echo $_POST['token_ws'];

$webpay = \Transbank\Wrapper\TransbankConfig::environment()->webpay();

$result = $webpay->retrieveNormal($_POST['token_ws']);

$confirm = $webpay->confirmNormal($_POST['token_ws']);

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
<form id="redirect" action="<?php echo $result->urlRedirection ?>" method="POST">
    <input type="hidden" name="<?php echo $result->getTokenName() ?>"
           value="<?php echo htmlspecialchars($_POST['token_ws'], ENT_HTML5) ?>">
    <button type="submit">Retornar a Webpay</button>
</form>
</body>
</html>

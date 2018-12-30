<?php

include_once '../../vendor/autoload.php';

$webpay = \DarkGhostHunter\TransbankApi\Transbank::environment()->webpay();

$result = $webpay->retrieveMallNormal($_POST['token_ws']);

$confirm = $webpay->confirmMallNormal($_POST['token_ws']);

echo '<pre>';
print_r($result);
print_r($confirm);
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

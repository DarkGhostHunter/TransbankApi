# Versión 4.0 Final

Como leen, esta será la última versión de Transbank API. El nuevo SDK oficial de Transbank usa HTTPS en vez de SOAP (puaj!), así que este SDK ya no es necesario.

# Ve y usa el [SDK Oficial de Transbank](https://github.com/TransbankDevelopers/transbank-sdk-php)

![rawpixel - Unsplash (UL) #SEDqvdbkDQw](https://images.unsplash.com/photo-1535603383947-c1ee27a4906f?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1280&h=400&q=80)

[![Latest Stable Version](https://poser.pugx.org/darkghosthunter/transbank-api/v/stable)](https://packagist.org/packages/darkghosthunter/transbank-api) [![License](https://poser.pugx.org/darkghosthunter/transbank-api/license)](https://packagist.org/packages/darkghosthunter/transbank-api)
![](https://img.shields.io/packagist/php-v/darkghosthunter/transbank-api.svg) [![PHP Composer](https://github.com/DarkGhostHunter/TransbankApi/workflows/PHP%20Composer/badge.svg)](https://github.com/DarkGhostHunter/TransbankApi/actions) [![Coverage Status](https://coveralls.io/repos/github/DarkGhostHunter/TransbankApi/badge.svg?branch=master)](https://coveralls.io/github/DarkGhostHunter/TransbankApi?branch=master) [![Maintainability](https://api.codeclimate.com/v1/badges/4a6d823102cea362adfd/maintainability)](https://codeclimate.com/github/DarkGhostHunter/TransbankApi/maintainability)


# Transbank API

`TransbankApi` es un reemplazo al [Transbank SDK](https://github.com/TransbankDevelopers/transbank-sdk-php) con la finalidad de mejorar la experiencia de integración y uso.

> Esta versión es incompatible con PHP 5. Para usar este código con PHP 5, usa el packete oficial de [Transbank SDK](https://github.com/TransbankDevelopers/transbank-sdk-php).

## Requisitos:

- PHP 7.4, o PHP 8.0
- Composer

## Dependencias

Este paquete usa [Guzzle HTTP 7.0](http://docs.guzzlephp.org/en/stable/) y la [implementación de SOAP de Luis Urrutia](https://github.com/LuisUrrutia/TransbankSoap).  

A su vez, este paquete necesita las siguientes extensiones de PHP habilitadas:

* ext-curl
* ext-json
* ext-mbstring
* ext-soap
* ext-dom

Instalarlas dependerá de tu sistema: en algunos casos sólo necesitarás habilitarlas en tu `php.ini`; en otros, descargarlas usando tu gestor de packetes (como `apt-get` o `apk`) o compilarlas manualmente.

### Logger

Esta librería es compatible con cualquier [logger PSR-3](https://www.php-fig.org/psr/psr-3/). Si quieres que tu proyecto escriba información sobre las transacciones, puedes usar [Monolog](https://github.com/Seldaek/monolog/) o cualquier otro que siga el estándar.

# Instalación

Hay tres formas para instalar el paquete: usando Composer, sin composer, y todo de forma (muy) manual.

### Instalar con Composer

Para usar el SDK en tu proyecto usa Composer:

```bash
composer require darkghosthunter/transbank-api
```

## Documentación 

La documentación de este paquete está [en la Wiki](https://github.com/DarkGhostHunter/TransbankApi/wiki).

Sin embargo, la idea de este paquete es que puedas realizar la mayoría de tus transacciones usando sintaxis expresiva:

La información sobre las variables que necesitas para realizar cada transacción está en [Transbank Developers](https://www.transbankdevelopers.cl). Este paquete no modifica el nombre de las variables.

## Ejemplos

Este paquete incluye una pequeña sección de ejemplos que te permitirán probar (y ver en acción) cómo funciona la interacción con los distintos servicios Transbank en modo `integration`.

Sólo dirígete al [directorio `examples`](examples) y sigue las instrucciones.

## Información para contribuir y desarrollar este Wrapper

Tirar la talla en buen chileno en los PR. Si usas otro idioma, serás víctima de bullying.

# Licencia

Este paquete está licenciado bajo la [Licencia MIT](LICENCIA) [(En inglés)](LICENSE).

`Redcompra`, `Webpay`, `Onepay`, `Patpass` y `tbk` son marcas registradas de [Transbank S.A.](https://www.transbank.cl/)

Este paquete no está aprobado, apoyado ni avalado por Transbank S.A. ni ninguna persona natural o jurídica vinculada directa o indirectamente a Transbank S.A.

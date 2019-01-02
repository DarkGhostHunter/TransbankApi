![rawpixel - Unsplash (UL) #SEDqvdbkDQw](https://images.unsplash.com/photo-1535603383947-c1ee27a4906f?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1280&h=400&q=80)

[![Latest Stable Version](https://poser.pugx.org/darkghosthunter/transbank-api/v/stable)](https://packagist.org/packages/darkghosthunter/transbank-api) [![License](https://poser.pugx.org/darkghosthunter/transbank-api/license)](https://packagist.org/packages/darkghosthunter/transbank-api)
![](https://img.shields.io/packagist/php-v/darkghosthunter/transbank-api.svg) [![Build Status](https://travis-ci.com/DarkGhostHunter/TransbankApi.svg?branch=master)](https://travis-ci.com/DarkGhostHunter/TransbankApi) [![Coverage Status](https://coveralls.io/repos/github/DarkGhostHunter/TransbankApi/badge.svg?branch=master)](https://coveralls.io/github/DarkGhostHunter/TransbankApi?branch=master) [![Maintainability](https://api.codeclimate.com/v1/badges/c6c87a84fa8ecba894da/maintainability)](https://codeclimate.com/github/DarkGhostHunter/TransbankApi/maintainability) [![Test Coverage](https://api.codeclimate.com/v1/badges/c6c87a84fa8ecba894da/test_coverage)](https://codeclimate.com/github/DarkGhostHunter/TransbankApi/test_coverage)


# Transbank API

`TransbankApi` es un reemplazo al [Transbank SDK](https://github.com/TransbankDevelopers/transbank-sdk-php) con la finalidad de mejorar la experiencia de integración y uso.

> Esta versión es incompatible con PHP 5. Para usar este código con PHP 5, usa el packete oficial de [Transbank SDK](https://github.com/TransbankDevelopers/transbank-sdk-php).

## Requisitos:

- PHP 7.1.3 o mayor
- Composer

## Dependencias

Este paquete usa [Guzzle HTTP 6.0](http://docs.guzzlephp.org/en/stable/), [KLogger](http://codefury.net/projects/klogger/), y la [implementación de SOAP de Luis Urrutia](https://github.com/LuisUrrutia/TransbankSoap).  

A su vez, este paquete necesita las siguientes extensiones de PHP habilitadas:

* ext-curl
* ext-json
* ext-mbstring
* ext-soap
* ext-dom

Instalarlas dependerá de tu sistema: en algunos casos sólo necesitarás habilitarlas en tu `php.ini`; en otros, descargarlas usando tu gestor de packetes (como `apt-get` o `apk`) o compilarlas manualmente. 

# Instalación

Hay tres formas para instalar el paquete: usando Composer, sin composer, y todo de forma (muy) manual.

### Instalar con Composer (fácil)

Para usar el SDK en tu proyecto puedes usar Composer, instalándolo desde la consola:

```bash
composer require darkghosthunter/transbank-api
```

También puedes añadir el SDK como dependencia a tu proyecto y luego ejecutar `composer update`.

```json
    "require": {
        "darkghosthunter/transbank-api": "^2.0"
    }
```

### Instalación sin Composer (complicado)

Además de tener instalado la línea de comandos de PHP, debes descargar el código desde este repositorio, descomprimirlo en el directorio que desees, y realizar lo siguiente:

1 - [Descargar `composer.phar`](https://getcomposer.org/download/) en el mismo directorio donde descomprimiste el SDK.

2 - Ejecutar en el directorio del SDK:

```bash
php composer.phar install --no-dev
```

3 - Requerir el SDK directamente desde tu aplicación 

```php
require_once('/directorio/de/transbank-api/load.php');
```

### Instalación remota (jodido)

Si no tienes acceso a la consola de tu servidor web, siempre puedes usar tu propio sistema: 

* [Descarga PHP](http://php.net/downloads.php)

* [Descarga `composer.phar`](https://getcomposer.org/download) donde descargaste este paquete.

* Abre una ventana de consola (powershell en Windows, Terminal en MacOS, *sh en Linux) y tipea:

```bash
directorio/de/php composer.phar install --no-dev
```

> (Si estás en Windows, usa `php.exe`)

* Comprime el directorio del paquete.

* Sube el directorio del paquete a tu servidor y descomprímelo allí.

> Si subes cada archivo uno por uno, puedes demorarte horas.

* Continúa con el [tercer paso de la instalación manual](#instalación-sin-composer-complicado).

## Documentación 

La documentación de este paquete está [en la Wiki](https://github.com/DarkGhostHunter/TransbankApi/wiki).

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
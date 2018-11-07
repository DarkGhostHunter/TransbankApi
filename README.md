# Transbank SDK Wrapper - v0.5

Wrapper no-oficial de Transbank para mejorar la experiencia de uso del SDK oficial.

> Esta versión es incompatible con PHP 5. Para usar este código con PHP 5, usa directamente la versión 1.4.2.

> Esta paquete es un trabajo en progreso ¡No lo uses en producción hasta que no esté listo! 

## Requisitos:

- PHP 7.1 o mayor
- Composer

## Dependencias

Este paquete sólo necesita descargar el SDK oficial de Transbank.  

A su vez, el SDK oficial necesita las siguiente extensiones de PHP habilitadas:

* ext-curl
* ext-json
* ext-mbstring
* ext-soap
* ext-dom

Instalarlas dependerá de tu sistema. En algunos casos sólo necesitarás habilitarlas en tu `php.ini`, o usar `ini_set`. En otros, descargarlas usando tu gestor de packetes (como `apt-get` o `apk`) o compilarlas manualmente. 

# Instalación

Hay dos formas para instalar el paquete: usando Composer o descargando el paquete de forma manual y ~~darse la paja bajar las dependencias~~.

### Instalar con Composer

Para usar el SDK en tu proyecto puedes usar Composer, instalándolo desde la consola:

```bash
composer require transbank/transbank-sdk "2.0-beta"
```

También puedes añadir el SDK como dependencia a tu proyecto:

```json
    "require": {
        "darkghostgunter/transbank-wrapper": "0.5"
    }
```

### Instalación manual

Esto es un poco más jodido. Además de tener instalado PHP en algún lugar accesible, debes descargar el código desde este repositorio descomprimirlo en el directorio que desees, y realizar lo siguiente:

1 - [Descargar `composer.phar`](https://getcomposer.org/download/) en el directorio donde descomprimiste el SDK.

2 - Ejecutar en el directorio del SDK:

```bash
php composer.phar require --nodev
```
3 - Requerir el SDK directamente desde tu aplicación 

```php
require_once('/directorio/del/sdk/load.php');
```

Si no tienes acceso a la consola de tu servidor, siempre puedes usar tu propio sistema, descargar PHP, usar el ejecutable, comprimir el resultado y descomprimirlo manualmente en tu servidor web. Esto último es lo mejor salvo que teangas tiempo de sobra para subir cientos de archivos por FTP u otro protocolo.

## Documentación 

La documentación de este Wrapper está en la Wiki.

La información sobre las variables de cada transacción está en https://www.transbankdevelopers.cl.

## Información para contribuir y desarrollar este Wrapper

Tirar la talla en buen chileno en los PR. Si usas otro idioma, serás víctima de bullying.
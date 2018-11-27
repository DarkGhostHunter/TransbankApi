# Ejemplos del SDK

Este directorio contiene algunos archivos de ejemplo para probar el **ambiente de integración** de este paquete, con las credenciales incluídas.

Los ejemplos de transacciones que contiene son:

* Webpay Plus Normal
* Webpay Plus Mall Normal
* Webpay Plus Diferida, Captura y Anulación
* Webpay Oneclick Registro, Cargo, Revertir y Dar de baja (*desregistrar*).
* Oneclick Web/Mobile

## Uso

Primero, deberás iniciar este paquete usando Composer. Si no lo has hecho, puedes leer la sección de instalación en el [`README.md`](../README.md) que está en la raíz de este paquete.

Una vez instalado, y las dependencias de composer presentes, sólo debes ejecutar el servidor integrado de PHP esta línea de código.

```bash
php -S localhost:8080 -t .\ 
```

Esto creará una especie de servidor al cual podrás acceder vía `http://localhost:8080/`, con vínculos a cada ejemplo.

Si puedes probar cada transacción de forma exitosa, significa que tu servidor (y este paquete) funciona.
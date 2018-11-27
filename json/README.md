# Proposición Transbank REST v1.0

Este documento propone cómo conformar la implemetación de servicios REST de Transbank para mejorar la experiencia de integración en los comercios, y desarrollar diferentes plugins para cada aplicación web.

## Unificación a JSON

Todas las transacciones (Webpay, Onepay, Patpass) deberían tratarse como **recursos** (resources). 

Las transacciones compartirían una misma estructura JSON, siempre y cuando su naturaleza los permita.

> Por ejemplo, una transacción Webpay Plus Normal sería diferente a un registro de usuario en Patpass, pero sí sería similar a una cargo Webpay Oneclick Normal o Onepay Normal.

## Peticiones JSON

Las peticiones para crear recursos deberían ser empujadas como JSON (`application/json`) vía HTTP, usando la misma estructura del recurso, pero sólo con los valores rellenables por el comercio.

De esta forma, Transbank devolvería la transacción completa, con el estado (error, inválida, pendiente, etc).

## Verbos

En congruencia con [RFC7231](https://tools.ietf.org/html/rfc7231), los diferentes verbos (o métodos) HTTP deberían producir los siguientes resultados:

* `POST`: Crearía un recurso
* `GET`: Obtendría el recurso
* `PATCH`: Editaría los atributos permitidos del recurso.
* `PUT`: Sin soporte [RFC5789](https://tools.ietf.org/html/rfc5789).
* `DELETE`: Borraría el recurso (sólo se podría con registros de usuarios).

### DELETE

Considerando la naturaleza de los recursos como registros de usuarios (caso Patapass, Oneclick), `DELETE` debería marcar el estado de `deleted`.

Esto permitiría conseguir el recurso y sus relaciones (compras, subscripciones, etc.), pero invalidar cualquier acción relacionada con éste al estar virtualmente no disponible.

No encuentro alguna razón por la cual el recurso eliminado debería poder ser vuelto a no-eliminado, salvo la conveniencia por equivocaciones del comercio o usuario.

## Autenticación sin firma

Asumiendo que todas las operaciones serán sobre HTTPS (TLS 1.2+), sólo sería necesario ingresar el `Commerce ID` como `base64` - codificarlo así permite que si en un futuro aparece una nueva forma de identificadores de comercio, se mantenga leíble.

```http request
POST http://webpay4g.transbank.cl/webpay/plus
Content-Type: application/json
Authorization: Bearer MzM4OTIxODc=
```

Esto debería ser el estándar para todas los verbos HTTP. TLS se encargaría de los ataques [MITM](https://en.wikipedia.org/wiki/Man-in-the-middle_attack). 

### Secreto en llamadas asíncronas

En caso que los servicios Transbank decidan enviar peticiones asíncronas al proceso de la transacción (webhooks), estos deberían salir con string secreto entre Transbank y el comercio.

```http request
POST http://myapp.com/transbank/webhook
 
Transbank-Webhook-Secret: 9mzhY4Yd5UjdE3GEgJRwqC3
Content-Type: application/json
```
 
Como el `webhook` del comercio estaría expuesto, el secreto permitiría sólo aceptar peticiones desde Transbank, mientras que Transbank usa cualquier servidor en su poder para realizar el webhook (`http://webhook003.webpay4g.transbank.cl`, etcétera).

Sin el secreto compartido, sería posible averiguar por fuerza bruta las transacciones, 

## URIS

Las URIs deberían ser relacionadas con el servicio, producto, tipo y recurso a operar. 

```
http://webpay4g.transbank.cl/{servicio}/{producto}/
```

Por ejemplo, para crear una nueva transacción Webpay Plus Mall Normal, sólo habría que cambiar el tipo de recurso dentro de los datos a enviar.

```http request
POST http://webpay4g.transbank.cl/webpay/plus
Content-Type: application/json
{
  "type": "mall.normal"
  // ...
}
```

Lo que después nos permitiría obtenerla usando su identificador único de Transbank.

```http request
GET http://webpay4g.transbank.cl/webpay/plus/e5c326c93c2bb0812...
```

Lo mismo ocurriría para Oneclick, Patpass y Onepay:

| Servicio | Sub-producto | Recurso | Tipos
|---|---|---|---|
| Webpay | Plus | Transacción | `normal`, `normal.mall`
| Webpay | Oneclick | Transacción | `normal`, `normal.mall`
| Webpay | Oneclick | Usuario | 
| Patpass |  | Transacción | 
| Patpass |  | Usuario | 
| Onepay |  | Transacción | 

## Timestamps visibles

Todas las acciones realizadas sobre el recurso deberían ser visibles, y `null` cuando la acción no haya sido realizada.

* `createdAt`: Cuándo se creó en los sistemas de Transbank.
* `authorizedAt`: Cuándo se autorizó la transacción por parte del cliente. `null` cuando todavía no se ha autorizado. 
* `voidedAt`: Cuándo se anuló. `null` si no se ha anulado 

## Tiempo de vida de transacciones

Las transacciones deberían vivir por 5 minutos desde su creación en Transbank. Una vez resueltas completamente, debería ser posible adquirirlas hasta que desaparezcan del API REST, y deberían tener el timestamp `disappearedAt`, y golpear el cliente con código `410` o `404`.

Obviamente, lo más conveniente sería que no desaparecieran. Al parecer esto fue creado para mejorar el manejo de memoria Transbank, como si hubiese un cache de transacciones activas delante de la real base de datos.

## Confirmación opcional

La confirmación de la transacción debería ser opcional, configurable en el panel de opciones del comercio.

Si está activa, el comercio debería usar PATCH sobre el recurso con el estado `confirmed`, bajo los 30 segundos una vez autorizada la compra. 

Si no está activa, la transacción quedará marcada como confirmada al mismo tiempo que se efectúa el GET, evitando que el comercio o SDK deba incurrir en mayor lógica innecesariamente.

## Manejo de errores

Todo error que rompa el flujo de la transacción debería ser marcado con un `Exception`. Todo cambio de estado en el recurso no debería serlo.

Por ejemplo, lo que rompe el flujo de la transacción son:

* Timeout en el API REST (504)
* Recurso malformado, parámetros inválidos (422)
* Endpoint inválido (404)
* Transacción no encontrada (404)
* Faltan datos (412)
* Firmas inválidas (401)
* Operación no autorizada (comercio no tiene autorización, la transacción no tiene autorización, etc). (403)
* Conflicto de recurso (mismo ID, o acción ya fue hecha con anterioridad) (409)
* Transacción de monto cero (srsly? cero?) (402)

Pero cosas que no rompen el flujo de la transacción son, por ejemplo, cuando se informa que la transacción no fue pagada por el cliente (cambio de estado).

Para informar el error que rompe el flujo de forma más detallada, se puede empujar en JSON el código específico interno y la razón, junto al código de estado HTTP. El SDK estaría a cargo de lavantar el `Exception`.

```json5
// HTTP 403

{
    "error": "TRANSACTION_ALREADY_EXISTS",
    "message": "La transacción con ID 'e5c326c93...' ya existe en Onepay. Indica otro ID."
}
```

# Flujo de estado de la transacción

Una transacción puede tener un sólo estado, pero explicativo:

```
[Crear Transacción]
  --> Pendiente
      --> Autorizado/Pagado
      --> Expirado (ningún cambio después del tiempo de vida)
      --> Abortado (El usuario abortó)
      --> Rechazado (La institución financiera rechazó)
  --> Confirmado (Y Validado) / No Confirmado (e inválido)
  --> Anulado
```

Así se evita saber qué pasó con la transacción. Estos no son errores, son estados de la transacción, y es deber de la aplicación del comercio considerar si uno de ellos amerita un error grave o recomenzar el flujo de la transacción.

Por ejemplo, un cargo automático vía Oneclick que es rechazado amerita un `Exception` y detener toda lógica, pero para otra aplicación quizás no dado que es esperable. 

# Cuerpo de una transacción completa

Esto es sólo un ejemplo:

```json5
{
  "ids": {
    "transbankId": "c0061c75-58fa-4ee8-97a9-02b14098fc10",
    "commerceId": "transaction#322" 
  },
  "transactions": [ 
    {
      "type": "normal", 
      "transbankOrderId": "c0061c75-58fa-4ee8-97a9-02b14098fc10",
      "commerceOrderId": "transaction#322",
      "status": "paid",
      "amount": 9990,
      "payment": {
        "authorizationCode": 6549879,
        "type" : "VN",
        "card": {
          "type" : "visa",
          "number": "6623"
        }
      },
      "timestamps": {
          "createdAt": "2018-01-01 09:30:00+0000",
          "confirmedAt": "2018-01-01 09:31:00+0000",
          "authorizedAt": "2018-01-01 09:31:00+0000",
          "abortedAt": null,
          "voidedAt": null,
          "expiredAt": null,
      },
      "description": "This is my custom description, generated from my App."
    }
  ],
  "meta": {
    "urlGateway": "https://webpay4g.transbank.cl/webpay/payment",
    "urlVoucher": "https://webpay4g.transbank.cl/webpay/voucher",
  }
}
```

## Variables expresivas y coherentes

El código es poesía, las variables expresiones. El API debería evitar usar variables con nombres crípticos `occ`, `VCI` y etcétera.

### Timestamps

Todos los valores de tiempo deben indicar sobre qué acción es, e indicar el valor en formato [ISO 8601](https://www.iso.org/iso-8601-date-and-time-format.html) `2018-01-01T12:30:01.234+0000`, siempre UTC:

```json5
{
  "createdAt": "2018-01-01T12:30:01.234+0000",
  "deletedAt": "2018-01-02T14:32:23.512+0000"
}
```

La razón de esto es permitir que el comercio pueda manejar la diferencia horaria con mayor facilidad, y sea agnóstico al cambio de horario de verano o territorio (Isla de Pascua, Antártida, etcétera).

Como es estándar, esto permite al comercio ocupar cualquier herramienta para pasar el string a un formato que pueda entender.

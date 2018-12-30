# Contratos

Estas interfaces funcionan como contratos. Si quieres crear un nuevo servicio, implementa estas interfaces para que no pierdas la pista de qué hace qué cosa y cómo.

Si quieres, puedes usar las clases abstractas si vas a reusar código, que podrás encontrar en cada directorio para:

* Services
* Transactions
* TransactionFactories
* Responses
* ResponseFactories
* Adapters
* Clients

No hay contratos para los `ResponseFactories` y `TransactionFactories`, pues no es necesario que cada servicio necesite una clase para crear respuestas o transacciones, pero es lo recomendado cuando hay distintos tipos de respuestas o transacciones.
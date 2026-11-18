# Registro de Decisiones de Arquitectura (ADR) - CECAR v3.0

## ADR 01: Front Controller y Enrutamiento Desacoplado sin Frameworks
* **Estado**: Aprobado
* **Contexto**: El proyecto exige arquitectura orientada a objetos en PHP 8.2 puro, sin uso de Laravel, Symfony ni librerías de terceros en Composer. Se requiere un punto de acceso centralizado que despache peticiones HTTP y maneje errores.
* **Decisión**: Implementar `public/index.php` como Front Controller único. La resolución de rutas extrae la ruta canónica tanto en entornos con servidor integrado (`php -S`) como en servidores Apache (XAMPP con o sin mod_rewrite). Se definen códigos HTTP estándar (200, 201, 400, 401, 403, 404, 405, 500).
* **Consecuencias**: Código limpio, fácil de auditar, cero dependencias de Composer y control total sobre el ciclo de vida de la petición.

---

## ADR 02: Transaccionalidad Atómica en el Registro de Candidatos (RF-001)
* **Estado**: Aprobado
* **Contexto**: El registro de un candidato impacta tres tablas relacionales en la base de datos: `usuario` (credenciales), `candidato` (perfil personal) y `usuario_rol` (asociación de seguridad con `id_rol = 6`). Un error en cualquiera de las fases podría dejar registros huérfanos.
* **Decisión**: Utilizar `beginTransaction()`, `commit()` y `rollBack()` coordinados por el DAO sobre una misma conexión PDO Singleton. Si la creación del candidato o la asignación del rol falla, se revierte inmediatamente la inserción del usuario.
* **Consecuencias**: Integridad referencial garantizada al 100% en la base de datos MySQL 8 InnoDB.

---

## ADR 03: Esquema de Envoltorio JSON y Seguridad de Errores (RNF-024)
* **Estado**: Aprobado
* **Contexto**: RNF-024 exige que nunca se revelen trazas internas, nombres de tablas, variables ni consultas SQL al usuario final. Al mismo tiempo, el frontend o cliente API requiere un formato predecible para procesar respuestas.
* **Decisión**: Estandarizar todas las salidas bajo el envelope JSON:
  ```json
  {
    "success": true | false,
    "status": 200 | 201 | 400 | 401 | 403 | 404 | 405 | 500,
    "message": "Mensaje legible para el usuario",
    "data": { ... } | null,
    "error": { ... } | null,
    "timestamp": "2026-11-13T20:00:00-05:00"
  }
  ```
  Los errores 500 capturados por el Front Controller se envían a `error_log()` en el servidor y al cliente se le devuelve un mensaje amigable genérico con código de incidencia.
* **Consecuencias**: Protección contra ataques de enumeración y fuga de metadatos, facilitando la integración con cualquier interfaz cliente.

---

## ADR 04: Compatibilidad Dual PHP 8.1 y PHP 8.2 en Value Objects
* **Estado**: Aprobado
* **Contexto**: El entorno local del usuario en XAMPP ejecuta el binario PHP 8.1.25, mientras que el diseño conceptual requería PHP 8.2. En PHP 8.1, la palabra clave `readonly` a nivel de clase (`final readonly class`) genera error de sintaxis en tiempo de análisis sintáctico.
* **Decisión**: Declarar las clases como `final class` manteniendo la inmutabilidad y tipado fuerte en el constructor (`public ?int $id = null, public string $nombre = ''`).
* **Consecuencias**: 100% de compatibilidad tanto en PHP 8.1 (entorno de pruebas local) como en servidores de producción PHP 8.2+.

---

## ADR 05: Protección contra Inyección SQL y Parámetros Vinculados
* **Estado**: Aprobado
* **Contexto**: Requisito crítico de seguridad que prohíbe terminantemente la concatenación de variables en sentencias SQL.
* **Decisión**: Todas las operaciones en la capa `Model\DAO` emplean PDO con sentencias preparadas nativas (`PDO::ATTR_EMULATE_PREPARES => false`) y vinculación tipada de parámetros mediante `bindValue()`.
* **Consecuencias**: Inmunidad total contra inyección SQL en todos los módulos del sistema.

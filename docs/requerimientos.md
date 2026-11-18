# Matriz de Trazabilidad de Requerimientos - CECAR v3.0

## Incremento 1: Front Controller y Registro de Candidatos (RF-001)

| ID Requerimiento / Regla | Tipo | Descripción Funcional / Técnica | Implementación en Código | Criterio de Aceptación Verificable |
| :--- | :--- | :--- | :--- | :--- |
| **RF-001** | Funcional | Registro de candidatos externos con datos básicos (correo, contraseña, nombres, apellidos, tipo y número de documento, teléfono, ciudad) para postulación a convocatorias. | `Controller\AuthController::register()`, `Model\DAO\UsuarioDAO::crear()`, `Model\DAO\CandidatoDAO::crear()` | Envío de POST JSON crea atómicamente el registro en `usuario`, `candidato` y asigna rol en `usuario_rol`. Retorna HTTP 201 Created con ficha del candidato. |
| **RF-002** | Funcional | Inicio de sesión para candidatos mediante correo electrónico y contraseña. | `Controller\AuthController::login()`, `Model\DAO\UsuarioDAO::buscarPorCorreo()` | Autenticación con contraseña válida retorna HTTP 200 OK y datos de sesión. Credencial inválida retorna HTTP 401. Cuenta inactiva retorna HTTP 403. |
| **RN-004** | Regla Negocio | Complejidad de credenciales: La contraseña debe tener una longitud mínima de 8 caracteres. | `Controller\AuthController::register()` (línea 55) | Contraseñas con menos de 8 caracteres son rechazadas inmediatamente con HTTP 400 Bad Request y mensaje explícito de error. |
| **RNF-009** | No Funcional | Seguridad de almacenamiento: Cifrado y hashing seguro de contraseñas mediante algoritmo robusto (BCRYPT / Argon2id). | `Controller\AuthController::register()` (función nativa `password_hash($p, PASSWORD_BCRYPT, ['cost' => 12])`) | La columna `contrasena_hash` en BD nunca almacena texto plano ni algoritmos vulnerables (MD5/SHA1). |
| **RNF-024** | No Funcional | Manejo centralizado y seguro de errores: No exponer detalles internos, rutas físicas, consultas SQL ni trazas de excepciones a los usuarios. | `public/index.php` (bloque `try / catch (Throwable $e)`) | Cualquier fallo imprevisto genera un log interno en el servidor y entrega una respuesta HTTP 500 estandarizada sin nombres de tablas ni credenciales. |
| **RN-001** | Regla Negocio | Unicidad de cuentas: No se permite registrar dos usuarios con la misma dirección de correo electrónico. | `Model\DAO\UsuarioDAO::buscarPorCorreo()`, `Controller\AuthController::register()` | Si el correo ya existe, retorna HTTP 400 Bad Request con mensaje informativo sin crear registros huérfanos. |
| **RN-002** | Regla Negocio | Unicidad de documento: No se permite registrar dos candidatos con el mismo tipo y número de documento de identidad. | `Model\DAO\CandidatoDAO::existeDocumento()`, `Controller\AuthController::register()` | Si el documento ya existe, retorna HTTP 400 Bad Request y rechaza la transacción. |

---

## Mapeo Arquitectónico de Clases y Tablas

| Tabla SQL (`seleccion_personal_cecar`) | Value Object (`model/vo/`) | Data Access Object (`model/dao/`) | Controlador (`controller/`) |
| :--- | :--- | :--- | :--- |
| `usuario` | `Model\VO\UsuarioVO` | `Model\DAO\UsuarioDAO` | `Controller\AuthController` |
| `candidato` | `Model\VO\CandidatoVO` | `Model\DAO\CandidatoDAO` | `Controller\AuthController` |
| `usuario_rol` | `Model\VO\UsuarioRolVO` | `Model\DAO\UsuarioDAO::asignarRol` | `Controller\AuthController` |
| `rol` | `Model\VO\RolVO` | `Model\DAO\UsuarioDAO::tienePermiso` | `Controller\AuthController` |

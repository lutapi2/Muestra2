# Plan y Guía de Pruebas de Endpoints - CECAR v3.0

Esta guía describe los casos de prueba positivos y negativos con comandos `curl` listos para ejecutar contra el servidor local (ejecutado con `php -S localhost:8000 -t public` o en Apache XAMPP `http://localhost/seleccion_cecar/public`).

---

## 1. Verificación de Estado del Sistema (Health Check)
Verifica que el Front Controller y el autoloader respondan correctamente.

### Petición
```bash
curl -X GET http://localhost:8000/api/health
```

### Respuesta Esperada (HTTP 200 OK)
```json
{
  "status": "UP",
  "timestamp": "2026-11-13T20:15:00-05:00",
  "version": "3.0.0"
}
```

---

## 2. RF-001: Caso Positivo - Registro Exitoso de Nuevo Candidato
Registra un candidato que no existe previamente en la base de datos con contraseña >= 8 caracteres (RN-004).

### Petición
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "correo": "aspirante.docente2026@correo.edu.co",
    "password": "PasswordDocente2026*",
    "nombres": "Mateo David",
    "apellidos": "Morales Castillo",
    "id_tipo_documento": 1,
    "numero_documento": "1104859600",
    "telefono": "3007654321",
    "ciudad": "Sincelejo",
    "direccion": "Barrio La Palma Cra 25 # 12-40"
  }'
```

### Respuesta Esperada (HTTP 201 Created)
```json
{
  "success": true,
  "status": 201,
  "message": "Candidato registrado exitosamente en el Sistema de Selección CECAR.",
  "data": {
    "id_usuario": 14,
    "id_candidato": 6,
    "correo": "aspirante.docente2026@correo.edu.co",
    "nombre_completo": "Mateo David Morales Castillo",
    "tipo": "CANDIDATO",
    "rol": "CANDIDATO",
    "estado": "ACTIVO"
  },
  "error": null,
  "timestamp": "2026-11-13T20:15:05-05:00"
}
```

---

## 3. RF-001: Caso Negativo - Correo Electrónico Duplicado
Intenta registrar un candidato con un correo que ya existe en el sistema (ej. uno de los datos semilla `carlos.mendoza.post@gmail.com`).

### Petición
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "correo": "carlos.mendoza.post@gmail.com",
    "password": "PasswordDocente2026*",
    "nombres": "Carlos Duplicado",
    "apellidos": "Mendoza",
    "id_tipo_documento": 1,
    "numero_documento": "999888777",
    "telefono": "3001112233",
    "ciudad": "Sincelejo"
  }'
```

### Respuesta Esperada (HTTP 400 Bad Request)
```json
{
  "success": false,
  "status": 400,
  "message": "El correo electrónico suministrado ya se encuentra registrado en el sistema.",
  "data": null,
  "error": {
    "correo": "El correo electrónico ya existe."
  },
  "timestamp": "2026-11-13T20:15:10-05:00"
}
```

---

## 4. RF-001: Caso Negativo - Documento de Identidad Duplicado
Intenta registrar un candidato con un número y tipo de documento ya existente en la base de datos (`1102845920` con tipo 1).

### Petición
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "correo": "nuevo.usuario.cecar@correo.com",
    "password": "PasswordDocente2026*",
    "nombres": "Otro Aspirante",
    "apellidos": "Pérez",
    "id_tipo_documento": 1,
    "numero_documento": "1102845920",
    "telefono": "3009998877",
    "ciudad": "Corozal"
  }'
```

### Respuesta Esperada (HTTP 400 Bad Request)
```json
{
  "success": false,
  "status": 400,
  "message": "Ya existe un candidato registrado con el mismo tipo y número de documento.",
  "data": null,
  "error": {
    "numero_documento": "Documento de identidad ya registrado en el sistema."
  },
  "timestamp": "2026-11-13T20:15:15-05:00"
}
```

---

## 5. RN-004: Caso Negativo - Contraseña no Cumple Criterio Mínimo (< 8 caracteres)
Envía una contraseña de solo 5 caracteres.

### Petición
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "correo": "aspirante.corto@correo.com",
    "password": "12345",
    "nombres": "Juan",
    "apellidos": "Díaz",
    "id_tipo_documento": 1,
    "numero_documento": "1109994444",
    "telefono": "3105556677",
    "ciudad": "Sincelejo"
  }'
```

### Respuesta Esperada (HTTP 400 Bad Request)
```json
{
  "success": false,
  "status": 400,
  "message": "Error de validación en los datos del candidato.",
  "data": null,
  "error": {
    "password": "La contraseña debe tener una longitud mínima de 8 caracteres (RN-004)."
  },
  "timestamp": "2026-11-13T20:15:20-05:00"
}
```

---

## 6. Caso Negativo - Campos Obligatorios Faltantes
Envía un cuerpo vacío o sin los campos obligatorios del candidato.

### Petición
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{}'
```

### Respuesta Esperada (HTTP 400 Bad Request)
```json
{
  "success": false,
  "status": 400,
  "message": "Error de validación en los datos del candidato.",
  "data": null,
  "error": {
    "correo": "El campo 'correo' es obligatorio.",
    "password": "El campo 'password' es obligatorio.",
    "nombres": "El campo 'nombres' es obligatorio.",
    "apellidos": "El campo 'apellidos' es obligatorio.",
    "id_tipo_documento": "El campo 'id_tipo_documento' es obligatorio.",
    "numero_documento": "El campo 'numero_documento' es obligatorio.",
    "telefono": "El campo 'telefono' es obligatorio.",
    "ciudad": "El campo 'ciudad' es obligatorio."
  },
  "timestamp": "2026-11-13T20:15:25-05:00"
}
```

---

## 7. RF-002: Caso Positivo - Inicio de Sesión de Candidato
Autenticación correcta con credenciales registradas.

### Petición
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "correo": "aspirante.docente2026@correo.edu.co",
    "password": "PasswordDocente2026*"
  }'
```

### Respuesta Esperada (HTTP 200 OK)
```json
{
  "success": true,
  "status": 200,
  "message": "Autenticación exitosa.",
  "data": {
    "id_usuario": 14,
    "correo": "aspirante.docente2026@correo.edu.co",
    "nombre_completo": "Mateo David Morales Castillo",
    "tipo": "CANDIDATO",
    "estado": "ACTIVO",
    "candidato": {
      "id_candidato": 6,
      "nombres": "Mateo David",
      "apellidos": "Morales Castillo",
      "id_tipo_documento": 1,
      "numero_documento": "1104859600",
      "telefono": "3007654321",
      "ciudad": "Sincelejo"
    }
  },
  "error": null,
  "timestamp": "2026-11-13T20:15:30-05:00"
}
```

---

## 8. RF-002: Caso Negativo - Credenciales Inválidas
Autenticación con contraseña errónea.

### Petición
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "correo": "aspirante.docente2026@correo.edu.co",
    "password": "PasswordErrada123"
  }'
```

### Respuesta Esperada (HTTP 401 Unauthorized)
```json
{
  "success": false,
  "status": 401,
  "message": "Credenciales inválidas. Por favor verifique el correo y la contraseña ingresados.",
  "data": null,
  "error": {
    "auth": "Acceso no autorizado."
  },
  "timestamp": "2026-11-13T20:15:35-05:00"
}
```

---

## 9. Caso Negativo - Endpoint Inexistente (HTTP 404 Not Found)
Petición a una ruta que no está registrada en el Front Controller.

### Petición
```bash
curl -X GET http://localhost:8000/api/modulo_fantasma
```

### Respuesta Esperada (HTTP 404 Not Found)
```json
{
  "success": false,
  "status": 404,
  "message": "El endpoint solicitado '/api/modulo_fantasma' no existe en el Sistema de Selección CECAR.",
  "data": null,
  "error": {
    "ruta": "/api/modulo_fantasma"
  },
  "timestamp": "2026-11-13T20:15:40-05:00"
}
```

---

## 10. Caso Negativo - Método HTTP No Permitido (HTTP 405 Method Not Allowed)
Enviar un método `GET` al endpoint `/api/auth/register` (que solo acepta `POST`).

### Petición
```bash
curl -X GET http://localhost:8000/api/auth/register
```

### Respuesta Esperada (HTTP 405 Method Not Allowed)
Cabecera devuelta: `Allow: POST`
```json
{
  "success": false,
  "status": 405,
  "message": "Método HTTP 'GET' no permitido para la ruta '/api/auth/register'. Métodos permitidos: POST.",
  "data": null,
  "error": {
    "metodos_permitidos": ["POST"]
  },
  "timestamp": "2026-11-13T20:15:45-05:00"
}
```

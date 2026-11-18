# Sistema de Selección de Personal CECAR v3.0 (2026)

Prototipo verificable y trazable del backend desarrollado en **PHP 8.2 orientado a objetos sin frameworks** y **MySQL 8.0+**, correspondiente al proyecto institucional de CECAR.

---

## 📁 Estructura del Repositorio

```text
seleccion_cecar/
├── config/
│   └── database.php           # Conexión Singleton PDO MySQL 8 (UTF8MB4, Bogota -05:00)
├── database/
│   ├── 01_schema.sql          # DDL aprobado (58 tablas, SPs, Triggers)
│   └── 02_seed.sql            # Datos maestros, RBAC, catálogos y casos de prueba
├── model/
│   ├── vo/                    # 58 Clases Value Object (1 por tabla, inmutables, toArray/fromArray)
│   └── dao/                   # BaseDAO y 16 DAOs especializados con sentencias preparadas
├── controller/
│   ├── BaseController.php     # Controlador base con parsing JSON y envelope HTTP
│   └── AuthController.php     # RF-001 (Registro candidato), RF-002 (Login), RN-004, RNF-009
├── public/
│   ├── index.php              # Front Controller con enrutador central y manejo 400-500
│   └── .htaccess              # Reglas de reescritura para Apache (XAMPP)
├── docs/
│   ├── requerimientos.md      # Matriz de trazabilidad RF / RN / RNF
│   ├── decisiones.md          # Registro de decisiones arquitectónicas (ADRs)
│   └── pruebas.md             # Casos de prueba detallados con comandos cURL
├── autoload.php               # Autoloader PSR-4 para Config, Model y Controller
└── README.md                  # Este documento
```

---

## 🚀 Requisitos Previos

- **PHP**: 8.1.0 o superior (compatible nativamente con PHP 8.1 y 8.2).
- **MySQL**: 8.0 o superior con soporte para `JSON`, triggers y funciones de ventana.
- **Servidor Web**: Servidor integrado de PHP (`php -S`) o Apache en XAMPP.

---

## ⚙️ Configuración Inicial

### 1. Base de Datos
1. Inicie el servicio de MySQL (por ejemplo, desde el panel de control de XAMPP).
2. Cree la base de datos e importe los scripts en orden:
   ```bash
   mysql -u root -p < database/01_schema.sql
   mysql -u root -p < database/02_seed.sql
   ```
3. Verifique las credenciales en `config/database.php` (por defecto: `host=127.0.0.1`, `dbname=seleccion_personal_cecar`, `user=root`, `password=""`).

---

## 💻 Ejecución del Servidor

### Opción A: Servidor Integrado de PHP (Recomendada para Desarrollo)
Abra una terminal en la raíz del proyecto (`seleccion_cecar/`) y ejecute:
```bash
php -S localhost:8000 -t public
```
El API quedará disponible de inmediato en `http://localhost:8000`.

### Opción B: Apache en XAMPP
Copie o mueva la carpeta `seleccion_cecar` dentro de `C:\xampp\htdocs\`.
Acceda mediante: `http://localhost/seleccion_cecar/public/`.

---

## 🧪 Pruebas Rápidas de Endpoints (Incremento 1)

### 1. Comprobación de Salud del API
```bash
curl -X GET http://localhost:8000/api/health
```

### 2. RF-001: Registro de un Candidato
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "correo": "docente.aspirante@correo.edu.co",
    "password": "PasswordDocente2026*",
    "nombres": "Mateo David",
    "apellidos": "Morales Castillo",
    "id_tipo_documento": 1,
    "numero_documento": "1104859600",
    "telefono": "3007654321",
    "ciudad": "Sincelejo"
  }'
```

### 3. RF-002: Inicio de Sesión
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "correo": "docente.aspirante@correo.edu.co",
    "password": "PasswordDocente2026*"
  }'
```

Consulte el archivo [docs/pruebas.md](file:///C:/Users/PC/.gemini/antigravity/scratch/seleccion_cecar/docs/pruebas.md) para ver la totalidad de pruebas negativas (duplicidad de correo, duplicidad de cédula, contraseñas cortas, errores 404 y 405).

---

## 🔒 Estándares de Seguridad Implementados
- **RNF-009**: Contraseñas procesadas exclusivamente con `password_hash()` usando BCRYPT (`cost = 12`).
- **RNF-024**: Manejo centralizado de excepciones en `public/index.php`. No se filtran trazas internas ni esquemas de bases de datos al cliente.
- **Preparación de Consultas**: 100% de consultas SQL usan sentencias preparadas con PDO (`bindValue`), imposibilitando ataques de inyección SQL.
- **Cabeceras HTTP de Seguridad**: Emisión automática de `X-Content-Type-Options: nosniff` y `X-Frame-Options: DENY`.

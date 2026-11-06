# Plataforma de Contratación y Selección de Personal - CECAR

Sistema integral de gestión de talento humano desarrollado en **PHP** siguiendo una arquitectura por capas (**MVC**), persistencia relacional con **SQLite / MySQL**, separación estricta de responsabilidades, seguridad por roles y estricta correspondencia visual y funcional con la **Especificación de Requerimientos Versión 3.0** y las **imágenes de referencia**.

---

## 🌟 Correspondencia con las Interfaces de Referencia

1. **Página Principal (Imagen 1 - Superior):**
   - Interfaz moderna en modo oscuro (`#071310`).
   - Título principal: *"Toda tu gestión administrativa en un solo lugar"*.
   - Botones de acción *"Iniciar sesión"* y *"Ver cómo funciona"*.
   - Ventana ilustrativa de estados de trazabilidad: `Aprobado` (verde), `En proceso` (ámbar) y `Firmado` (esmeralda).
   - Acceso contextual flotante a `Tutorial`.

2. **Sistema de Selección y Dashboard Institucional (Imagen 1 - Inferior):**
   - Banner verde institucional: *"¡Bienvenido al Sistema de Gestión! Accede a todos los módulos desde el menú lateral. Tu sesión está activa y segura."*
   - Indicadores de contexto: `EMPRESA: CECAR`, `POSICIÓN: Gestión de Talento Humano`, `SEDE: Sincelejo` y avatar de usuario.
   - Cajas de navegación rápida: *Usuarios (Gestión completa)*, *Activos (Control total)*, *Tareas (Pendientes)* y *Reportes (Análisis)*.
   - Acciones Rápidas: *Nuevo Título*, *Registrar Activo* y *Generar Reporte*.
   - Menú lateral con Dashboard, Mesa de Ayudas, Procesos de selección y Cerrar sesión.

3. **Gestión de Contratación de Seleccionados (Imagen 2):**
   - Encabezado: *"Gestión de Contratación de Seleccionados - Administra tus documentos por proceso"*.
   - 6 Contadores superiores exactos:
     - `1` Usuarios
     - `25` Documentos
     - `25` Aprobados
     - `0` Pendientes Revisión
     - `0` Rechazados
     - `0` Subsanados
   - Tarjeta de filtros *"Mis Documentos por Proceso [1]"*:
     - Fila 1: *Todos los procesos*, *Todas las unidades*, *Estado proceso*, *Contratación de Seleccionados*, búsqueda y botón verde de recarga.
     - Fila 2: *Buscar usuario...*, *Incompleto*, *Estados de Re...*, *Estado carga de documentos* (Carga completa / Carga incompleta / En Formalización / Contratado), *Ver: Todos*, botón *⬇️ Autorizados*.
   - Tabla con columnas: `USUARIO`, `PROCESO`, `CARGADOS`, `APROBADOS`, `PENDIENTES POR REVISIÓN`, `RECHAZADOS`, `ACCIÓN`.
   - Registro de aspirante exacto: *Aspirante Nuevo a docente cátedra* | *Facultad de Educación y Ciencias Sociales (Activo)* | *23/23 Contratado* | *23* | *0* | *0* | Acciones.

4. **Documentos para Aprobación (Imagen 3):**
   - Acordeón de 8 categorías documentales con conteos:
     1. `Contrato (1/1)`
     2. `Información Personal (3/3)`
     3. `Formación Académica (5/5)`
     4. `Experiencia Laboral (1/1)`
     5. `Seguridad Social (2/2)`
     6. `Información Bancaria (1/1)` -> **Certificado Apertura Cuenta Davivienda \*** (verificado con check verde).
     7. `Salud Ocupacional (1/1)` -> Concepto de aptitud laboral sin datos clínicos confidenciales (RN-027).
     8. `Antecedentes Legales (8/8)` -> Procuraduría, Contraloría, Policía, RNMC, Ley 1918, REDAM, Tarjeta Profesional y Listas Restrictivas.

---

## 🏗️ Arquitectura por Capas del Software (RNF-023)

```
plataforma-contratacion/
├── config/
│   ├── app.php                # Constantes de entorno, subidas y zona horaria (America/Bogota)
│   └── database.php           # Configuración de base de datos relacional (SQLite / MySQL)
├── database/
│   ├── schema.sql             # Esquema DDL de 22 tablas normalizadas
│   └── seed.sql               # Semilla con datos institucionales reales de CECAR
├── app/
│   ├── Core/
│   │   ├── Database.php       # Gestor PDO Singleton con auto-migración en primer arranque
│   │   ├── Request.php        # Envoltorio de peticiones HTTP, sanitización y tokens CSRF
│   │   ├── Response.php       # Respuestas JSON, redirecciones y streaming seguro de archivos
│   │   ├── Router.php         # Front Controller y despachador de acciones
│   │   └── View.php           # Renderizador de plantillas con soporte para layouts
│   ├── Helpers/
│   │   ├── AuthHelper.php     # Control de sesiones seguras, expiración y control de roles (RBAC)
│   │   ├── FileHelper.php     # Validación estricta de archivos MIME (finfo) y almacenamiento
│   │   ├── AuditHelper.php    # Bitácora inmutable de auditoría y trazabilidad histórica (RN-020)
│   │   ├── NotifyHelper.php   # Despachador de notificaciones internas del portal
│   │   └── FormatHelper.php   # Formateo de fechas colombianas, moneda y badges de estados
│   ├── Models/
│   │   ├── Usuario.php        # Gestión de cuentas institucionales y candidatos
│   │   ├── Convocatoria.php   # Administración y ciclo de vida de convocatorias
│   │   ├── Postulacion.php    # Expediente del aspirante, radicación y progreso
│   │   ├── ItemHV.php         # Ítems independientes de hoja de vida y soportes vinculados (RN-005)
│   │   ├── Contratacion.php   # Lógica del módulo de Contratación de Seleccionados (Imágenes 2 y 3)
│   │   ├── Entrevista.php     # Agendamiento sin traslapes (RN-017) y evaluación individual
│   │   ├── PlanTrabajo.php    # Hitos y seguimiento de coordinación de selección (RF-091)
│   │   └── TicketAyuda.php    # Mesa de ayuda institucional (RF-099)
│   └── Controllers/
│       ├── HomeController.php         # Landing page (Imagen 1 Superior) y tutorial
│       ├── AuthController.php         # Login, registro de aspirante y conmutador rápido de roles
│       ├── DashboardController.php    # Tablero de mandos institucional (Imagen 1 Inferior)
│       ├── ConvocatoriaController.php # Catálogo y publicación de convocatorias
│       ├── PostulacionController.php  # Portal del candidato, carga de ítems y radicación
│       ├── RevisionController.php     # Revisión documental lado a lado y subsanaciones (RN-011)
│       ├── EvaluacionController.php   # Matriz de calificación y causales de descarte (RN-024)
│       ├── EntrevistaController.php   # Programación de entrevistas y comité evaluador
│       ├── ContratacionController.php # Contratación de Seleccionados y vinculación laboral
│       ├── KanbanController.php       # Tablero Kanban y línea de tiempo por aspirante (RF-092)
│       ├── PlanTrabajoController.php  # Planes de trabajo y cronograma de hitos (RF-091)
│       ├── ReportesController.php     # Los 3 reportes exigidos (RF-096) y exportación a Excel
│       ├── MesaAyudaController.php    # Mesa de ayuda y solicitudes (RF-099)
│       ├── AdminController.php        # Administración de usuarios, catálogos y auditoría
│       └── DescargaController.php     # Entrega segura y protegida de documentos (RNF-013)
├── public/
│   ├── index.php              # Punto de entrada único del servidor web
│   ├── css/
│   │   └── theme.css          # Estilos institucionales CECAR acordes a las imágenes
│   └── js/
│       ├── app.js             # Filtros dinámicos, acordeones y control de modales
│       └── tutorial.js        # Tutorial interactivo contextual (RF-083)
├── storage/
│   ├── database.sqlite        # Base de datos portable generada automáticamente
│   └── uploads/               # Almacenamiento seguro de soportes en PDF
└── views/
    ├── layouts/               # Plantillas estructurales (main y landing)
    ├── home/                  # Vistas públicas de inicio y explicación
    ├── auth/                  # Vistas de autenticación y registro
    ├── dashboard/             # Tablero institucional con banner verde
    ├── convocatorias/         # Gestión de convocatorias y perfiles
    ├── postulacion/           # Formulario por secciones y constancia de radicación
    ├── revision/              # Bandeja y visor de revisión lado a lado
    ├── contratacion/          # Gestión de Contratación de Seleccionados (Imágenes 2 y 3)
    ├── evaluacion/            # Matriz de puntajes, pruebas y causales
    ├── entrevistas/           # Agendamiento sin conflictos y rúbricas
    ├── kanban/                # Tablero visual y línea de tiempo histórica
    ├── plan_trabajo/          # Tareas operativas de talento humano
    ├── mesa_ayuda/            # Solicitudes y respuestas de soporte
    └── reportes/              # Informes institucionales y filtros ampliados
```

---

## 🔑 Credenciales de Acceso Preconfiguradas

Para facilitar la evaluación de los diferentes roles contemplados en el sistema, puede iniciar sesión con las siguientes cuentas o utilizar el **Conmutador Rápido de Roles** disponible en la barra superior:

| Rol Institucional | Correo Electrónico | Contraseña | Propósito |
| :--- | :--- | :--- | :--- |
| **Talento Humano** | `talento.humano@cecar.edu.co` | `talento123` | Gestión completa de convocatorias, revisión, contratación y vinculación |
| **Revisor Documental** | `revisor@cecar.edu.co` | `talento123` | Verificación de soportes lado a lado y requerimientos de subsanación |
| **Candidato / Seleccionado** | `candidato@correo.com` | `candidato123` | Aspirante Juan Carlos Pérez Gómez (Aspirante de la Imagen 2 y 3) |
| **Comité / Evaluador** | `evaluador@cecar.edu.co` | `talento123` | Calificación de pruebas psicotécnicas, aula y entrevistas |
| **Desarrollo Profesoral** | `docencia@cecar.edu.co` | `talento123` | Calificación especializada de prueba de aula pedagógica |
| **Administrador** | `admin@cecar.edu.co` | `admin123` | Gestión de usuarios, catálogos, causales y auditoría de seguridad |

---

## 🚀 Instrucciones de Ejecución Local

### Opción A: Servidor Interno de PHP (Inmediato)
Ejecute en la terminal PowerShell dentro del directorio del proyecto:
```powershell
& "C:\xampp\php\php.exe" -S 127.0.0.1:8000 -t "C:\Users\USER\.gemini\antigravity\scratch\plataforma-contratacion\public"
```
Luego abra en su navegador:
**[http://127.0.0.1:8000/](http://127.0.0.1:8000/)**

### Opción B: Apache / XAMPP
El proyecto se encuentra enlazado automáticamente con `C:\xampp\htdocs\plataforma-contratacion`. Inicie el servicio Apache desde el panel de control de XAMPP y abra:
**[http://localhost/plataforma-contratacion/public/](http://localhost/plataforma-contratacion/public/)**

---

## ✅ Cumplimiento de Requerimientos y Reglas de Negocio

- **Ítems Independientes con Soporte (RN-005, RN-006):** Cada título, certificado laboral, cuenta bancaria o documento legal se almacena como un registro autónomo en `items_hv` con su correspondiente archivo en `soportes`.
- **Subsanación Controlada (RN-011, RF-049):** Al emitirse una observación, el candidato únicamente puede editar los ítems observados; el resto del expediente se mantiene protegido contra alteraciones.
- **Sin Conflictos de Agenda en Entrevistas (RN-017, RF-064):** El modelo valida traslapes temporales antes de confirmar una cita con el comité evaluador.
- **Causales de Descarte Obligatorias (RN-024, RF-093):** Ningún aspirante puede ser descartado sin asociar una causal estandarizada del catálogo institucional (DES-01 a DES-08).
- **Vinculación con Transferencia a Hoja de Vida (RF-104, RF-105, RN-025):** Al formalizarse la contratación, se genera el código oficial de colaborador y se registra la sincronización con el sistema institucional.
- **Privacidad y Salud Ocupacional (RN-027, RNF-028):** El módulo de salud ocupacional registra únicamente el concepto de aptitud laboral (`Apto` / `Apto con restricciones` / `No apto`) sin exponer información médica confidencial.

-- ============================================================================
-- PLATAFORMA DE CONTRATACIÓN Y SELECCIÓN DE PERSONAL - CECAR
-- Esquema Relacional de Base de Datos (SQLite / MySQL Compatible)
-- Cumple con RF-001 a RF-106, RNF-001 a RNF-030 y Reglas RN-001 a RN-028
-- ============================================================================

CREATE TABLE IF NOT EXISTS dependencias (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nombre VARCHAR(150) NOT NULL,
    sigla VARCHAR(20) NOT NULL,
    facultad VARCHAR(150) NOT NULL,
    sede VARCHAR(100) NOT NULL DEFAULT 'Sincelejo',
    activa INTEGER NOT NULL DEFAULT 1
);

CREATE TABLE IF NOT EXISTS roles (
    id VARCHAR(30) PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT
);

CREATE TABLE IF NOT EXISTS usuarios (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    tipo_documento VARCHAR(10) NOT NULL DEFAULT 'CC',
    numero_documento VARCHAR(30) NOT NULL UNIQUE,
    correo VARCHAR(150) NOT NULL UNIQUE,
    telefono VARCHAR(30),
    ciudad VARCHAR(100) DEFAULT 'Sincelejo',
    direccion VARCHAR(200),
    password_hash VARCHAR(255) NOT NULL,
    rol VARCHAR(30) NOT NULL DEFAULT 'candidato',
    dependencia_id INTEGER,
    cargo_interno VARCHAR(150),
    activo INTEGER NOT NULL DEFAULT 1,
    ultimo_acceso DATETIME,
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rol) REFERENCES roles(id),
    FOREIGN KEY (dependencia_id) REFERENCES dependencias(id)
);

CREATE TABLE IF NOT EXISTS consentimientos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    usuario_id INTEGER NOT NULL,
    finalidad VARCHAR(150) NOT NULL,
    version_texto VARCHAR(20) NOT NULL DEFAULT 'v3.0-2026',
    ip_origen VARCHAR(50),
    fecha_hora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE IF NOT EXISTS convocatorias (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    cargo VARCHAR(150) NOT NULL,
    dependencia_id INTEGER NOT NULL,
    facultad VARCHAR(150) NOT NULL,
    programa VARCHAR(150),
    sede VARCHAR(100) NOT NULL DEFAULT 'Sincelejo',
    periodo_academico VARCHAR(20) NOT NULL DEFAULT '2026-2',
    vacantes INTEGER NOT NULL DEFAULT 1,
    modalidad VARCHAR(50) NOT NULL DEFAULT 'Tiempo Completo',
    tipo_convocatoria VARCHAR(50) NOT NULL DEFAULT 'Docente',
    descripcion TEXT,
    fecha_apertura DATETIME NOT NULL,
    fecha_cierre DATETIME NOT NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'borrador', -- borrador, publicada, cerrada, retirada
    motivo_retiro TEXT,
    creado_por INTEGER,
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (dependencia_id) REFERENCES dependencias(id),
    FOREIGN KEY (creado_por) REFERENCES usuarios(id)
);

CREATE TABLE IF NOT EXISTS perfil_cargo (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    convocatoria_id INTEGER NOT NULL UNIQUE,
    formacion_minima TEXT NOT NULL,
    experiencia_minima_meses INTEGER NOT NULL DEFAULT 12,
    competencias TEXT,
    funciones TEXT,
    condiciones TEXT,
    FOREIGN KEY (convocatoria_id) REFERENCES convocatorias(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS requisitos_convocatoria (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    convocatoria_id INTEGER NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    tipo_item VARCHAR(50) NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    obligatoriedad VARCHAR(20) NOT NULL DEFAULT 'obligatorio', -- obligatorio, condicional, opcional
    soporte_requerido INTEGER NOT NULL DEFAULT 1,
    FOREIGN KEY (convocatoria_id) REFERENCES convocatorias(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS criterios_evaluacion (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    convocatoria_id INTEGER NOT NULL,
    etapa VARCHAR(50) NOT NULL, -- documental, psicotecnica, conocimiento, aula, entrevista
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    puntaje_maximo REAL NOT NULL DEFAULT 100,
    ponderacion REAL NOT NULL DEFAULT 100,
    obligatorio INTEGER NOT NULL DEFAULT 1,
    FOREIGN KEY (convocatoria_id) REFERENCES convocatorias(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS postulaciones (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    convocatoria_id INTEGER NOT NULL,
    usuario_id INTEGER NOT NULL,
    codigo_radicado VARCHAR(50) NOT NULL UNIQUE,
    estado VARCHAR(40) NOT NULL DEFAULT 'borrador', 
    -- borrador, enviada, en_revision, subsanacion_requerida, habilitada, no_habilitada, 
    -- preseleccionada, descartada, seleccionada, no_seleccionada, en_formalizacion, contratado, vinculado, retirada
    resultado_documental VARCHAR(30) NOT NULL DEFAULT 'pendiente', -- pendiente, conforme, observado, no_conforme
    porcentaje_avance INTEGER NOT NULL DEFAULT 0,
    version INTEGER NOT NULL DEFAULT 1,
    declaracion_veracidad INTEGER NOT NULL DEFAULT 0,
    fecha_declaracion DATETIME,
    fecha_postulacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_envio DATETIME,
    motivo_descarte TEXT,
    causal_descarte_id VARCHAR(50),
    etapa_actual VARCHAR(50) NOT NULL DEFAULT 'revision_documental',
    banco_talento_consentimiento INTEGER NOT NULL DEFAULT 0,
    notas_internas TEXT,
    FOREIGN KEY (convocatoria_id) REFERENCES convocatorias(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    UNIQUE (convocatoria_id, usuario_id)
);

CREATE TABLE IF NOT EXISTS items_hv (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    postulacion_id INTEGER NOT NULL,
    categoria VARCHAR(50) NOT NULL, 
    -- personal, formacion, laboral, docente, investigativa, licencias, complementaria, idiomas, competencias, publicaciones, reconocimientos, referencias, disponibilidad, seguridad_social, bancaria, antecedentes, contrato
    tipo_item VARCHAR(50) NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    datos_json TEXT NOT NULL,
    version INTEGER NOT NULL DEFAULT 1,
    estado VARCHAR(30) NOT NULL DEFAULT 'borrador', -- borrador, enviado, aprobado, observado, rechazado
    requiere_subsanacion INTEGER NOT NULL DEFAULT 0,
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (postulacion_id) REFERENCES postulaciones(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS soportes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    item_hv_id INTEGER,
    postulacion_id INTEGER NOT NULL,
    nombre_original VARCHAR(255) NOT NULL,
    nombre_almacenado VARCHAR(255) NOT NULL,
    ruta VARCHAR(255) NOT NULL,
    mime VARCHAR(100) NOT NULL,
    tamano_bytes INTEGER NOT NULL,
    hash_sha256 VARCHAR(64),
    version INTEGER NOT NULL DEFAULT 1,
    fecha_carga DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (item_hv_id) REFERENCES items_hv(id) ON DELETE SET NULL,
    FOREIGN KEY (postulacion_id) REFERENCES postulaciones(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS revisiones_item (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    item_hv_id INTEGER NOT NULL,
    revisor_id INTEGER NOT NULL,
    resultado VARCHAR(30) NOT NULL, -- aprobado, observado, rechazado
    motivo VARCHAR(200),
    observacion TEXT,
    fecha_revision DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (item_hv_id) REFERENCES items_hv(id) ON DELETE CASCADE,
    FOREIGN KEY (revisor_id) REFERENCES usuarios(id)
);

CREATE TABLE IF NOT EXISTS solicitudes_subsanacion (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    postulacion_id INTEGER NOT NULL,
    solicitante_id INTEGER NOT NULL,
    fecha_solicitud DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_limite DATETIME NOT NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'pendiente', -- pendiente, respondida, vencida
    observaciones TEXT,
    FOREIGN KEY (postulacion_id) REFERENCES postulaciones(id) ON DELETE CASCADE,
    FOREIGN KEY (solicitante_id) REFERENCES usuarios(id)
);

CREATE TABLE IF NOT EXISTS detalles_subsanacion (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    solicitud_id INTEGER NOT NULL,
    item_hv_id INTEGER NOT NULL,
    motivo_observacion TEXT NOT NULL,
    subsanado INTEGER NOT NULL DEFAULT 0,
    FOREIGN KEY (solicitud_id) REFERENCES solicitudes_subsanacion(id) ON DELETE CASCADE,
    FOREIGN KEY (item_hv_id) REFERENCES items_hv(id)
);

CREATE TABLE IF NOT EXISTS evaluaciones (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    postulacion_id INTEGER NOT NULL,
    criterio_id INTEGER NOT NULL,
    evaluador_id INTEGER NOT NULL,
    puntaje REAL NOT NULL,
    resultado VARCHAR(30) NOT NULL, -- cumple, no_cumple, sobresaliente
    observacion TEXT,
    fecha_evaluacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (postulacion_id) REFERENCES postulaciones(id) ON DELETE CASCADE,
    FOREIGN KEY (criterio_id) REFERENCES criterios_evaluacion(id),
    FOREIGN KEY (evaluador_id) REFERENCES usuarios(id)
);

CREATE TABLE IF NOT EXISTS pruebas (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    postulacion_id INTEGER NOT NULL,
    tipo VARCHAR(50) NOT NULL, -- psicotecnica, conocimiento, aula_docente
    responsable_id INTEGER NOT NULL,
    calificacion REAL,
    dictamen VARCHAR(30) NOT NULL, -- aprobado, no_aprobado
    observaciones TEXT,
    soporte_id INTEGER,
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (postulacion_id) REFERENCES postulaciones(id) ON DELETE CASCADE,
    FOREIGN KEY (responsable_id) REFERENCES usuarios(id),
    FOREIGN KEY (soporte_id) REFERENCES soportes(id)
);

CREATE TABLE IF NOT EXISTS franjas_entrevista (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    convocatoria_id INTEGER NOT NULL,
    fecha DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    modalidad VARCHAR(30) NOT NULL DEFAULT 'virtual', -- presencial, virtual
    ubicacion VARCHAR(200),
    enlace VARCHAR(255),
    cupos INTEGER NOT NULL DEFAULT 1,
    activa INTEGER NOT NULL DEFAULT 1,
    FOREIGN KEY (convocatoria_id) REFERENCES convocatorias(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS entrevistas (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    postulacion_id INTEGER NOT NULL UNIQUE,
    franja_id INTEGER,
    fecha DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    modalidad VARCHAR(30) NOT NULL DEFAULT 'virtual',
    ubicacion VARCHAR(200),
    enlace VARCHAR(255),
    estado VARCHAR(30) NOT NULL DEFAULT 'programada', -- programada, confirmada, realizada, reprogramada, cancelada
    motivo_cambio TEXT,
    fecha_programacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (postulacion_id) REFERENCES postulaciones(id) ON DELETE CASCADE,
    FOREIGN KEY (franja_id) REFERENCES franjas_entrevista(id)
);

CREATE TABLE IF NOT EXISTS entrevistadores (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    entrevista_id INTEGER NOT NULL,
    usuario_id INTEGER NOT NULL,
    rol_comite VARCHAR(50) NOT NULL DEFAULT 'evaluador',
    FOREIGN KEY (entrevista_id) REFERENCES entrevistas(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    UNIQUE (entrevista_id, usuario_id)
);

CREATE TABLE IF NOT EXISTS resultados_entrevista (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    entrevista_id INTEGER NOT NULL,
    evaluador_id INTEGER NOT NULL,
    puntaje REAL NOT NULL,
    concepto VARCHAR(50) NOT NULL, -- favorable, desfavorable, condicionado
    observacion TEXT,
    tipo VARCHAR(20) NOT NULL DEFAULT 'individual', -- individual, consolidado
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (entrevista_id) REFERENCES entrevistas(id) ON DELETE CASCADE,
    FOREIGN KEY (evaluador_id) REFERENCES usuarios(id)
);

CREATE TABLE IF NOT EXISTS decisiones_finales (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    postulacion_id INTEGER NOT NULL UNIQUE,
    resultado VARCHAR(30) NOT NULL, -- seleccionado, no_seleccionado, elegible
    fundamento TEXT NOT NULL,
    usuario_id INTEGER NOT NULL,
    fecha_publicacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (postulacion_id) REFERENCES postulaciones(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE IF NOT EXISTS examenes_ocupacionales (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    postulacion_id INTEGER NOT NULL UNIQUE,
    proveedor VARCHAR(150) NOT NULL DEFAULT 'SST Institucional CECAR',
    fecha_programada DATE,
    fecha_examen DATE,
    concepto_aptitud VARCHAR(40) NOT NULL DEFAULT 'pendiente', -- apto, apto_con_restricciones, no_apto, pendiente
    soporte_id INTEGER,
    registrado_por INTEGER,
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (postulacion_id) REFERENCES postulaciones(id) ON DELETE CASCADE,
    FOREIGN KEY (soporte_id) REFERENCES soportes(id),
    FOREIGN KEY (registrado_por) REFERENCES usuarios(id)
);

CREATE TABLE IF NOT EXISTS contrataciones (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    postulacion_id INTEGER NOT NULL UNIQUE,
    responsable_id INTEGER,
    estado_formalizacion VARCHAR(30) NOT NULL DEFAULT 'pendiente', -- pendiente, en_tramite, firmado, rechazado, formalizado
    estado_carga_documentos VARCHAR(30) NOT NULL DEFAULT 'incompleta', -- completa, incompleta
    salario_asignado REAL DEFAULT 0,
    fecha_inicio_labores DATE,
    fecha_firma DATETIME,
    observaciones TEXT,
    soporte_contrato_id INTEGER,
    fecha_actualizacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (postulacion_id) REFERENCES postulaciones(id) ON DELETE CASCADE,
    FOREIGN KEY (responsable_id) REFERENCES usuarios(id),
    FOREIGN KEY (soporte_contrato_id) REFERENCES soportes(id)
);

CREATE TABLE IF NOT EXISTS vinculaciones (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    contratacion_id INTEGER NOT NULL UNIQUE,
    postulacion_id INTEGER NOT NULL UNIQUE,
    codigo_colaborador VARCHAR(50) NOT NULL UNIQUE,
    fecha_vinculacion DATE NOT NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'vinculado', -- vinculado, anulado
    confirmado_por INTEGER NOT NULL,
    fecha_confirmacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (contratacion_id) REFERENCES contrataciones(id),
    FOREIGN KEY (postulacion_id) REFERENCES postulaciones(id),
    FOREIGN KEY (confirmado_por) REFERENCES usuarios(id)
);

CREATE TABLE IF NOT EXISTS integraciones_hv (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    postulacion_id INTEGER NOT NULL,
    sistema_destino VARCHAR(100) NOT NULL DEFAULT 'Sistema_Colaboradores_CECAR',
    estado VARCHAR(30) NOT NULL DEFAULT 'exitosa', -- exitosa, fallida, reintentando
    codigo_respuesta VARCHAR(50) DEFAULT '200_OK',
    payload_transferido TEXT,
    fecha_transferencia DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    reintentos INTEGER NOT NULL DEFAULT 0,
    FOREIGN KEY (postulacion_id) REFERENCES postulaciones(id)
);

CREATE TABLE IF NOT EXISTS planes_trabajo (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    convocatoria_id INTEGER NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    responsable_id INTEGER NOT NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'en_curso', -- planificado, en_curso, completado
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    FOREIGN KEY (convocatoria_id) REFERENCES convocatorias(id) ON DELETE CASCADE,
    FOREIGN KEY (responsable_id) REFERENCES usuarios(id)
);

CREATE TABLE IF NOT EXISTS actividades_plan (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    plan_id INTEGER NOT NULL,
    actividad VARCHAR(200) NOT NULL,
    responsable_id INTEGER NOT NULL,
    fecha_limite DATE NOT NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'pendiente', -- pendiente, en_progreso, completada
    observaciones TEXT,
    FOREIGN KEY (plan_id) REFERENCES planes_trabajo(id) ON DELETE CASCADE,
    FOREIGN KEY (responsable_id) REFERENCES usuarios(id)
);

CREATE TABLE IF NOT EXISTS tickets_ayuda (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    usuario_id INTEGER NOT NULL,
    categoria VARCHAR(50) NOT NULL DEFAULT 'soporte_postulacion',
    asunto VARCHAR(200) NOT NULL,
    mensaje TEXT NOT NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'abierto', -- abierto, en_atencion, resuelto, cerrado
    respuesta TEXT,
    respondido_por INTEGER,
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_respuesta DATETIME,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (respondido_por) REFERENCES usuarios(id)
);

CREATE TABLE IF NOT EXISTS notificaciones (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    usuario_id INTEGER NOT NULL,
    postulacion_id INTEGER,
    canal VARCHAR(20) NOT NULL DEFAULT 'portal', -- portal, correo
    asunto VARCHAR(200) NOT NULL,
    mensaje TEXT NOT NULL,
    leida INTEGER NOT NULL DEFAULT 0,
    fecha_envio DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (postulacion_id) REFERENCES postulaciones(id)
);

CREATE TABLE IF NOT EXISTS movimientos_historial (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    postulacion_id INTEGER NOT NULL,
    usuario_id INTEGER NOT NULL,
    accion VARCHAR(100) NOT NULL,
    estado_anterior VARCHAR(50),
    estado_nuevo VARCHAR(50),
    detalle TEXT,
    es_interno INTEGER NOT NULL DEFAULT 0,
    fecha_hora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (postulacion_id) REFERENCES postulaciones(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE IF NOT EXISTS auditoria (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    usuario_id INTEGER,
    accion VARCHAR(100) NOT NULL,
    entidad VARCHAR(50) NOT NULL,
    entidad_id VARCHAR(50),
    datos_antes TEXT,
    datos_despues TEXT,
    ip VARCHAR(50),
    user_agent VARCHAR(255),
    fecha_hora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS catalogos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    grupo VARCHAR(50) NOT NULL,
    codigo VARCHAR(50) NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    activo INTEGER NOT NULL DEFAULT 1,
    orden INTEGER NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS parametros_sistema (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    clave VARCHAR(50) NOT NULL UNIQUE,
    valor TEXT NOT NULL,
    descripcion TEXT,
    fecha_actualizacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

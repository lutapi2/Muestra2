-- =============================================================================
-- SISTEMA DE SELECCIÓN DE PERSONAL - CECAR
-- Base de Datos Relacional para MySQL 8.0+
-- Motor: InnoDB | Juego de Caracteres: utf8mb4 | Colación: utf8mb4_unicode_ci
-- Especificación de Requerimientos Versión 3.0
-- =============================================================================

DROP DATABASE IF EXISTS seleccion_personal_cecar;

CREATE DATABASE seleccion_personal_cecar
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE seleccion_personal_cecar;

SET time_zone = '-05:00';

-- =============================================================================
-- 1. TABLAS DE ADMINISTRACIÓN, CATÁLOGOS Y ESTRUCTURA INSTITUCIONAL (CECAR)
-- =============================================================================

-- RF-034, RF-051, RF-080, RF-082, RNF-014
CREATE TABLE parametro_sistema (
  id_parametro        SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  clave               VARCHAR(60)  NOT NULL,
  valor               VARCHAR(255) NOT NULL,
  tipo                ENUM('ENTERO','TEXTO','BOOLEANO','FECHA','DECIMAL') NOT NULL DEFAULT 'TEXTO',
  descripcion         VARCHAR(255) NULL,
  fecha_vigencia      DATE NOT NULL,
  id_actualizado_por  INT UNSIGNED NULL,
  actualizado_en      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id_parametro),
  UNIQUE KEY uq_parametro_clave_vigencia (clave, fecha_vigencia)
) ENGINE=InnoDB COMMENT='Configuraciones globales: tamaños máximos de archivos, plazos de subsanación, etc.';

-- RF-016, RF-080, HV-002
CREATE TABLE tipo_documento (
  id_tipo_documento   TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  codigo              VARCHAR(5)   NOT NULL,
  nombre              VARCHAR(60)  NOT NULL,
  activo              BOOLEAN NOT NULL DEFAULT TRUE,
  PRIMARY KEY (id_tipo_documento),
  UNIQUE KEY uq_tipo_documento_codigo (codigo)
) ENGINE=InnoDB COMMENT='Catálogo de documentos de identidad (CC, CE, PA, etc.)';

-- RF-006, RF-085
CREATE TABLE dependencia (
  id_dependencia      SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre              VARCHAR(120) NOT NULL,
  sigla               VARCHAR(15)  NULL,
  estado              ENUM('ACTIVA','INACTIVA') NOT NULL DEFAULT 'ACTIVA',
  PRIMARY KEY (id_dependencia),
  UNIQUE KEY uq_dependencia_nombre (nombre),
  UNIQUE KEY uq_dependencia_sigla (sigla)
) ENGINE=InnoDB COMMENT='Áreas o unidades administrativas de CECAR';

-- RF-097: Filtros institucionales ampliados
CREATE TABLE facultad (
  id_facultad         SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre              VARCHAR(120) NOT NULL,
  codigo              VARCHAR(20)  NULL,
  activo              BOOLEAN NOT NULL DEFAULT TRUE,
  PRIMARY KEY (id_facultad),
  UNIQUE KEY uq_facultad_nombre (nombre)
) ENGINE=InnoDB COMMENT='Facultades de CECAR para segmentación de convocatorias y reportes';

-- RF-097
CREATE TABLE programa_academico (
  id_programa         SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_facultad         SMALLINT UNSIGNED NOT NULL,
  nombre              VARCHAR(120) NOT NULL,
  codigo              VARCHAR(20)  NULL,
  activo              BOOLEAN NOT NULL DEFAULT TRUE,
  PRIMARY KEY (id_programa),
  UNIQUE KEY uq_programa_facultad (id_facultad, nombre),
  CONSTRAINT fk_prog_facultad FOREIGN KEY (id_facultad) REFERENCES facultad (id_facultad)
) ENGINE=InnoDB COMMENT='Programas académicos asociados a facultades';

-- RF-097
CREATE TABLE sede (
  id_sede             TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre              VARCHAR(80)  NOT NULL,
  ciudad              VARCHAR(80)  NOT NULL,
  activo              BOOLEAN NOT NULL DEFAULT TRUE,
  PRIMARY KEY (id_sede),
  UNIQUE KEY uq_sede_nombre (nombre)
) ENGINE=InnoDB COMMENT='Sedes físicas de CECAR (Sincelejo, Montería, etc.)';

-- RF-097
CREATE TABLE periodo_academico (
  id_periodo          SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  codigo              VARCHAR(15)  NOT NULL COMMENT 'Ej: 2026-1, 2026-2',
  nombre              VARCHAR(60)  NOT NULL,
  fecha_inicio        DATE NOT NULL,
  fecha_fin           DATE NOT NULL,
  activo              BOOLEAN NOT NULL DEFAULT TRUE,
  PRIMARY KEY (id_periodo),
  UNIQUE KEY uq_periodo_codigo (codigo),
  CONSTRAINT ck_periodo_fechas CHECK (fecha_fin >= fecha_inicio)
) ENGINE=InnoDB COMMENT='Periodos semestrales para reporte y filtrado';

-- RF-006, RF-080
CREATE TABLE modalidad_contratacion (
  id_modalidad_contratacion TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre              VARCHAR(60)  NOT NULL,
  activo              BOOLEAN NOT NULL DEFAULT TRUE,
  PRIMARY KEY (id_modalidad_contratacion),
  UNIQUE KEY uq_modalidad_nombre (nombre)
) ENGINE=InnoDB COMMENT='Modalidad contractual (Tiempo Completo, Medio Tiempo, Cátedra, etc.)';

-- RF-047, RF-058, RF-093, RN-024
CREATE TABLE motivo (
  id_motivo           SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  tipo                ENUM('REVISION','DESCARTE','RETIRO','REPROGRAMACION') NOT NULL,
  codigo              VARCHAR(30)  NOT NULL,
  nombre              VARCHAR(120) NOT NULL,
  mensaje_candidato   VARCHAR(255) NULL COMMENT 'Mensaje externo visible para el aspirante (RN-019)',
  activo              BOOLEAN NOT NULL DEFAULT TRUE,
  PRIMARY KEY (id_motivo),
  UNIQUE KEY uq_motivo_tipo_codigo (tipo, codigo)
) ENGINE=InnoDB COMMENT='Catálogo estandarizado de causales y motivos';

-- RF-080
CREATE TABLE catalogo (
  id_catalogo         SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  tipo                ENUM('NIVEL_EDUCATIVO','COMPETENCIA','NIVEL_IDIOMA') NOT NULL,
  codigo              VARCHAR(40)  NOT NULL,
  nombre              VARCHAR(120) NOT NULL,
  activo              BOOLEAN NOT NULL DEFAULT TRUE,
  PRIMARY KEY (id_catalogo),
  UNIQUE KEY uq_catalogo_tipo_codigo (tipo, codigo)
) ENGINE=InnoDB COMMENT='Catálogos auxiliares tipificados';

-- =============================================================================
-- 2. SEGURIDAD, USUARIOS Y CONTROL DE ACCESO (RBAC)
-- =============================================================================

-- RF-004, RF-005
CREATE TABLE rol (
  id_rol              TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre              VARCHAR(40)  NOT NULL,
  descripcion         VARCHAR(200) NULL,
  estado              ENUM('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO',
  PRIMARY KEY (id_rol),
  UNIQUE KEY uq_rol_nombre (nombre)
) ENGINE=InnoDB COMMENT='Roles del sistema: Administrador, Talento Humano, Revisor, Entrevistador, Candidato, etc.';

-- RF-004
CREATE TABLE permiso (
  id_permiso          SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  codigo              VARCHAR(60)  NOT NULL,
  modulo              VARCHAR(40)  NOT NULL,
  descripcion         VARCHAR(200) NULL,
  PRIMARY KEY (id_permiso),
  UNIQUE KEY uq_permiso_codigo (codigo)
) ENGINE=InnoDB COMMENT='Permisos atómicos validados en el servidor PHP (RNF-010)';

-- RF-001, RF-002, RF-003, RF-005, RNF-009
CREATE TABLE usuario (
  id_usuario          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  correo              VARCHAR(150) NOT NULL,
  contrasena_hash     VARCHAR(255) NOT NULL,
  tipo                ENUM('CANDIDATO','INTERNO') NOT NULL,
  nombre_completo     VARCHAR(150) NOT NULL,
  id_dependencia      SMALLINT UNSIGNED NULL,
  estado              ENUM('ACTIVO','INACTIVO','BLOQUEADO') NOT NULL DEFAULT 'ACTIVO',
  ultimo_acceso       DATETIME NULL,
  creado_en           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_usuario),
  UNIQUE KEY uq_usuario_correo (correo),
  CONSTRAINT fk_usuario_dependencia FOREIGN KEY (id_dependencia) REFERENCES dependencia (id_dependencia),
  CONSTRAINT ck_usuario_correo CHECK (correo LIKE '_%@_%.__%'),
  CONSTRAINT ck_usuario_hash CHECK (CHAR_LENGTH(contrasena_hash) >= 60 AND LEFT(contrasena_hash,1) = '$')
) ENGINE=InnoDB COMMENT='Cuentas de usuario. Contraseña siempre con hash seguro (RNF-009)';

-- RF-004, RF-005
CREATE TABLE usuario_rol (
  id_usuario          INT UNSIGNED NOT NULL,
  id_rol              TINYINT UNSIGNED NOT NULL,
  asignado_en         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  asignado_por        INT UNSIGNED NULL,
  PRIMARY KEY (id_usuario, id_rol),
  CONSTRAINT fk_ur_usuario FOREIGN KEY (id_usuario)   REFERENCES usuario (id_usuario) ON DELETE CASCADE,
  CONSTRAINT fk_ur_rol     FOREIGN KEY (id_rol)       REFERENCES rol (id_rol),
  CONSTRAINT fk_ur_asigna  FOREIGN KEY (asignado_por) REFERENCES usuario (id_usuario)
) ENGINE=InnoDB;

-- RF-004
CREATE TABLE rol_permiso (
  id_rol              TINYINT UNSIGNED NOT NULL,
  id_permiso          SMALLINT UNSIGNED NOT NULL,
  PRIMARY KEY (id_rol, id_permiso),
  CONSTRAINT fk_rp_rol     FOREIGN KEY (id_rol)     REFERENCES rol (id_rol) ON DELETE CASCADE,
  CONSTRAINT fk_rp_permiso FOREIGN KEY (id_permiso) REFERENCES permiso (id_permiso) ON DELETE CASCADE
) ENGINE=InnoDB;

-- RF-002, RNF-012
CREATE TABLE sesion (
  id_sesion           CHAR(64) NOT NULL,
  id_usuario          INT UNSIGNED NOT NULL,
  creada_en           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  expira_en           DATETIME NOT NULL,
  cerrada_en          DATETIME NULL,
  ip                  VARCHAR(45) NULL,
  PRIMARY KEY (id_sesion),
  KEY ix_sesion_usuario (id_usuario),
  CONSTRAINT fk_sesion_usuario FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario),
  CONSTRAINT ck_sesion_expira CHECK (expira_en > creada_en)
) ENGINE=InnoDB COMMENT='Gestión segura de sesiones de usuario con invalidación controlada';

-- =============================================================================
-- 3. DATOS DE CANDIDATO Y CONSENTIMIENTOS
-- =============================================================================

-- RF-001, RF-016, RF-017, HV-002, HV-003, HV-004
CREATE TABLE candidato (
  id_candidato        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_usuario          INT UNSIGNED NOT NULL,
  nombres             VARCHAR(80)  NOT NULL,
  apellidos           VARCHAR(80)  NOT NULL,
  id_tipo_documento   TINYINT UNSIGNED NOT NULL,
  numero_documento    VARCHAR(20)  NOT NULL,
  telefono            VARCHAR(20)  NOT NULL,
  ciudad              VARCHAR(80)  NOT NULL,
  direccion           VARCHAR(150) NULL,
  creado_en           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_candidato),
  UNIQUE KEY uq_candidato_usuario (id_usuario),
  UNIQUE KEY uq_candidato_documento (id_tipo_documento, numero_documento),
  CONSTRAINT fk_cand_usuario FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario),
  CONSTRAINT fk_cand_tipodoc FOREIGN KEY (id_tipo_documento) REFERENCES tipo_documento (id_tipo_documento)
) ENGINE=InnoDB COMMENT='Datos de filiación del candidato';

-- RF-029, HV-020
CREATE TABLE referencia (
  id_referencia       INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_candidato        INT UNSIGNED NOT NULL,
  nombre              VARCHAR(120) NOT NULL,
  relacion            VARCHAR(60)  NOT NULL,
  entidad             VARCHAR(120) NULL,
  telefono            VARCHAR(20)  NOT NULL,
  correo              VARCHAR(150) NULL,
  autoriza_contacto   BOOLEAN NOT NULL DEFAULT FALSE,
  PRIMARY KEY (id_referencia),
  KEY ix_ref_candidato (id_candidato),
  CONSTRAINT fk_ref_candidato FOREIGN KEY (id_candidato) REFERENCES candidato (id_candidato) ON DELETE CASCADE
) ENGINE=InnoDB COMMENT='Referencias personales y laborales declaradas';

-- =============================================================================
-- 4. CONVOCATORIAS, PERFILES Y REQUISITOS DOCUMENTALES
-- =============================================================================

-- RF-006, RF-011, RF-012, RF-097, RN-002, RN-003
CREATE TABLE convocatoria (
  id_convocatoria     INT UNSIGNED NOT NULL AUTO_INCREMENT,
  codigo              VARCHAR(20)  NOT NULL,
  cargo               VARCHAR(120) NOT NULL,
  id_dependencia      SMALLINT UNSIGNED NOT NULL,
  id_facultad         SMALLINT UNSIGNED NULL,
  id_programa         SMALLINT UNSIGNED NULL,
  id_sede             TINYINT UNSIGNED NULL,
  id_periodo          SMALLINT UNSIGNED NULL,
  vacantes            SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  id_modalidad_contratacion TINYINT UNSIGNED NOT NULL,
  descripcion         TEXT NULL,
  fecha_apertura      DATETIME NOT NULL,
  fecha_cierre        DATETIME NOT NULL,
  estado              ENUM('BORRADOR','PUBLICADA','CERRADA','RETIRADA') NOT NULL DEFAULT 'BORRADOR',
  fecha_publicacion   DATETIME NULL,
  motivo_retiro       VARCHAR(255) NULL,
  id_creador          INT UNSIGNED NOT NULL,
  creado_en           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_convocatoria),
  UNIQUE KEY uq_convocatoria_codigo (codigo),
  KEY ix_conv_estado_fechas (estado, fecha_apertura, fecha_cierre),
  CONSTRAINT fk_conv_dependencia FOREIGN KEY (id_dependencia) REFERENCES dependencia (id_dependencia),
  CONSTRAINT fk_conv_facultad    FOREIGN KEY (id_facultad)    REFERENCES facultad (id_facultad),
  CONSTRAINT fk_conv_programa    FOREIGN KEY (id_programa)    REFERENCES programa_academico (id_programa),
  CONSTRAINT fk_conv_sede        FOREIGN KEY (id_sede)        REFERENCES sede (id_sede),
  CONSTRAINT fk_conv_periodo     FOREIGN KEY (id_periodo)     REFERENCES periodo_academico (id_periodo),
  CONSTRAINT fk_conv_modalidad   FOREIGN KEY (id_modalidad_contratacion) REFERENCES modalidad_contratacion (id_modalidad_contratacion),
  CONSTRAINT fk_conv_creador     FOREIGN KEY (id_creador)     REFERENCES usuario (id_usuario),
  CONSTRAINT ck_conv_vacantes    CHECK (vacantes > 0),
  CONSTRAINT ck_conv_fechas      CHECK (fecha_cierre > fecha_apertura),
  CONSTRAINT ck_conv_publicada   CHECK (estado = 'BORRADOR' OR fecha_publicacion IS NOT NULL),
  CONSTRAINT ck_conv_retiro      CHECK (estado <> 'RETIRADA' OR motivo_retiro IS NOT NULL)
) ENGINE=InnoDB COMMENT='Convocatorias públicas o internas de selección';

-- RF-007, RN-014, RNF-017
CREATE TABLE perfil_cargo (
  id_perfil            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_convocatoria      INT UNSIGNED NOT NULL,
  formacion_requerida  TEXT NOT NULL,
  experiencia_requerida TEXT NOT NULL,
  competencias         TEXT NULL,
  funciones            TEXT NOT NULL,
  condiciones          TEXT NULL,
  PRIMARY KEY (id_perfil),
  UNIQUE KEY uq_perfil_convocatoria (id_convocatoria),
  CONSTRAINT fk_perfil_convocatoria FOREIGN KEY (id_convocatoria) REFERENCES convocatoria (id_convocatoria) ON DELETE CASCADE
) ENGINE=InnoDB COMMENT='Requisitos y funciones del perfil asociado 1:1 con la convocatoria';

-- RF-008, RF-080, HV-001 a HV-026
CREATE TABLE tipo_item_hv (
  id_tipo_item        SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  codigo              VARCHAR(10)  NOT NULL COMMENT 'HV-001 al HV-026 del catálogo',
  seccion             VARCHAR(50)  NOT NULL,
  nombre              VARCHAR(100) NOT NULL,
  campos_minimos      VARCHAR(255) NOT NULL,
  soporte_esperado    VARCHAR(200) NULL,
  obligatoriedad_base ENUM('OBLIGATORIO','OPCIONAL','CONDICIONAL') NOT NULL,
  repetible           BOOLEAN NOT NULL DEFAULT TRUE,
  almacenamiento      ENUM('ITEM_HV','CONSENTIMIENTO','REFERENCIA') NOT NULL DEFAULT 'ITEM_HV',
  activo              BOOLEAN NOT NULL DEFAULT TRUE,
  PRIMARY KEY (id_tipo_item),
  UNIQUE KEY uq_tipo_item_codigo (codigo)
) ENGINE=InnoDB COMMENT='Catálogo formal de secciones y campos dinámicos de la hoja de vida';

-- RF-008, RF-033, RN-007
CREATE TABLE requisito_convocatoria (
  id_requisito        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_convocatoria     INT UNSIGNED NOT NULL,
  id_tipo_item        SMALLINT UNSIGNED NOT NULL,
  obligatoriedad      ENUM('OBLIGATORIO','OPCIONAL','CONDICIONAL') NOT NULL,
  soporte_requerido   ENUM('OBLIGATORIO','OPCIONAL','CONDICIONAL','NO_APLICA') NOT NULL,
  regla               VARCHAR(255) NULL,
  PRIMARY KEY (id_requisito),
  UNIQUE KEY uq_requisito_conv_item (id_convocatoria, id_tipo_item),
  CONSTRAINT fk_req_convocatoria FOREIGN KEY (id_convocatoria) REFERENCES convocatoria (id_convocatoria) ON DELETE CASCADE,
  CONSTRAINT fk_req_tipo_item    FOREIGN KEY (id_tipo_item)    REFERENCES tipo_item_hv (id_tipo_item),
  CONSTRAINT ck_req_regla CHECK ((obligatoriedad <> 'CONDICIONAL' AND soporte_requerido <> 'CONDICIONAL') OR regla IS NOT NULL)
) ENGINE=InnoDB COMMENT='Configuración de ítems y obligatoriedad por convocatoria';

-- RF-010, RF-060
CREATE TABLE etapa_proceso (
  id_etapa            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_convocatoria     INT UNSIGNED NOT NULL,
  nombre              VARCHAR(80)  NOT NULL,
  tipo                ENUM('REVISION_DOCUMENTAL','PRUEBAS','PRESELECCION','ENTREVISTA','DECISION_FINAL') NOT NULL,
  orden               TINYINT UNSIGNED NOT NULL,
  obligatoria         BOOLEAN NOT NULL DEFAULT TRUE,
  estado              ENUM('ACTIVA','INACTIVA') NOT NULL DEFAULT 'ACTIVA',
  PRIMARY KEY (id_etapa),
  UNIQUE KEY uq_etapa_conv_orden  (id_convocatoria, orden),
  UNIQUE KEY uq_etapa_conv_nombre (id_convocatoria, nombre),
  CONSTRAINT fk_etapa_convocatoria FOREIGN KEY (id_convocatoria) REFERENCES convocatoria (id_convocatoria) ON DELETE CASCADE,
  CONSTRAINT ck_etapa_orden CHECK (orden > 0)
) ENGINE=InnoDB COMMENT='Etapas secuenciales del proceso de selección';

-- RF-009, RF-054, RF-055, RN-013
CREATE TABLE criterio_evaluacion (
  id_criterio         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_convocatoria     INT UNSIGNED NOT NULL,
  id_etapa            INT UNSIGNED NULL,
  nombre              VARCHAR(120) NOT NULL,
  descripcion         VARCHAR(300) NULL,
  puntaje_maximo      DECIMAL(5,2) NOT NULL,
  ponderacion         DECIMAL(5,2) NOT NULL,
  obligatorio         BOOLEAN NOT NULL DEFAULT TRUE,
  bloqueado           BOOLEAN NOT NULL DEFAULT FALSE,
  PRIMARY KEY (id_criterio),
  UNIQUE KEY uq_criterio_conv_nombre (id_convocatoria, nombre),
  CONSTRAINT fk_crit_convocatoria FOREIGN KEY (id_convocatoria) REFERENCES convocatoria (id_convocatoria) ON DELETE CASCADE,
  CONSTRAINT fk_crit_etapa        FOREIGN KEY (id_etapa)        REFERENCES etapa_proceso (id_etapa),
  CONSTRAINT ck_crit_puntaje      CHECK (puntaje_maximo > 0),
  CONSTRAINT ck_crit_ponderacion  CHECK (ponderacion >= 0 AND ponderacion <= 100)
) ENGINE=InnoDB COMMENT='Rúbricas y ponderaciones bloqueadas una vez iniciada la evaluación';

-- =============================================================================
-- 5. POSTULACIONES, HOJA DE VIDA POR ÍTEMS Y ARCHIVOS DE SOPORTE
-- =============================================================================

-- RF-014, RF-038, RF-039, RF-041, RF-042, RN-001, RN-008
CREATE TABLE postulacion (
  id_postulacion      INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_candidato        INT UNSIGNED NOT NULL,
  id_convocatoria     INT UNSIGNED NOT NULL,
  id_etapa_actual     INT UNSIGNED NULL,
  estado              ENUM('BORRADOR','ENVIADA','EN_REVISION','SUBSANACION_REQUERIDA','HABILITADA',
                           'PRESELECCIONADA','ENTREVISTADA','ELEGIBLE','SELECCIONADA','NO_SELECCIONADA',
                           'NO_HABILITADA','NO_CONTINUA','RETIRADA','VINCULADO') NOT NULL DEFAULT 'BORRADOR',
  resultado_documental ENUM('PENDIENTE','CONFORME','OBSERVADO','NO_CONFORME') NOT NULL DEFAULT 'PENDIENTE',
  version             SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  fecha_inicio        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  fecha_envio         DATETIME NULL,
  PRIMARY KEY (id_postulacion),
  UNIQUE KEY uq_postulacion_candidato_convocatoria (id_candidato, id_convocatoria),
  KEY ix_post_estado (estado),
  KEY ix_post_convocatoria (id_convocatoria),
  CONSTRAINT fk_post_candidato    FOREIGN KEY (id_candidato)    REFERENCES candidato (id_candidato),
  CONSTRAINT fk_post_convocatoria FOREIGN KEY (id_convocatoria) REFERENCES convocatoria (id_convocatoria),
  CONSTRAINT fk_post_etapa        FOREIGN KEY (id_etapa_actual) REFERENCES etapa_proceso (id_etapa),
  CONSTRAINT ck_post_envio CHECK (estado IN ('BORRADOR','RETIRADA') OR fecha_envio IS NOT NULL),
  CONSTRAINT ck_post_fechas CHECK (fecha_envio IS NULL OR fecha_envio >= fecha_inicio)
) ENGINE=InnoDB COMMENT='Expediente de postulación de un candidato a una vacante';

-- RF-015, RF-076, RN-004, RN-021, RNF-015
CREATE TABLE consentimiento (
  id_consentimiento   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_candidato        INT UNSIGNED NOT NULL,
  id_postulacion      INT UNSIGNED NULL,
  finalidad           ENUM('TRATAMIENTO_DATOS','VERACIDAD','BANCO_TALENTO') NOT NULL,
  version_texto       VARCHAR(20)  NOT NULL,
  aceptado            BOOLEAN NOT NULL,
  fecha_hora          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ip                  VARCHAR(45)  NULL,
  revocado_en         DATETIME NULL,
  PRIMARY KEY (id_consentimiento),
  KEY ix_consent_candidato (id_candidato, finalidad),
  CONSTRAINT fk_consent_candidato   FOREIGN KEY (id_candidato)   REFERENCES candidato (id_candidato),
  CONSTRAINT fk_consent_postulacion FOREIGN KEY (id_postulacion) REFERENCES postulacion (id_postulacion)
) ENGINE=InnoDB COMMENT='Evidencia inmutable de consentimiento legal (Habeas Data)';

-- RF-076, RN-021
CREATE TABLE banco_talento (
  id_banco            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_candidato        INT UNSIGNED NOT NULL,
  id_consentimiento   INT UNSIGNED NOT NULL,
  fecha_ingreso       DATE NOT NULL,
  vigencia_hasta      DATE NOT NULL,
  estado              ENUM('VIGENTE','REVOCADO','VENCIDO') NOT NULL DEFAULT 'VIGENTE',
  PRIMARY KEY (id_banco),
  UNIQUE KEY uq_banco_consentimiento (id_consentimiento),
  CONSTRAINT fk_banco_candidato FOREIGN KEY (id_candidato)      REFERENCES candidato (id_candidato),
  CONSTRAINT fk_banco_consent   FOREIGN KEY (id_consentimiento) REFERENCES consentimiento (id_consentimiento),
  CONSTRAINT ck_banco_vigencia  CHECK (vigencia_hasta > fecha_ingreso)
) ENGINE=InnoDB COMMENT='Candidatos elegibles conservados para futuras convocatorias';

-- RF-016 a RF-031, RN-005
CREATE TABLE item_hv (
  id_item_hv          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_postulacion      INT UNSIGNED NOT NULL,
  id_tipo_item        SMALLINT UNSIGNED NOT NULL,
  estado_revision     ENUM('PENDIENTE','VERIFICADO','OBSERVADO','RECHAZADO') NOT NULL DEFAULT 'PENDIENTE',
  fecha_registro      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_item_hv),
  KEY ix_item_postulacion (id_postulacion, id_tipo_item),
  CONSTRAINT fk_item_postulacion FOREIGN KEY (id_postulacion) REFERENCES postulacion (id_postulacion) ON DELETE CASCADE,
  CONSTRAINT fk_item_tipo        FOREIGN KEY (id_tipo_item)   REFERENCES tipo_item_hv (id_tipo_item)
) ENGINE=InnoDB COMMENT='Registro individual de estudio, experiencia o dato de HV';

-- RF-031, RF-049, RF-050, RF-052, RN-009
CREATE TABLE version_item (
  id_version_item     INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_item_hv          INT UNSIGNED NOT NULL,
  numero_version      SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  datos               JSON NOT NULL,
  creado_en           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_version_item),
  UNIQUE KEY uq_version_item_num (id_item_hv, numero_version),
  CONSTRAINT fk_version_item FOREIGN KEY (id_item_hv) REFERENCES item_hv (id_item_hv) ON DELETE CASCADE,
  CONSTRAINT ck_version_num CHECK (numero_version >= 1)
) ENGINE=InnoDB COMMENT='Historial de versiones del dato para auditoría y subsanación';

-- RF-032, RF-034, RF-035, RN-006, RNF-013, RNF-014
CREATE TABLE soporte (
  id_soporte          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_version_item     INT UNSIGNED NOT NULL,
  nombre_original     VARCHAR(255) NOT NULL,
  nombre_almacenado   VARCHAR(100) NOT NULL,
  mime                VARCHAR(100) NOT NULL,
  tamano_bytes        INT UNSIGNED NOT NULL,
  hash_sha256         CHAR(64) NOT NULL,
  ruta_segura         VARCHAR(255) NOT NULL COMMENT 'Almacenado fuera del webroot',
  estado_archivo      ENUM('VALIDO','EN_CUARENTENA','RECHAZADO') NOT NULL DEFAULT 'VALIDO',
  fecha_carga         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_soporte),
  UNIQUE KEY uq_soporte_almacenado (nombre_almacenado),
  KEY ix_soporte_version (id_version_item),
  CONSTRAINT fk_soporte_version FOREIGN KEY (id_version_item) REFERENCES version_item (id_version_item) ON DELETE CASCADE,
  CONSTRAINT ck_soporte_tamano CHECK (tamano_bytes > 0),
  CONSTRAINT ck_soporte_hash   CHECK (hash_sha256 REGEXP '^[0-9a-f]{64}$')
) ENGINE=InnoDB COMMENT='Archivos PDF o evidencias vinculados 1:1 o 1:N con la versión del ítem';

-- =============================================================================
-- 6. REVISIÓN DOCUMENTAL Y SUBSANACIÓN
-- =============================================================================

-- RF-045, RF-046, RF-047, RF-052, RN-010
CREATE TABLE revision_item (
  id_revision         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_version_item     INT UNSIGNED NOT NULL,
  id_revisor          INT UNSIGNED NOT NULL,
  resultado           ENUM('APROBADO','OBSERVADO','RECHAZADO') NOT NULL,
  id_motivo           SMALLINT UNSIGNED NULL,
  observacion         VARCHAR(500) NULL,
  fecha_revision      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_revision),
  KEY ix_revision_version (id_version_item, fecha_revision),
  CONSTRAINT fk_rev_version FOREIGN KEY (id_version_item) REFERENCES version_item (id_version_item),
  CONSTRAINT fk_rev_revisor FOREIGN KEY (id_revisor)      REFERENCES usuario (id_usuario),
  CONSTRAINT fk_rev_motivo  FOREIGN KEY (id_motivo)       REFERENCES motivo (id_motivo),
  CONSTRAINT ck_rev_motivo  CHECK (resultado = 'APROBADO' OR observacion IS NOT NULL)
) ENGINE=InnoDB COMMENT='Dictamen del revisor sobre un ítem y su soporte';

-- RF-048, RF-051, RN-011
CREATE TABLE solicitud_subsanacion (
  id_solicitud        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_postulacion      INT UNSIGNED NOT NULL,
  id_solicitante      INT UNSIGNED NOT NULL,
  fecha_solicitud     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  fecha_limite        DATETIME NOT NULL,
  fecha_extension     DATETIME NULL,
  estado              ENUM('VIGENTE','RESPONDIDA','VENCIDA') NOT NULL DEFAULT 'VIGENTE',
  fecha_respuesta     DATETIME NULL,
  PRIMARY KEY (id_solicitud),
  KEY ix_subs_postulacion (id_postulacion),
  CONSTRAINT fk_subs_postulacion FOREIGN KEY (id_postulacion) REFERENCES postulacion (id_postulacion),
  CONSTRAINT fk_subs_solicitante FOREIGN KEY (id_solicitante) REFERENCES usuario (id_usuario),
  CONSTRAINT ck_subs_limite    CHECK (fecha_limite > fecha_solicitud),
  CONSTRAINT ck_subs_extension CHECK (fecha_extension IS NULL OR fecha_extension > fecha_limite)
) ENGINE=InnoDB COMMENT='Ventana de subsanación con fecha de vencimiento';

-- RF-048, RF-049, RN-011
CREATE TABLE detalle_subsanacion (
  id_detalle           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_solicitud         INT UNSIGNED NOT NULL,
  id_item_hv           INT UNSIGNED NOT NULL,
  id_revision          INT UNSIGNED NULL,
  motivo               VARCHAR(300) NOT NULL,
  estado               ENUM('PENDIENTE','CORREGIDO') NOT NULL DEFAULT 'PENDIENTE',
  id_version_corregida INT UNSIGNED NULL,
  PRIMARY KEY (id_detalle),
  UNIQUE KEY uq_detalle_sol_item (id_solicitud, id_item_hv),
  CONSTRAINT fk_det_solicitud FOREIGN KEY (id_solicitud) REFERENCES solicitud_subsanacion (id_solicitud) ON DELETE CASCADE,
  CONSTRAINT fk_det_item      FOREIGN KEY (id_item_hv)   REFERENCES item_hv (id_item_hv),
  CONSTRAINT fk_det_revision  FOREIGN KEY (id_revision)  REFERENCES revision_item (id_revision),
  CONSTRAINT fk_det_version   FOREIGN KEY (id_version_corregida) REFERENCES version_item (id_version_item)
) ENGINE=InnoDB COMMENT='Ítems exactos que el candidato tiene autorización de corregir';

-- =============================================================================
-- 7. EVALUACIÓN DE PERFIL, PRUEBAS Y COMITÉS DE ENTREVISTAS
-- =============================================================================

-- RF-054, RF-055, RN-013, RN-014
CREATE TABLE evaluacion (
  id_evaluacion       INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_postulacion      INT UNSIGNED NOT NULL,
  id_criterio         INT UNSIGNED NOT NULL,
  id_evaluador        INT UNSIGNED NOT NULL,
  resultado           ENUM('CUMPLE','NO_CUMPLE','PUNTUADO') NOT NULL,
  puntaje             DECIMAL(5,2) NULL,
  evidencia           VARCHAR(255) NULL,
  observacion         VARCHAR(500) NULL,
  fecha_evaluacion    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_evaluacion),
  UNIQUE KEY uq_eval_post_crit_eval (id_postulacion, id_criterio, id_evaluador),
  CONSTRAINT fk_eval_postulacion FOREIGN KEY (id_postulacion) REFERENCES postulacion (id_postulacion),
  CONSTRAINT fk_eval_criterio    FOREIGN KEY (id_criterio)    REFERENCES criterio_evaluacion (id_criterio),
  CONSTRAINT fk_eval_evaluador   FOREIGN KEY (id_evaluador)   REFERENCES usuario (id_usuario),
  CONSTRAINT ck_eval_puntaje CHECK ((resultado <> 'PUNTUADO' OR puntaje IS NOT NULL) AND (puntaje IS NULL OR puntaje >= 0))
) ENGINE=InnoDB COMMENT='Calificaciones y conceptos emitidos por los evaluadores';

-- RF-087, RF-088: Pruebas psicotécnicas y de conocimiento
CREATE TABLE prueba (
  id_prueba           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_convocatoria     INT UNSIGNED NOT NULL,
  tipo                ENUM('PSICOTECNICA','CONOCIMIENTO') NOT NULL,
  nombre              VARCHAR(120) NOT NULL,
  descripcion         VARCHAR(300) NULL,
  fecha_aplicacion    DATETIME NULL,
  id_responsable      INT UNSIGNED NOT NULL,
  PRIMARY KEY (id_prueba),
  KEY ix_prueba_convocatoria (id_convocatoria),
  CONSTRAINT fk_prueba_convocatoria FOREIGN KEY (id_convocatoria) REFERENCES convocatoria (id_convocatoria),
  CONSTRAINT fk_prueba_responsable  FOREIGN KEY (id_responsable)  REFERENCES usuario (id_usuario)
) ENGINE=InnoDB COMMENT='Configuración de pruebas psicotécnicas y de conocimiento';

-- RF-087, RF-088
CREATE TABLE resultado_prueba (
  id_resultado_prueba INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_prueba           INT UNSIGNED NOT NULL,
  id_postulacion      INT UNSIGNED NOT NULL,
  calificacion        DECIMAL(5,2) NULL,
  dictamen            ENUM('APROBADO','NO_APROBADO','PENDIENTE') NOT NULL DEFAULT 'PENDIENTE',
  observacion         TEXT NULL,
  soporte_evidencia   VARCHAR(255) NULL COMMENT 'Ruta a documento o informe de la prueba',
  fecha_registro      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_resultado_prueba),
  UNIQUE KEY uq_resultado_prueba_post (id_prueba, id_postulacion),
  CONSTRAINT fk_rp_prueba      FOREIGN KEY (id_prueba)      REFERENCES prueba (id_prueba),
  CONSTRAINT fk_rp_postulacion FOREIGN KEY (id_postulacion) REFERENCES postulacion (id_postulacion)
) ENGINE=InnoDB COMMENT='Resultados y dictámenes de pruebas por aspirante';

-- RF-089: Prueba de aula docente (Desarrollo Profesoral)
CREATE TABLE prueba_aula (
  id_prueba_aula      INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_postulacion      INT UNSIGNED NOT NULL,
  tema                VARCHAR(200) NOT NULL,
  rubrica             TEXT NOT NULL,
  id_evaluador        INT UNSIGNED NOT NULL,
  puntaje             DECIMAL(5,2) NOT NULL,
  dictamen            ENUM('APROBADA','NO_APROBADA') NOT NULL,
  observaciones       TEXT NULL,
  soporte_acta        VARCHAR(255) NULL,
  fecha_evaluacion    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_prueba_aula),
  UNIQUE KEY uq_prueba_aula_postulacion (id_postulacion),
  CONSTRAINT fk_pa_postulacion FOREIGN KEY (id_postulacion) REFERENCES postulacion (id_postulacion),
  CONSTRAINT fk_pa_evaluador   FOREIGN KEY (id_evaluador)   REFERENCES usuario (id_usuario),
  CONSTRAINT ck_pa_puntaje CHECK (puntaje >= 0 AND puntaje <= 100)
) ENGINE=InnoDB COMMENT='Evaluación de clase magistral para perfiles docentes';

-- RF-057, RN-012, RN-016
CREATE TABLE preseleccion (
  id_preseleccion     INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_postulacion      INT UNSIGNED NOT NULL,
  id_usuario          INT UNSIGNED NOT NULL,
  fecha               DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  fundamento          VARCHAR(500) NOT NULL,
  es_excepcion        BOOLEAN NOT NULL DEFAULT FALSE,
  estado              ENUM('VIGENTE','REVOCADA') NOT NULL DEFAULT 'VIGENTE',
  PRIMARY KEY (id_preseleccion),
  UNIQUE KEY uq_preseleccion_post (id_postulacion),
  CONSTRAINT fk_pres_postulacion FOREIGN KEY (id_postulacion) REFERENCES postulacion (id_postulacion),
  CONSTRAINT fk_pres_usuario     FOREIGN KEY (id_usuario)     REFERENCES usuario (id_usuario)
) ENGINE=InnoDB COMMENT='Autorización formal requerida antes de agendar entrevista';

-- RF-061, RN-017
CREATE TABLE franja_entrevista (
  id_franja           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_convocatoria     INT UNSIGNED NOT NULL,
  id_etapa            INT UNSIGNED NOT NULL,
  inicio              DATETIME NOT NULL,
  fin                 DATETIME NOT NULL,
  cupos               TINYINT UNSIGNED NOT NULL DEFAULT 1,
  modalidad           ENUM('PRESENCIAL','VIRTUAL') NOT NULL DEFAULT 'PRESENCIAL',
  recurso             VARCHAR(150) NULL COMMENT 'Aula, oficina o plataforma',
  PRIMARY KEY (id_franja),
  KEY ix_franja_convocatoria (id_convocatoria),
  CONSTRAINT fk_franja_convocatoria FOREIGN KEY (id_convocatoria) REFERENCES convocatoria (id_convocatoria) ON DELETE CASCADE,
  CONSTRAINT fk_franja_etapa        FOREIGN KEY (id_etapa)        REFERENCES etapa_proceso (id_etapa),
  CONSTRAINT ck_franja_horas CHECK (fin > inicio),
  CONSTRAINT ck_franja_cupos CHECK (cupos > 0)
) ENGINE=InnoDB COMMENT='Disponibilidad horaria configurada para el proceso';

-- RF-063, RF-065, RF-067, RF-068, RN-016, RN-017
CREATE TABLE entrevista (
  id_entrevista       INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_postulacion      INT UNSIGNED NOT NULL,
  id_etapa            INT UNSIGNED NOT NULL,
  id_franja           INT UNSIGNED NULL,
  inicio              DATETIME NOT NULL,
  fin                 DATETIME NOT NULL,
  modalidad           ENUM('PRESENCIAL','VIRTUAL') NOT NULL DEFAULT 'PRESENCIAL',
  ubicacion           VARCHAR(150) NULL,
  enlace_virtual      VARCHAR(255) NULL,
  estado              ENUM('PROGRAMADA','CONFIRMADA','REPROGRAMADA','CANCELADA','REALIZADA') NOT NULL DEFAULT 'PROGRAMADA',
  confirmacion_candidato ENUM('PENDIENTE','CONFIRMADA','SOLICITA_REPROGRAMACION') NOT NULL DEFAULT 'PENDIENTE',
  reprogramaciones    TINYINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (id_entrevista),
  KEY ix_entrevista_post (id_postulacion),
  KEY ix_entrevista_horario (inicio, fin),
  CONSTRAINT fk_entrev_postulacion FOREIGN KEY (id_postulacion) REFERENCES postulacion (id_postulacion),
  CONSTRAINT fk_entrev_etapa       FOREIGN KEY (id_etapa)       REFERENCES etapa_proceso (id_etapa),
  CONSTRAINT fk_entrev_franja      FOREIGN KEY (id_franja)      REFERENCES franja_entrevista (id_franja),
  CONSTRAINT ck_entrev_horas  CHECK (fin > inicio),
  CONSTRAINT ck_entrev_lugar  CHECK (modalidad <> 'PRESENCIAL' OR ubicacion IS NOT NULL),
  CONSTRAINT ck_entrev_enlace CHECK (modalidad <> 'VIRTUAL' OR enlace_virtual IS NOT NULL)
) ENGINE=InnoDB COMMENT='Cita de entrevista presencial o virtual';

-- RF-062, RN-018
CREATE TABLE entrevistador (
  id_entrevistador    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_entrevista       INT UNSIGNED NOT NULL,
  id_usuario          INT UNSIGNED NOT NULL,
  rol_comite          ENUM('PRESIDE','MIEMBRO','SECRETARIO') NOT NULL DEFAULT 'MIEMBRO',
  estado              ENUM('ASIGNADO','CONFIRMADO','EXCUSADO') NOT NULL DEFAULT 'ASIGNADO',
  PRIMARY KEY (id_entrevistador),
  UNIQUE KEY uq_entrevistador (id_entrevista, id_usuario),
  CONSTRAINT fk_entrevistador_entrevista FOREIGN KEY (id_entrevista) REFERENCES entrevista (id_entrevista) ON DELETE CASCADE,
  CONSTRAINT fk_entrevistador_usuario    FOREIGN KEY (id_usuario)    REFERENCES usuario (id_usuario)
) ENGINE=InnoDB COMMENT='Miembros del comité evaluador de la entrevista';

-- RF-069
CREATE TABLE guia_entrevista (
  id_guia             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_etapa            INT UNSIGNED NOT NULL,
  orden               TINYINT UNSIGNED NOT NULL,
  pregunta            VARCHAR(300) NOT NULL,
  id_criterio         INT UNSIGNED NULL,
  PRIMARY KEY (id_guia),
  UNIQUE KEY uq_guia_etapa_orden (id_etapa, orden),
  CONSTRAINT fk_guia_etapa    FOREIGN KEY (id_etapa)    REFERENCES etapa_proceso (id_etapa) ON DELETE CASCADE,
  CONSTRAINT fk_guia_criterio FOREIGN KEY (id_criterio) REFERENCES criterio_evaluacion (id_criterio)
) ENGINE=InnoDB COMMENT='Preguntas estandarizadas orientativas';

-- RF-070, RF-071, RF-090, RN-018
CREATE TABLE resultado_entrevista (
  id_resultado        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_entrevista       INT UNSIGNED NOT NULL,
  id_evaluador        INT UNSIGNED NULL,
  tipo                ENUM('INDIVIDUAL','CONSOLIDADO') NOT NULL,
  puntaje             DECIMAL(5,2) NULL,
  concepto            ENUM('FAVORABLE','FAVORABLE_CON_RESERVAS','NO_FAVORABLE') NOT NULL,
  observacion         VARCHAR(500) NULL,
  fecha               DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_resultado),
  UNIQUE KEY uq_resultado_evaluador (id_entrevista, id_evaluador),
  CONSTRAINT fk_resent_entrevista FOREIGN KEY (id_entrevista) REFERENCES entrevista (id_entrevista) ON DELETE CASCADE,
  CONSTRAINT fk_resent_evaluador  FOREIGN KEY (id_evaluador)  REFERENCES usuario (id_usuario),
  CONSTRAINT ck_resent_tipo    CHECK ((tipo = 'INDIVIDUAL' AND id_evaluador IS NOT NULL) OR tipo = 'CONSOLIDADO'),
  CONSTRAINT ck_resent_puntaje CHECK (puntaje IS NULL OR (puntaje >= 0 AND puntaje <= 100))
) ENGINE=InnoDB COMMENT='Conceptos y calificaciones individuales y consolidadas';

-- RF-093, RN-024: Resultado obligatorio y causal por etapa
CREATE TABLE resultado_etapa (
  id_resultado_etapa  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_postulacion      INT UNSIGNED NOT NULL,
  id_etapa            INT UNSIGNED NOT NULL,
  dictamen            ENUM('APROBADO','NO_APROBADO') NOT NULL,
  observacion         TEXT NOT NULL,
  id_motivo_descarte  SMALLINT UNSIGNED NULL,
  id_responsable      INT UNSIGNED NOT NULL,
  fecha_registro      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_resultado_etapa),
  UNIQUE KEY uq_resultado_etapa_post (id_postulacion, id_etapa),
  CONSTRAINT fk_re_postulacion FOREIGN KEY (id_postulacion) REFERENCES postulacion (id_postulacion),
  CONSTRAINT fk_re_etapa       FOREIGN KEY (id_etapa)       REFERENCES etapa_proceso (id_etapa),
  CONSTRAINT fk_re_motivo      FOREIGN KEY (id_motivo_descarte) REFERENCES motivo (id_motivo),
  CONSTRAINT fk_re_responsable FOREIGN KEY (id_responsable) REFERENCES usuario (id_usuario),
  CONSTRAINT ck_re_descarte CHECK (dictamen = 'APROBADO' OR id_motivo_descarte IS NOT NULL)
) ENGINE=InnoDB COMMENT='Cierre formal obligatorio por cada etapa del proceso (RN-024)';

-- RF-073, RF-074, RN-015
CREATE TABLE decision_final (
  id_decision         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_postulacion      INT UNSIGNED NOT NULL,
  resultado           ENUM('SELECCIONADO','NO_SELECCIONADO','ELEGIBLE') NOT NULL,
  fundamento          VARCHAR(500) NOT NULL,
  id_usuario          INT UNSIGNED NOT NULL COMMENT 'Decisión humana autorizada (RNF-018)',
  fecha_decision      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  fecha_publicacion   DATETIME NULL,
  PRIMARY KEY (id_decision),
  UNIQUE KEY uq_decision_post (id_postulacion),
  CONSTRAINT fk_dec_postulacion FOREIGN KEY (id_postulacion) REFERENCES postulacion (id_postulacion),
  CONSTRAINT fk_dec_usuario     FOREIGN KEY (id_usuario)     REFERENCES usuario (id_usuario),
  CONSTRAINT ck_dec_publicacion CHECK (fecha_publicacion IS NULL OR fecha_publicacion >= fecha_decision)
) ENGINE=InnoDB COMMENT='Veredicto final registrado por el gestor de Talento Humano';

-- =============================================================================
-- 8. TRAZABILIDAD, NOTIFICACIONES, SOPORTE Y PLANES OPERATIVOS
-- =============================================================================

-- RF-059, RN-020
CREATE TABLE movimiento (
  id_movimiento       BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_postulacion      INT UNSIGNED NOT NULL,
  id_usuario          INT UNSIGNED NOT NULL,
  accion              VARCHAR(60)  NOT NULL,
  estado_anterior     VARCHAR(30)  NULL,
  estado_nuevo        VARCHAR(30)  NULL,
  id_motivo           SMALLINT UNSIGNED NULL,
  detalle             VARCHAR(500) NULL,
  visible_candidato   BOOLEAN NOT NULL DEFAULT FALSE,
  fecha_hora          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_movimiento),
  KEY ix_mov_postulacion (id_postulacion, fecha_hora),
  CONSTRAINT fk_mov_postulacion FOREIGN KEY (id_postulacion) REFERENCES postulacion (id_postulacion) ON DELETE CASCADE,
  CONSTRAINT fk_mov_usuario     FOREIGN KEY (id_usuario)     REFERENCES usuario (id_usuario),
  CONSTRAINT fk_mov_motivo      FOREIGN KEY (id_motivo)      REFERENCES motivo (id_motivo)
) ENGINE=InnoDB COMMENT='Línea de tiempo funcional: bitácora auditable solo de inserción';

-- RF-059, RNF-019
CREATE TABLE auditoria (
  id_auditoria        BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_usuario          INT UNSIGNED NULL,
  accion              VARCHAR(60) NOT NULL,
  entidad             VARCHAR(60) NOT NULL,
  id_entidad          BIGINT UNSIGNED NULL,
  datos_antes         JSON NULL,
  datos_despues       JSON NULL,
  ip                  VARCHAR(45) NULL,
  fecha_hora          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_auditoria),
  KEY ix_aud_entidad (entidad, id_entidad),
  CONSTRAINT fk_aud_usuario FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario)
) ENGINE=InnoDB COMMENT='Registro de auditoría técnica y seguridad inmutable';

-- RF-066, RF-074, RF-081, RF-100, RN-022, RN-028
CREATE TABLE notificacion (
  id_notificacion     INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_destinatario     INT UNSIGNED NOT NULL,
  id_postulacion      INT UNSIGNED NULL,
  canal               ENUM('PORTAL','CORREO') NOT NULL DEFAULT 'PORTAL',
  asunto              VARCHAR(150) NOT NULL,
  mensaje             VARCHAR(1000) NOT NULL,
  fecha_creacion      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  leida_en            DATETIME NULL,
  estado_envio        ENUM('PENDIENTE','ENVIADA','FALLIDA') NOT NULL DEFAULT 'ENVIADA',
  PRIMARY KEY (id_notificacion),
  KEY ix_notif_destinatario (id_destinatario, leida_en),
  CONSTRAINT fk_notif_destinatario FOREIGN KEY (id_destinatario) REFERENCES usuario (id_usuario),
  CONSTRAINT fk_notif_postulacion  FOREIGN KEY (id_postulacion)  REFERENCES postulacion (id_postulacion) ON DELETE CASCADE
) ENGINE=InnoDB COMMENT='Buzón de notificaciones del portal y cola de correos';

-- RF-091: Planes de trabajo de coordinación
CREATE TABLE plan_trabajo (
  id_plan             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_convocatoria     INT UNSIGNED NOT NULL,
  id_responsable      INT UNSIGNED NOT NULL,
  fecha_inicio        DATE NOT NULL,
  fecha_estimada_fin  DATE NOT NULL,
  estado              ENUM('EN_CURSO','COMPLETADO','SUSPENDIDO') NOT NULL DEFAULT 'EN_CURSO',
  PRIMARY KEY (id_plan),
  UNIQUE KEY uq_plan_convocatoria (id_convocatoria),
  CONSTRAINT fk_plan_convocatoria FOREIGN KEY (id_convocatoria) REFERENCES convocatoria (id_convocatoria),
  CONSTRAINT fk_plan_responsable  FOREIGN KEY (id_responsable)  REFERENCES usuario (id_usuario)
) ENGINE=InnoDB COMMENT='Plan operativo de la convocatoria para la Coordinación de Selección';

-- RF-091
CREATE TABLE actividad_plan (
  id_actividad        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_plan             INT UNSIGNED NOT NULL,
  nombre              VARCHAR(150) NOT NULL,
  descripcion         TEXT NULL,
  id_responsable      INT UNSIGNED NOT NULL,
  fecha_limite        DATE NOT NULL,
  completada          BOOLEAN NOT NULL DEFAULT FALSE,
  fecha_completada    DATETIME NULL,
  PRIMARY KEY (id_actividad),
  KEY ix_actividad_plan (id_plan),
  CONSTRAINT fk_act_plan FOREIGN KEY (id_plan) REFERENCES plan_trabajo (id_plan) ON DELETE CASCADE,
  CONSTRAINT fk_act_responsable FOREIGN KEY (id_responsable) REFERENCES usuario (id_usuario)
) ENGINE=InnoDB COMMENT='Tareas operativas del plan de selección';

-- RF-099: Mesa de ayuda y soporte al candidato
CREATE TABLE ticket_ayuda (
  id_ticket           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_usuario          INT UNSIGNED NOT NULL,
  categoria           ENUM('PROBLEMA_TECNICO','DUDA_CONVOCATORIA','SOPORTES','OTRO') NOT NULL,
  asunto              VARCHAR(150) NOT NULL,
  mensaje             TEXT NOT NULL,
  estado              ENUM('ABIERTO','EN_ATENCION','CERRADO') NOT NULL DEFAULT 'ABIERTO',
  fecha_creacion      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_ticket),
  KEY ix_ticket_usuario (id_usuario),
  CONSTRAINT fk_ticket_usuario FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario)
) ENGINE=InnoDB COMMENT='Tickets de soporte de candidatos o usuarios';

-- RF-099
CREATE TABLE respuesta_ticket (
  id_respuesta        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_ticket           INT UNSIGNED NOT NULL,
  id_usuario          INT UNSIGNED NOT NULL,
  mensaje             TEXT NOT NULL,
  fecha               DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_respuesta),
  KEY ix_resp_ticket (id_ticket),
  CONSTRAINT fk_rt_ticket  FOREIGN KEY (id_ticket)  REFERENCES ticket_ayuda (id_ticket) ON DELETE CASCADE,
  CONSTRAINT fk_rt_usuario FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario)
) ENGINE=InnoDB COMMENT='Historial de respuestas en los tickets de ayuda';

-- =============================================================================
-- 9. TABLAS DE FASE 2: CONTRATACIÓN, SALUD OCUPACIONAL Y VINCULACIÓN
-- (Estructuradas para extensión sin alterar el núcleo de Fase 1)
-- =============================================================================

-- RF-101, RN-027, RNF-028
CREATE TABLE examen_ocupacional (
  id_examen           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_postulacion      INT UNSIGNED NOT NULL,
  proveedor_salud     VARCHAR(150) NOT NULL,
  fecha_programada    DATE NOT NULL,
  fecha_concepto      DATE NULL,
  concepto_aptitud    ENUM('APTO','APTO_CON_RESTRICCIONES','NO_APTO','PENDIENTE') NOT NULL DEFAULT 'PENDIENTE',
  observaciones_sst   VARCHAR(500) NULL COMMENT 'Estrictamente laboral, sin datos clínicos protegidos (RN-027)',
  id_responsable_sst  INT UNSIGNED NOT NULL,
  PRIMARY KEY (id_examen),
  UNIQUE KEY uq_examen_postulacion (id_postulacion),
  CONSTRAINT fk_eo_postulacion FOREIGN KEY (id_postulacion) REFERENCES postulacion (id_postulacion),
  CONSTRAINT fk_eo_sst         FOREIGN KEY (id_responsable_sst) REFERENCES usuario (id_usuario)
) ENGINE=InnoDB COMMENT='Concepto de aptitud ocupacional (sin diagnósticos médicos)';

-- RF-102, RF-103
CREATE TABLE contratacion (
  id_contratacion     INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_postulacion      INT UNSIGNED NOT NULL,
  estado_formalizacion ENUM('PENDIENTE','EN_TRAMITE','FIRMADO','RECHAZADO','FORMALIZADO') NOT NULL DEFAULT 'PENDIENTE',
  fecha_inicio_labores DATE NULL,
  id_responsable_gh   INT UNSIGNED NOT NULL,
  observaciones       TEXT NULL,
  creado_en           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_contratacion),
  UNIQUE KEY uq_contratacion_post (id_postulacion),
  CONSTRAINT fk_cont_postulacion FOREIGN KEY (id_postulacion) REFERENCES postulacion (id_postulacion),
  CONSTRAINT fk_cont_gh          FOREIGN KEY (id_responsable_gh) REFERENCES usuario (id_usuario)
) ENGINE=InnoDB COMMENT='Expediente de contratación institucional';

-- RF-102
CREATE TABLE documento_contratacion (
  id_doc_contratacion INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_contratacion     INT UNSIGNED NOT NULL,
  tipo_documento      VARCHAR(80)  NOT NULL COMMENT 'RUT, EPS, AFP, Certificado Bancario, Contrato firmado',
  ruta_archivo        VARCHAR(255) NOT NULL,
  estado_verificacion ENUM('PENDIENTE','VALIDO','RECHAZADO') NOT NULL DEFAULT 'PENDIENTE',
  fecha_carga         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_doc_contratacion),
  KEY ix_doc_contratacion (id_contratacion),
  CONSTRAINT fk_dc_contratacion FOREIGN KEY (id_contratacion) REFERENCES contratacion (id_contratacion) ON DELETE CASCADE
) ENGINE=InnoDB COMMENT='Documentos y anexos legales para formalizar la contratación';

-- RF-104, RN-025
CREATE TABLE vinculacion (
  id_vinculacion      INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_contratacion     INT UNSIGNED NOT NULL,
  codigo_colaborador  VARCHAR(30)  NOT NULL COMMENT 'Código de empleado asignado por CECAR',
  fecha_vinculacion   DATE NOT NULL,
  estado              ENUM('ACTIVO','FINALIZADO') NOT NULL DEFAULT 'ACTIVO',
  id_autorizado_por   INT UNSIGNED NOT NULL,
  fecha_registro      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_vinculacion),
  UNIQUE KEY uq_vinc_contratacion (id_contratacion),
  UNIQUE KEY uq_vinc_codigo (codigo_colaborador),
  CONSTRAINT fk_vinc_contratacion FOREIGN KEY (id_contratacion) REFERENCES contratacion (id_contratacion),
  CONSTRAINT fk_vinc_autoriza     FOREIGN KEY (id_autorizado_por) REFERENCES usuario (id_usuario)
) ENGINE=InnoDB COMMENT='Confirmación formal del candidato como colaborador institucional';

-- RF-105, RF-106, RN-026
CREATE TABLE integracion_hv (
  id_integracion      INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_postulacion      INT UNSIGNED NOT NULL,
  sistema_destino     VARCHAR(80)  NOT NULL DEFAULT 'SISTEMA_TALENTO_CECAR',
  id_externo_creado   VARCHAR(50)  NULL,
  estado              ENUM('PENDIENTE','TRANSFERIDO','ERROR') NOT NULL DEFAULT 'PENDIENTE',
  fecha_transferencia DATETIME NULL,
  reintentos          TINYINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (id_integracion),
  UNIQUE KEY uq_integracion_post (id_postulacion),
  CONSTRAINT fk_int_postulacion FOREIGN KEY (id_postulacion) REFERENCES postulacion (id_postulacion)
) ENGINE=InnoDB COMMENT='Control de exportación idempotente a la plataforma de nómina / colaboradores';

-- RF-106
CREATE TABLE bitacora_integracion (
  id_bitacora         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_integracion      INT UNSIGNED NOT NULL,
  payload_enviado     JSON NULL,
  respuesta_recibida  TEXT NULL,
  codigo_http         SMALLINT UNSIGNED NULL,
  error_detalle       TEXT NULL,
  fecha_intento       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_bitacora),
  KEY ix_bi_integracion (id_integracion),
  CONSTRAINT fk_bi_integracion FOREIGN KEY (id_integracion) REFERENCES integracion_hv (id_integracion) ON DELETE CASCADE
) ENGINE=InnoDB COMMENT='Log de transacciones de sincronización con sistemas externos';

-- =============================================================================
-- 10. VISTAS ANALÍTICAS Y OPERATIVAS
-- =============================================================================

-- Vista de la última versión y estado de cada ítem
CREATE OR REPLACE VIEW v_item_vigente AS
SELECT i.id_item_hv, i.id_postulacion, i.id_tipo_item,
       v.id_version_item, v.numero_version, v.datos,
       (SELECT r.resultado FROM revision_item r
         WHERE r.id_version_item = v.id_version_item
         ORDER BY r.fecha_revision DESC, r.id_revision DESC LIMIT 1) AS ultimo_resultado,
       (SELECT COUNT(*) FROM soporte s
         WHERE s.id_version_item = v.id_version_item AND s.estado_archivo = 'VALIDO') AS n_soportes
FROM item_hv i
JOIN version_item v ON v.id_item_hv = i.id_item_hv
 AND v.numero_version = (SELECT MAX(v2.numero_version) FROM version_item v2 WHERE v2.id_item_hv = i.id_item_hv);

-- Vista de cálculo de conformidad documental (RF-053, RN-012)
CREATE OR REPLACE VIEW v_estado_documental AS
SELECT p.id_postulacion,
       p.resultado_documental AS almacenado,
       COUNT(*)                                        AS requisitos_obligatorios,
       SUM(iv.id_item_hv IS NOT NULL)                  AS cubiertos,
       SUM(iv.ultimo_resultado = 'APROBADO')           AS aprobados,
       CASE
         WHEN SUM(iv.ultimo_resultado = 'RECHAZADO') > 0 THEN 'NO_CONFORME'
         WHEN SUM(iv.ultimo_resultado = 'OBSERVADO') > 0 THEN 'OBSERVADO'
         WHEN SUM(iv.ultimo_resultado IS NULL) > 0       THEN 'PENDIENTE'
         ELSE 'CONFORME'
       END AS calculado
FROM postulacion p
JOIN requisito_convocatoria rc ON rc.id_convocatoria = p.id_convocatoria AND rc.obligatoriedad = 'OBLIGATORIO'
JOIN tipo_item_hv t ON t.id_tipo_item = rc.id_tipo_item AND t.almacenamiento = 'ITEM_HV'
LEFT JOIN v_item_vigente iv ON iv.id_postulacion = p.id_postulacion AND iv.id_tipo_item = rc.id_tipo_item
GROUP BY p.id_postulacion, p.resultado_documental;

-- RF-096: Reporte 1 - Aspirantes No Aprobados con causal
CREATE OR REPLACE VIEW v_no_aprobados AS
SELECT p.id_postulacion, c.codigo AS convocatoria, c.cargo, d.nombre AS dependencia,
       CONCAT(ca.nombres,' ',ca.apellidos) AS candidato,
       p.estado, e.nombre AS etapa_descarte,
       mo.nombre AS causa, m.detalle AS observacion, m.fecha_hora AS fecha_descarte
FROM postulacion p
JOIN candidato ca    ON ca.id_candidato = p.id_candidato
JOIN convocatoria c  ON c.id_convocatoria = p.id_convocatoria
JOIN dependencia d   ON d.id_dependencia = c.id_dependencia
LEFT JOIN etapa_proceso e ON e.id_etapa = p.id_etapa_actual
LEFT JOIN movimiento m ON m.id_movimiento = (SELECT MAX(m2.id_movimiento) FROM movimiento m2
                                              WHERE m2.id_postulacion = p.id_postulacion AND m2.estado_nuevo = p.estado)
LEFT JOIN motivo mo ON mo.id_motivo = m.id_motivo
WHERE p.estado IN ('NO_HABILITADA','NO_CONTINUA','NO_SELECCIONADA');

-- RF-096: Reporte 2 - Aspirantes En Proceso
CREATE OR REPLACE VIEW v_en_proceso AS
SELECT p.id_postulacion, c.codigo AS convocatoria, c.cargo, d.nombre AS dependencia,
       CONCAT(ca.nombres,' ',ca.apellidos) AS candidato,
       p.estado, e.nombre AS etapa_actual, p.resultado_documental, p.fecha_envio
FROM postulacion p
JOIN candidato ca    ON ca.id_candidato = p.id_candidato
JOIN convocatoria c  ON c.id_convocatoria = p.id_convocatoria
JOIN dependencia d   ON d.id_dependencia = c.id_dependencia
LEFT JOIN etapa_proceso e ON e.id_etapa = p.id_etapa_actual
WHERE p.estado IN ('ENVIADA','EN_REVISION','SUBSANACION_REQUERIDA','HABILITADA','PRESELECCIONADA','ENTREVISTADA','ELEGIBLE');

-- RF-096: Reporte 3 - Candidatos Seleccionados
CREATE OR REPLACE VIEW v_seleccionados AS
SELECT p.id_postulacion, c.codigo AS convocatoria, c.cargo, d.nombre AS dependencia,
       CONCAT(ca.nombres,' ',ca.apellidos) AS candidato,
       p.estado, df.resultado AS decision, df.fundamento, df.fecha_decision, df.fecha_publicacion
FROM postulacion p
JOIN candidato ca    ON ca.id_candidato = p.id_candidato
JOIN convocatoria c  ON c.id_convocatoria = p.id_convocatoria
JOIN dependencia d   ON d.id_dependencia = c.id_dependencia
JOIN decision_final df ON df.id_postulacion = p.id_postulacion
WHERE df.resultado = 'SELECCIONADO';

-- RF-086, RF-098: Métricas consolidadas para el Dashboard
CREATE OR REPLACE VIEW v_dashboard_indicadores AS
SELECT c.id_convocatoria, c.codigo, c.cargo,
       COUNT(p.id_postulacion) AS total_postulados,
       SUM(CASE WHEN p.estado = 'ENVIADA' THEN 1 ELSE 0 END) AS total_enviadas,
       SUM(CASE WHEN p.estado = 'EN_REVISION' THEN 1 ELSE 0 END) AS total_en_revision,
       SUM(CASE WHEN p.estado = 'SUBSANACION_REQUERIDA' THEN 1 ELSE 0 END) AS total_subsanacion,
       SUM(CASE WHEN p.estado IN ('HABILITADA','PRESELECCIONADA') THEN 1 ELSE 0 END) AS total_habilitadas,
       SUM(CASE WHEN p.estado = 'ENTREVISTADA' THEN 1 ELSE 0 END) AS total_entrevistadas,
       SUM(CASE WHEN p.estado = 'SELECCIONADA' THEN 1 ELSE 0 END) AS total_seleccionados,
       SUM(CASE WHEN p.estado IN ('NO_HABILITADA','NO_CONTINUA','NO_SELECCIONADA') THEN 1 ELSE 0 END) AS total_descartados,
       SUM(CASE WHEN p.estado = 'VINCULADO' THEN 1 ELSE 0 END) AS total_vinculados
FROM convocatoria c
LEFT JOIN postulacion p ON p.id_convocatoria = c.id_convocatoria
GROUP BY c.id_convocatoria, c.codigo, c.cargo;

-- =============================================================================
-- 11. PROCEDIMIENTOS ALMACENADOS (LÓGICA TRANSACCIONAL SEGURA)
-- =============================================================================

DELIMITER $$

-- Valida traslapes de agenda de manera aislada y atómica (RN-017)
CREATE PROCEDURE sp_validar_agenda(
  IN p_excluir_entrevista INT UNSIGNED,
  IN p_postulacion        INT UNSIGNED,
  IN p_inicio             DATETIME,
  IN p_fin                DATETIME,
  IN p_modalidad          VARCHAR(12),
  IN p_ubicacion          VARCHAR(150))
BEGIN
  DECLARE v_candidato INT UNSIGNED;
  SELECT id_candidato INTO v_candidato FROM postulacion WHERE id_postulacion = p_postulacion;

  -- 1. Validar que el candidato no tenga otra entrevista simultánea
  IF EXISTS (SELECT 1 FROM entrevista e JOIN postulacion p ON p.id_postulacion = e.id_postulacion
              WHERE p.id_candidato = v_candidato
                AND e.id_entrevista <> IFNULL(p_excluir_entrevista, 0)
                AND e.estado IN ('PROGRAMADA','CONFIRMADA','REPROGRAMADA')
                AND e.inicio < p_fin AND e.fin > p_inicio) THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Conflicto de agenda: el candidato ya tiene una entrevista en ese horario';
  END IF;

  -- 2. Validar que la sala física no esté ocupada
  IF p_modalidad = 'PRESENCIAL' AND p_ubicacion IS NOT NULL AND EXISTS (
       SELECT 1 FROM entrevista e
        WHERE e.modalidad = 'PRESENCIAL' AND e.ubicacion = p_ubicacion
          AND e.id_entrevista <> IFNULL(p_excluir_entrevista, 0)
          AND e.estado IN ('PROGRAMADA','CONFIRMADA','REPROGRAMADA')
          AND e.inicio < p_fin AND e.fin > p_inicio) THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Conflicto de agenda: el espacio físico ya está ocupado en ese horario';
  END IF;
END$$

-- Transición controlada de estados con registro inmutable en MOVIMIENTO
CREATE PROCEDURE sp_cambiar_estado_postulacion(
  IN p_postulacion INT UNSIGNED,
  IN p_nuevo       VARCHAR(30),
  IN p_usuario     INT UNSIGNED,
  IN p_detalle     VARCHAR(500),
  IN p_motivo      SMALLINT UNSIGNED,
  IN p_etapa       INT UNSIGNED,
  IN p_fecha       DATETIME)
BEGIN
  DECLARE v_anterior   VARCHAR(30);
  DECLARE v_documental VARCHAR(20);
  DECLARE v_candidato  INT UNSIGNED;
  DECLARE v_faltantes  INT DEFAULT 0;
  DECLARE v_fecha      DATETIME DEFAULT IFNULL(p_fecha, NOW());

  SELECT estado, resultado_documental, id_candidato INTO v_anterior, v_documental, v_candidato
    FROM postulacion WHERE id_postulacion = p_postulacion FOR UPDATE;

  IF v_anterior IS NULL THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Postulación inexistente';
  END IF;

  IF v_anterior IN ('VINCULADO','NO_SELECCIONADA','NO_CONTINUA','NO_HABILITADA','RETIRADA') THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La postulación está en un estado final y no admite modificaciones';
  END IF;

  -- Validaciones de pase a ENVIADA
  IF p_nuevo = 'ENVIADA' THEN
    IF NOT EXISTS (SELECT 1 FROM consentimiento WHERE id_candidato = v_candidato
                    AND finalidad = 'TRATAMIENTO_DATOS' AND aceptado AND revocado_en IS NULL) THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Debe aceptar la autorización de tratamiento de datos antes de enviar';
    END IF;

    IF NOT EXISTS (SELECT 1 FROM consentimiento WHERE id_postulacion = p_postulacion
                    AND finalidad = 'VERACIDAD' AND aceptado) THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Debe aceptar la declaración de veracidad antes de enviar';
    END IF;

    SELECT COUNT(*) INTO v_faltantes
      FROM postulacion p
      JOIN requisito_convocatoria rc ON rc.id_convocatoria = p.id_convocatoria AND rc.obligatoriedad = 'OBLIGATORIO'
      JOIN tipo_item_hv t ON t.id_tipo_item = rc.id_tipo_item AND t.almacenamiento = 'ITEM_HV'
     WHERE p.id_postulacion = p_postulacion
       AND NOT EXISTS (SELECT 1 FROM v_item_vigente iv
                        WHERE iv.id_postulacion = p.id_postulacion AND iv.id_tipo_item = rc.id_tipo_item
                          AND (rc.soporte_requerido <> 'OBLIGATORIO' OR iv.n_soportes > 0));

    IF v_faltantes > 0 THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No se puede enviar: existen ítems o soportes obligatorios pendientes';
    END IF;
  END IF;

  -- RN-012: Solo conforme puede quedar habilitada
  IF p_nuevo = 'HABILITADA' AND v_documental <> 'CONFORME' THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Solo un expediente documentalmente conforme puede quedar habilitado';
  END IF;

  -- RF-058, RN-024: Descarte exige causal de catálogo
  IF p_nuevo IN ('NO_HABILITADA','NO_CONTINUA','NO_SELECCIONADA') AND p_motivo IS NULL THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El descarte exige una causal formal de catálogo';
  END IF;

  UPDATE postulacion
     SET estado = p_nuevo,
         fecha_envio = IF(p_nuevo = 'ENVIADA', v_fecha, fecha_envio),
         id_etapa_actual = IFNULL(p_etapa, id_etapa_actual)
   WHERE id_postulacion = p_postulacion;

  INSERT INTO movimiento (id_postulacion, id_usuario, accion, estado_anterior, estado_nuevo,
                          id_motivo, detalle, visible_candidato, fecha_hora)
  VALUES (p_postulacion, p_usuario, 'CAMBIO_ESTADO', v_anterior, p_nuevo, p_motivo, p_detalle, TRUE, v_fecha);
END$$

-- Recalcular estado documental
CREATE PROCEDURE sp_recalcular_estado_documental(IN p_postulacion INT UNSIGNED)
BEGIN
  UPDATE postulacion p
    JOIN v_estado_documental d ON d.id_postulacion = p.id_postulacion
     SET p.resultado_documental = d.calculado
   WHERE p.id_postulacion = p_postulacion;
END$$

-- =============================================================================
-- 12. DISPARADORES (TRIGGERS) PARA INTEGRIDAD Y CONTROL DE VERSIONES
-- =============================================================================

-- Control de fechas y estado de convocatoria al postular (Corrección bug: comprueba PUBLICADA)
CREATE TRIGGER trg_postulacion_periodo BEFORE INSERT ON postulacion FOR EACH ROW
BEGIN
  DECLARE v_estado VARCHAR(12);
  DECLARE v_ap DATETIME;
  DECLARE v_ci DATETIME;

  SELECT estado, fecha_apertura, fecha_cierre INTO v_estado, v_ap, v_ci
    FROM convocatoria WHERE id_convocatoria = NEW.id_convocatoria;

  IF v_estado <> 'PUBLICADA' THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Solo se admiten postulaciones en convocatorias en estado PUBLICADA';
  END IF;

  IF NEW.fecha_inicio < v_ap OR NEW.fecha_inicio > v_ci THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La fecha de postulación está fuera del periodo de apertura y cierre';
  END IF;
END$$

-- Control de cierre extemporáneo
CREATE TRIGGER trg_postulacion_cierre BEFORE UPDATE ON postulacion FOR EACH ROW
BEGIN
  DECLARE v_ci DATETIME;
  IF NEW.fecha_envio IS NOT NULL AND (OLD.fecha_envio IS NULL OR NEW.fecha_envio <> OLD.fecha_envio) THEN
    SELECT fecha_cierre INTO v_ci FROM convocatoria WHERE id_convocatoria = NEW.id_convocatoria;
    IF NEW.fecha_envio > v_ci THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El envío definitivo no puede realizarse después del cierre de la convocatoria';
    END IF;
  END IF;
END$$

-- Control de no repetición de ítems únicos
CREATE TRIGGER trg_item_no_repetible BEFORE INSERT ON item_hv FOR EACH ROW
BEGIN
  DECLARE v_rep BOOLEAN;
  SELECT repetible INTO v_rep FROM tipo_item_hv WHERE id_tipo_item = NEW.id_tipo_item;
  IF NOT v_rep AND EXISTS (SELECT 1 FROM item_hv WHERE id_postulacion = NEW.id_postulacion
                            AND id_tipo_item = NEW.id_tipo_item) THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Esta sección de la hoja de vida no permite registros múltiples';
  END IF;
END$$

-- Control de creación de versiones de ítems (RN-009, RN-011)
CREATE TRIGGER trg_version_item_control BEFORE INSERT ON version_item FOR EACH ROW
BEGIN
  DECLARE v_estado VARCHAR(30);
  DECLARE v_post INT UNSIGNED;

  SELECT p.estado, p.id_postulacion
    INTO v_estado, v_post
    FROM item_hv i
    JOIN postulacion p ON p.id_postulacion = i.id_postulacion
   WHERE i.id_item_hv = NEW.id_item_hv;

  IF v_estado NOT IN ('BORRADOR', 'SUBSANACION_REQUERIDA') THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Expediente bloqueado: no se pueden agregar ni modificar versiones en el estado actual';
  END IF;

  IF v_estado = 'SUBSANACION_REQUERIDA' AND NOT EXISTS (
      SELECT 1
        FROM detalle_subsanacion d
        JOIN solicitud_subsanacion s ON s.id_solicitud = d.id_solicitud
       WHERE d.id_item_hv = NEW.id_item_hv
         AND s.id_postulacion = v_post
         AND s.estado = 'VIGENTE'
         AND d.estado = 'PENDIENTE'
         AND IFNULL(s.fecha_extension, s.fecha_limite) >= IFNULL(NEW.creado_en, NOW())
  ) THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Solo se pueden actualizar ítems formalmente requeridos dentro del plazo de subsanación';
  END IF;
END$$

-- Inmutabilidad de versiones, revisiones, movimientos y auditoría (RNF-019)
CREATE TRIGGER trg_version_no_update BEFORE UPDATE ON version_item FOR EACH ROW
  SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Las versiones de ítems son inmutables'$$
CREATE TRIGGER trg_version_no_delete BEFORE DELETE ON version_item FOR EACH ROW
  SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Las versiones de ítems no se pueden eliminar'$$
CREATE TRIGGER trg_revision_no_update BEFORE UPDATE ON revision_item FOR EACH ROW
  SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Las revisiones no se sobrescriben'$$
CREATE TRIGGER trg_revision_no_delete BEFORE DELETE ON revision_item FOR EACH ROW
  SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Las revisiones no se pueden eliminar'$$
CREATE TRIGGER trg_movimiento_no_update BEFORE UPDATE ON movimiento FOR EACH ROW
  SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La bitácora de movimientos es de solo inserción'$$
CREATE TRIGGER trg_movimiento_no_delete BEFORE DELETE ON movimiento FOR EACH ROW
  SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La bitácora de movimientos no se puede eliminar'$$
CREATE TRIGGER trg_auditoria_no_update BEFORE UPDATE ON auditoria FOR EACH ROW
  SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El registro de auditoría es inalterable'$$
CREATE TRIGGER trg_auditoria_no_delete BEFORE DELETE ON auditoria FOR EACH ROW
  SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El registro de auditoría no se puede eliminar'$$

-- Control de criterios bloqueados
CREATE TRIGGER trg_criterio_bloqueo BEFORE UPDATE ON criterio_evaluacion FOR EACH ROW
BEGIN
  IF OLD.bloqueado AND (NEW.puntaje_maximo <> OLD.puntaje_maximo OR NEW.ponderacion <> OLD.ponderacion
                        OR NEW.nombre <> OLD.nombre) THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El criterio está bloqueado porque la fase de evaluación ya inició';
  END IF;
END$$

-- Validación de puntaje máximo de evaluación
CREATE TRIGGER trg_evaluacion_max_ins BEFORE INSERT ON evaluacion FOR EACH ROW
BEGIN
  DECLARE v_max DECIMAL(5,2);
  SELECT puntaje_maximo INTO v_max FROM criterio_evaluacion WHERE id_criterio = NEW.id_criterio;
  IF NEW.puntaje IS NOT NULL AND NEW.puntaje > v_max THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El puntaje excede el máximo permitido para este criterio';
  END IF;
END$$

-- Preselección condicionada a estado documental conforme (RN-012, RN-016)
CREATE TRIGGER trg_preseleccion_conforme BEFORE INSERT ON preseleccion FOR EACH ROW
BEGIN
  DECLARE v_doc VARCHAR(20);
  SELECT resultado_documental INTO v_doc FROM postulacion WHERE id_postulacion = NEW.id_postulacion;
  IF NOT NEW.es_excepcion AND v_doc <> 'CONFORME' THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Solo se puede preseleccionar si el expediente está documentalmente Conforme';
  END IF;
END$$

-- Verificación de preselección para entrevista
CREATE TRIGGER trg_entrevista_preseleccion BEFORE INSERT ON entrevista FOR EACH ROW
BEGIN
  IF NOT EXISTS (SELECT 1 FROM preseleccion WHERE id_postulacion = NEW.id_postulacion AND estado = 'VIGENTE') THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Solo candidatos preseleccionados pueden ser programados para entrevista';
  END IF;
END$$

DELIMITER ;

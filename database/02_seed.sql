-- =============================================================================
-- SISTEMA DE SELECCIÓN DE PERSONAL - CECAR
-- Script de Datos Semilla para Pruebas (02_seed.sql)
-- Motor: MySQL 8.0+ | Juego de Caracteres: utf8mb4 | Zona Horaria: America/Bogota
-- Todos los datos son 100% ficticios, realistas y contextualmente consistentes
-- =============================================================================

USE seleccion_personal_cecar;

SET time_zone = '-05:00';
SET FOREIGN_KEY_CHECKS = 0;

-- Limpieza previa en orden para permitir re-ejecución limpia
TRUNCATE TABLE bitacora_integracion;
TRUNCATE TABLE integracion_hv;
TRUNCATE TABLE vinculacion;
TRUNCATE TABLE documento_contratacion;
TRUNCATE TABLE contratacion;
TRUNCATE TABLE examen_ocupacional;
TRUNCATE TABLE respuesta_ticket;
TRUNCATE TABLE ticket_ayuda;
TRUNCATE TABLE actividad_plan;
TRUNCATE TABLE plan_trabajo;
TRUNCATE TABLE notificacion;
TRUNCATE TABLE auditoria;
TRUNCATE TABLE movimiento;
TRUNCATE TABLE decision_final;
TRUNCATE TABLE resultado_etapa;
TRUNCATE TABLE resultado_entrevista;
TRUNCATE TABLE guia_entrevista;
TRUNCATE TABLE entrevistador;
TRUNCATE TABLE entrevista;
TRUNCATE TABLE franja_entrevista;
TRUNCATE TABLE preseleccion;
TRUNCATE TABLE prueba_aula;
TRUNCATE TABLE resultado_prueba;
TRUNCATE TABLE prueba;
TRUNCATE TABLE evaluacion;
TRUNCATE TABLE detalle_subsanacion;
TRUNCATE TABLE solicitud_subsanacion;
TRUNCATE TABLE revision_item;
TRUNCATE TABLE soporte;
TRUNCATE TABLE version_item;
TRUNCATE TABLE item_hv;
TRUNCATE TABLE banco_talento;
TRUNCATE TABLE consentimiento;
TRUNCATE TABLE postulacion;
TRUNCATE TABLE criterio_evaluacion;
TRUNCATE TABLE etapa_proceso;
TRUNCATE TABLE requisito_convocatoria;
TRUNCATE TABLE tipo_item_hv;
TRUNCATE TABLE perfil_cargo;
TRUNCATE TABLE convocatoria;
TRUNCATE TABLE referencia;
TRUNCATE TABLE candidato;
TRUNCATE TABLE sesion;
TRUNCATE TABLE rol_permiso;
TRUNCATE TABLE usuario_rol;
TRUNCATE TABLE usuario;
TRUNCATE TABLE permiso;
TRUNCATE TABLE rol;
TRUNCATE TABLE motivo;
TRUNCATE TABLE catalogo;
TRUNCATE TABLE modalidad_contratacion;
TRUNCATE TABLE periodo_academico;
TRUNCATE TABLE sede;
TRUNCATE TABLE programa_academico;
TRUNCATE TABLE facultad;
TRUNCATE TABLE dependencia;
TRUNCATE TABLE tipo_documento;
TRUNCATE TABLE parametro_sistema;

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================================================
-- 1. PARÁMETROS DEL SISTEMA (RF-034, RF-051, RF-080, RNF-014)
-- =============================================================================
INSERT INTO parametro_sistema (clave, valor, tipo, descripcion, fecha_vigencia) VALUES
('archivo.tamano_maximo_mb', '5', 'ENTERO', 'Tamaño máximo permitido por archivo de soporte en Megabytes', '2026-01-01'),
('archivo.mimes_permitidos', 'application/pdf', 'TEXTO', 'Lista separada por comas de tipos MIME válidos para soportes', '2026-01-01'),
('archivo.extensiones_permitidas', 'pdf', 'TEXTO', 'Extensiones de archivo autorizadas', '2026-01-01'),
('subsanacion.dias_plazo_default', '3', 'ENTERO', 'Plazo estándar en días calendario para responder subsanaciones', '2026-01-01'),
('subsanacion.max_iteraciones', '2', 'ENTERO', 'Cantidad máxima de solicitudes de subsanación por candidato', '2026-01-01'),
('notificacion.correo_activo', 'true', 'BOOLEANO', 'Bandera para habilitar el envío complementario por SMTP', '2026-01-01'),
('notificacion.remitente_nombre', 'Talento Humano CECAR', 'TEXTO', 'Nombre del remitente en correos transaccionales', '2026-01-01'),
('notificacion.remitente_email', 'seleccion@cecar.edu.co', 'TEXTO', 'Dirección de correo de salida del sistema', '2026-01-01'),
('seguridad.expiracion_sesion_minutos', '30', 'ENTERO', 'Tiempo de inactividad antes de invalidar la sesión web', '2026-01-01'),
('entrevista.minutos_tolerancia_conflicto', '15', 'ENTERO', 'Margen mínimo en minutos entre citas consecutivas de evaluadores', '2026-01-01');

-- =============================================================================
-- 2. CATÁLOGOS BASE Y ESTRUCTURA INSTITUCIONAL CECAR (RF-006, RF-080, RF-097)
-- =============================================================================
INSERT INTO tipo_documento (id_tipo_documento, codigo, nombre, activo) VALUES
(1, 'CC',  'Cédula de Ciudadanía', TRUE),
(2, 'CE',  'Cédula de Extranjería', TRUE),
(3, 'PA',  'Pasaporte', TRUE),
(4, 'PEP', 'Permiso Especial de Permanencia', TRUE),
(5, 'PPT', 'Permiso por Protección Temporal', TRUE);

INSERT INTO dependencia (id_dependencia, nombre, sigla, estado) VALUES
(1, 'Rectoría General', 'REC', 'ACTIVA'),
(2, 'Vicerrectoría Académica', 'VAC', 'ACTIVA'),
(3, 'Dirección de Gestión del Talento Humano', 'GTH', 'ACTIVA'),
(4, 'Dirección de Tecnologías de Información', 'DTI', 'ACTIVA'),
(5, 'Dirección de Investigaciones', 'DINV', 'ACTIVA'),
(6, 'Dirección de Extensión y Proyección Social', 'DEXT', 'ACTIVA'),
(7, 'Dirección Administrativa y Financiera', 'DAF', 'ACTIVA'),
(8, 'Centro de Desarrollo Profesoral', 'CDP', 'ACTIVA'),
(9, 'Oficina Jurídica', 'OJUR', 'ACTIVA'),
(10, 'Seguridad y Salud en el Trabajo', 'SST', 'ACTIVA');

INSERT INTO facultad (id_facultad, nombre, codigo, activo) VALUES
(1, 'Facultad de Ciencias Básicas, Ingenierías y Arquitectura', 'FCBIA', TRUE),
(2, 'Facultad de Humanidades y Educación', 'FHE', TRUE),
(3, 'Facultad de Ciencias Económicas y Administrativas', 'FCEA', TRUE),
(4, 'Facultad de Derecho y Ciencias Políticas', 'FDCP', TRUE);

INSERT INTO programa_academico (id_programa, id_facultad, nombre, codigo, activo) VALUES
(1, 1, 'Ingeniería de Sistemas', 'ISIST', TRUE),
(2, 1, 'Ingeniería Industrial', 'IIND', TRUE),
(3, 1, 'Arquitectura', 'ARQ', TRUE),
(4, 2, 'Licenciatura en Inglés', 'LING', TRUE),
(5, 2, 'Licenciatura en Pedagogía Infantil', 'LPED', TRUE),
(6, 2, 'Psicología', 'PSIC', TRUE),
(7, 3, 'Administración de Empresas', 'ADEM', TRUE),
(8, 3, 'Contaduría Pública', 'CONT', TRUE),
(9, 3, 'Economía', 'ECON', TRUE),
(10, 4, 'Derecho', 'DER', TRUE);

INSERT INTO sede (id_sede, nombre, ciudad, activo) VALUES
(1, 'Campus Principal Sincelejo', 'Sincelejo', TRUE),
(2, 'Centro Tutorial Montería', 'Montería', TRUE),
(3, 'Centro Tutorial Corozal', 'Corozal', TRUE),
(4, 'Centro Tutorial San Marcos', 'San Marcos', TRUE),
(5, 'Sede Magangué', 'Magangué', TRUE);

INSERT INTO periodo_academico (id_periodo, codigo, nombre, fecha_inicio, fecha_fin, activo) VALUES
(1, '2026-1', 'Primer Semestre Académico 2026', '2026-01-15', '2026-06-30', TRUE),
(2, '2026-2', 'Segundo Semestre Académico 2026', '2026-07-15', '2026-12-15', TRUE),
(3, '2027-1', 'Primer Semestre Académico 2027', '2027-01-15', '2027-06-30', TRUE);

INSERT INTO modalidad_contratacion (id_modalidad_contratacion, nombre, activo) VALUES
(1, 'Docente Tiempo Completo', TRUE),
(2, 'Docente Medio Tiempo', TRUE),
(3, 'Docente Cátedra', TRUE),
(4, 'Administrativo Término Indefinido', TRUE),
(5, 'Administrativo Término Fijo', TRUE),
(6, 'Prestación de Servicios Profesionales', TRUE);

INSERT INTO motivo (id_motivo, tipo, codigo, nombre, mensaje_candidato, activo) VALUES
(1, 'REVISION', 'SOPORTE_ILEGIBLE', 'Soporte borroso o ilegible', 'El documento adjunto no es legible. Por favor escanee el original en PDF nítido.', TRUE),
(2, 'REVISION', 'DOCUMENTO_INCOMPLETO', 'Documento incompleto o cortado', 'El soporte cargado no incluye todas las páginas requeridas.', TRUE),
(3, 'REVISION', 'FECHA_INCONSISTENTE', 'Fechas no coinciden con el certificado', 'La fecha registrada en el formulario difiere de la consignada en la certificación oficial.', TRUE),
(4, 'DESCARTE', 'NO_CUMPLE_EXPERIENCIA', 'Experiencia mínima insuficiente', 'No acredita el tiempo mínimo de experiencia profesional exigido en el perfil de la vacante.', TRUE),
(5, 'DESCARTE', 'NO_CUMPLE_TITULO', 'Título no afín al perfil convocado', 'La titulación académica acreditada no corresponde a las áreas del conocimiento requeridas.', TRUE),
(6, 'DESCARTE', 'SUBSANACION_EXTEMPORANEA', 'Plazo de subsanación vencido', 'No se recibieron las correcciones documentales dentro del plazo improrrogable establecido.', TRUE),
(7, 'DESCARTE', 'PRUEBA_NO_SUPERADA', 'Puntaje de prueba inferior al mínimo aprobatorio', 'El puntaje obtenido en la evaluación psicotécnica o de conocimientos no alcanzó el umbral institucional.', TRUE),
(8, 'RETIRO', 'DESISTIMIENTO_VOLUNTARIO', 'Desistimiento expreso del candidato', 'El candidato manifestó su decisión de retirarse voluntariamente del proceso.', TRUE),
(9, 'REPROGRAMACION', 'CRUCE_AGENDA_INSTITUCIONAL', 'Cruce de agenda de evaluador o espacio', 'Reprogramación por motivos de fuerza mayor institucional o disponibilidad de salas.', TRUE),
(10, 'REPROGRAMACION', 'SOLICITUD_CANDIDATO', 'Reprogramación solicitada por el aspirante', 'Reprogramación autorizada previa justificación remitida por el candidato.', TRUE);

INSERT INTO catalogo (tipo, codigo, nombre, activo) VALUES
('NIVEL_EDUCATIVO', 'PREGRADO', 'Pregrado / Profesional Universitario', TRUE),
('NIVEL_EDUCATIVO', 'ESPECIALIZACION', 'Especialización Universitaria', TRUE),
('NIVEL_EDUCATIVO', 'MAESTRIA', 'Maestría / Magíster', TRUE),
('NIVEL_EDUCATIVO', 'DOCTORADO', 'Doctorado / PhD', TRUE),
('NIVEL_EDUCATIVO', 'TECNOLOGICO', 'Tecnológico', TRUE),
('COMPETENCIA', 'PEDAGOGICA', 'Diseño curricular y estrategias pedagógicas activas', TRUE),
('COMPETENCIA', 'INVESTIGATIVA', 'Formulación de proyectos MinCiencias y escritura científica', TRUE),
('COMPETENCIA', 'DIGITAL', 'Uso de plataformas LMS y recursos educativos abiertos', TRUE),
('COMPETENCIA', 'LIDERAZGO', 'Gestión de equipos y resolución pacífica de conflictos', TRUE),
('NIVEL_IDIOMA', 'A1', 'A1 - Principiante (MCER)', TRUE),
('NIVEL_IDIOMA', 'A2', 'A2 - Básico (MCER)', TRUE),
('NIVEL_IDIOMA', 'B1', 'B1 - Pre-intermedio (MCER)', TRUE),
('NIVEL_IDIOMA', 'B2', 'B2 - Intermedio Alto (MCER)', TRUE),
('NIVEL_IDIOMA', 'C1', 'C1 - Avanzado Dominante (MCER)', TRUE);

-- =============================================================================
-- 3. SEGURIDAD: ROLES, PERMISOS Y USUARIOS (RF-001 al RF-005, RNF-009)
-- HASH BCRYPT REAL (60 caracteres): $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
-- CONTRASEÑA DE PRUEBAS PARA TODOS: password
-- =============================================================================
INSERT INTO rol (id_rol, nombre, descripcion, estado) VALUES
(1, 'ADMINISTRADOR', 'Administrador técnico del sistema y gestión de parámetros', 'ACTIVO'),
(2, 'TALENTO_HUMANO', 'Gestor de selección, convocatorias, bandejas y decisiones finales', 'ACTIVO'),
(3, 'REVISOR_DOCUMENTAL', 'Verificación minuciosa de ítems y soportes documentales', 'ACTIVO'),
(4, 'EVALUADOR', 'Docente o directivo evaluador de pruebas y entrevistas', 'ACTIVO'),
(5, 'DESARROLLO_PROFESORAL', 'Coordinador de rúbricas y pruebas de aula docente', 'ACTIVO'),
(6, 'CANDIDATO', 'Usuario externo postulante a convocatorias públicas', 'ACTIVO'),
(7, 'AUDITOR', 'Consulta de trazabilidad, reportes e informes institucionales', 'ACTIVO');

INSERT INTO permiso (id_permiso, codigo, modulo, descripcion) VALUES
(1, 'usuario.gestionar', 'Seguridad', 'Crear y modificar usuarios internos'),
(2, 'convocatoria.crear', 'Convocatorias', 'Registrar nuevas convocatorias en borrador'),
(3, 'convocatoria.publicar', 'Convocatorias', 'Aprobar y publicar convocatorias al público'),
(4, 'postulacion.revisar', 'Revision', 'Acceder a expedientes y validar soportes'),
(5, 'subsanacion.solicitar', 'Revision', 'Aperturar solicitudes de subsanación'),
(6, 'preseleccion.autorizar', 'Evaluacion', 'Marcar expedientes conformes como preseleccionados'),
(7, 'entrevista.programar', 'Entrevistas', 'Crear franjas y agendar citas a candidatos'),
(8, 'entrevista.calificar', 'Entrevistas', 'Registrar conceptos y notas individuales de comités'),
(9, 'prueba.registrar', 'Pruebas', 'Cargar notas y dictámenes de pruebas psicotécnicas'),
(10, 'decision.registrar', 'Decision', 'Emitir veredicto final de seleccionado o descartado'),
(11, 'reporte.exportar', 'Reportes', 'Generar y descargar informes en Excel y PDF'),
(12, 'catalogo.administrar', 'Parametrizacion', 'Editar catálogos y motivos de rechazo');

INSERT INTO rol_permiso (id_rol, id_permiso) VALUES
(1, 1), (1, 11), (1, 12),
(2, 2), (2, 3), (2, 4), (2, 5), (2, 6), (2, 7), (2, 10), (2, 11),
(3, 4), (3, 5),
(4, 8), (4, 9),
(5, 8), (5, 9), (5, 11),
(7, 11);

-- 20 Usuarios: 8 Funcionarios Internos + 12 Candidatos
INSERT INTO usuario (id_usuario, correo, contrasena_hash, tipo, nombre_completo, id_dependencia, estado, ultimo_acceso) VALUES
-- Usuarios Internos
(1, 'admin.ti@cecar.edu.co',       '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'INTERNO', 'Ing. Roberto Carlos Vergara', 4, 'ACTIVO', '2026-11-10 08:30:00'),
(2, 'coordinacion.th@cecar.edu.co', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'INTERNO', 'Dra. María Claudia Montes',    3, 'ACTIVO', '2026-11-12 14:15:00'),
(3, 'analista.th@cecar.edu.co',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'INTERNO', 'Lic. Juan Camilo Pineda',      3, 'ACTIVO', '2026-11-12 16:40:00'),
(4, 'revisor.doc1@cecar.edu.co',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'INTERNO', 'Esp. Diana Marcela Salgado',   3, 'ACTIVO', '2026-11-11 11:20:00'),
(5, 'revisor.doc2@cecar.edu.co',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'INTERNO', 'Dr. Jorge Luis Contreras',     3, 'ACTIVO', '2026-11-12 09:10:00'),
(6, 'evaluador.sistemas@cecar.edu.co','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','INTERNO', 'Mg. Álvaro José Berrío',      1, 'ACTIVO', '2026-11-12 15:00:00'),
(7, 'evaluador.derecho@cecar.edu.co', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'INTERNO', 'Dra. Carmen Cecilia Paternina', 9, 'ACTIVO', '2026-11-10 17:30:00'),
(8, 'desarrollo.prof@cecar.edu.co', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'INTERNO', 'Dr. Andrés Felipe Mercado',     8, 'ACTIVO', '2026-11-12 10:45:00'),
-- Usuarios Candidatos (Externos)
(9,  'carlos.mendoza.post@gmail.com',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'CANDIDATO', 'Carlos Andrés Mendoza Arrieta',    NULL, 'ACTIVO', '2026-11-12 18:20:00'),
(10, 'laura.perez.abog@outlook.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'CANDIDATO', 'Laura Patricia Pérez Sierra',       NULL, 'ACTIVO', '2026-11-12 12:00:00'),
(11, 'andres.gomez.ing@hotmail.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'CANDIDATO', 'Andrés Felipe Gómez Meza',          NULL, 'ACTIVO', '2026-11-11 19:30:00'),
(12, 'valentina.arrieta@yahoo.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'CANDIDATO', 'Valentina María Arrieta Castillo', NULL, 'ACTIVO', '2026-11-12 20:10:00'),
(13, 'gustavo.martinez.doc@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'CANDIDATO', 'Gustavo Adolfo Martínez Blanco',    NULL, 'ACTIVO', '2026-11-09 14:10:00'),
(14, 'daniela.orozco.arq@gmail.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'CANDIDATO', 'Daniela Sofía Orozco Mercado',     NULL, 'ACTIVO', '2026-11-10 16:50:00'),
(15, 'esteban.vargas.dev@gmail.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'CANDIDATO', 'Esteban David Vargas Quintero',     NULL, 'ACTIVO', '2026-11-12 11:15:00'),
(16, 'mariana.duque.cont@outlook.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'CANDIDATO', 'Mariana Lucía Duque Cárdenas',     NULL, 'ACTIVO', '2026-11-08 09:40:00'),
(17, 'felipe.alvarez.lic@gmail.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'CANDIDATO', 'Felipe Antonio Álvarez Barrios',    NULL, 'ACTIVO', '2026-11-12 13:25:00'),
(18, 'paula.morales.psi@gmail.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'CANDIDATO', 'Paula Andrea Morales Olivera',      NULL, 'ACTIVO', '2026-11-12 17:05:00'),
(19, 'javier.rios.docente@gmail.com',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'CANDIDATO', 'Javier Enrique Ríos Barreto',       NULL, 'ACTIVO', '2026-11-07 10:15:00'),
(20, 'monica.castillo.ing@hotmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'CANDIDATO', 'Mónica María Castillo Hoyos',       NULL, 'ACTIVO', '2026-11-12 19:45:00');

INSERT INTO usuario_rol (id_usuario, id_rol, asignado_por) VALUES
(1, 1, 1), -- Admin
(2, 2, 1), -- Talento Humano Jefe
(3, 2, 1), -- Talento Humano Analista
(4, 3, 2), -- Revisor 1
(5, 3, 2), -- Revisor 2
(6, 4, 2), -- Evaluador Sistemas
(7, 4, 2), -- Evaluador Derecho
(8, 5, 2), -- Desarrollo Profesoral
(9, 6, 1), (10, 6, 1), (11, 6, 1), (12, 6, 1), (13, 6, 1), (14, 6, 1),
(15, 6, 1), (16, 6, 1), (17, 6, 1), (18, 6, 1), (19, 6, 1), (20, 6, 1);

INSERT INTO sesion (id_sesion, id_usuario, creada_en, expira_en, ip) VALUES
(SHA2('sesion_admin_01', 256),   1, '2026-11-12 08:00:00', '2026-11-12 09:00:00', '190.248.12.10'),
(SHA2('sesion_th_02', 256),      2, '2026-11-12 14:00:00', '2026-11-12 15:30:00', '190.248.12.11'),
(SHA2('sesion_cand_09', 256),    9, '2026-11-12 18:00:00', '2026-11-12 19:00:00', '186.84.90.15'),
(SHA2('sesion_cand_10', 256),   10, '2026-11-12 11:30:00', '2026-11-12 12:30:00', '186.84.90.16'),
(SHA2('sesion_cand_11', 256),   11, '2026-11-11 19:00:00', '2026-11-11 20:00:00', '181.143.22.40'),
(SHA2('sesion_cand_12', 256),   12, '2026-11-12 20:00:00', '2026-11-12 21:00:00', '181.143.22.41'),
(SHA2('sesion_cand_13', 256),   13, '2026-11-09 14:00:00', '2026-11-09 15:00:00', '190.157.30.22'),
(SHA2('sesion_cand_14', 256),   14, '2026-11-10 16:30:00', '2026-11-10 17:30:00', '190.157.30.23'),
(SHA2('sesion_cand_15', 256),   15, '2026-11-12 11:00:00', '2026-11-12 12:00:00', '186.84.90.17'),
(SHA2('sesion_cand_16', 256),   16, '2026-11-08 09:00:00', '2026-11-08 10:00:00', '181.143.22.42');

-- =============================================================================
-- 4. CANDIDATOS, CONSENTIMIENTOS Y REFERENCIAS (RF-014 al RF-017, RF-029)
-- =============================================================================
INSERT INTO candidato (id_candidato, id_usuario, nombres, apellidos, id_tipo_documento, numero_documento, telefono, ciudad, direccion) VALUES
(1,  9,  'Carlos Andrés',    'Mendoza Arrieta',    1, '1102845920', '3004567890', 'Sincelejo', 'Barrio Boston Cra 19 # 24-30'),
(2,  10, 'Laura Patricia',   'Pérez Sierra',       1, '1103984512', '3109876543', 'Sincelejo', 'Barrio Venecia Calle 28 # 14-12'),
(3,  11, 'Andrés Felipe',    'Gómez Meza',         1, '1088324190', '3201234567', 'Montería',  'Barrio La Castellana Calle 60 # 12-40'),
(4,  12, 'Valentina María',  'Arrieta Castillo',   1, '1104765231', '3012349876', 'Corozal',   'Centro Calle 30 # 25-18'),
(5,  13, 'Gustavo Adolfo',   'Martínez Blanco',    1, '73158942',   '3156789012', 'Sincelejo', 'Barrio Majagual Cra 18 # 32-05'),
(6,  14, 'Daniela Sofía',    'Orozco Mercado',     1, '1067982451', '3045671234', 'Montería',  'Barrio El Recreo Calle 64 # 4-10'),
(7,  15, 'Esteban David',    'Vargas Quintero',    1, '1102983746', '3189012345', 'Sincelejo', 'Barrio Florencia Calle 21 # 17-50'),
(8,  16, 'Mariana Lucía',    'Duque Cárdenas',     1, '1101928374', '3123456789', 'Corozal',   'Barrio San José Calle 35 # 28-14'),
(9,  17, 'Felipe Antonio',   'Álvarez Barrios',    1, '1003482910', '3145678901', 'Sincelejo', 'Barrio La Selva Cra 25 # 12-45'),
(10, 18, 'Paula Andrea',     'Morales Olivera',    1, '1082938475', '3023456781', 'Montería',  'Barrio El Edén Calle 44 # 10-20'),
(11, 19, 'Javier Enrique',   'Ríos Barreto',       1, '92543821',   '3167890123', 'Sincelejo', 'Barrio Ford Calle 22 # 18-35'),
(12, 20, 'Mónica María',     'Castillo Hoyos',     1, '1103847291', '3112345678', 'Sincelejo', 'Barrio Mochila Cra 15 # 30-10');

INSERT INTO consentimiento (id_candidato, finalidad, version_texto, aceptado, ip) VALUES
(1,  'TRATAMIENTO_DATOS', 'v1.0_2026', TRUE, '186.84.90.15'),
(2,  'TRATAMIENTO_DATOS', 'v1.0_2026', TRUE, '186.84.90.16'),
(3,  'TRATAMIENTO_DATOS', 'v1.0_2026', TRUE, '181.143.22.40'),
(4,  'TRATAMIENTO_DATOS', 'v1.0_2026', TRUE, '181.143.22.41'),
(5,  'TRATAMIENTO_DATOS', 'v1.0_2026', TRUE, '190.157.30.22'),
(6,  'TRATAMIENTO_DATOS', 'v1.0_2026', TRUE, '190.157.30.23'),
(7,  'TRATAMIENTO_DATOS', 'v1.0_2026', TRUE, '186.84.90.17'),
(8,  'TRATAMIENTO_DATOS', 'v1.0_2026', TRUE, '181.143.22.42'),
(9,  'TRATAMIENTO_DATOS', 'v1.0_2026', TRUE, '186.84.90.18'),
(10, 'TRATAMIENTO_DATOS', 'v1.0_2026', TRUE, '181.143.22.43'),
(11, 'TRATAMIENTO_DATOS', 'v1.0_2026', TRUE, '190.157.30.24'),
(12, 'TRATAMIENTO_DATOS', 'v1.0_2026', TRUE, '186.84.90.19'),
(1,  'BANCO_TALENTO',    'v1.0_2026', TRUE, '186.84.90.15'),
(4,  'BANCO_TALENTO',    'v1.0_2026', TRUE, '181.143.22.41');

INSERT INTO referencia (id_candidato, nombre, relacion, entidad, telefono, correo, autoriza_contacto) VALUES
(1, 'Ing. Mauricio Benítez', 'Ex-Jefe Inmediato', 'Soluciones Cloud SAS', '3009988776', 'mbenitez@solcloud.co', TRUE),
(1, 'Mg. Patricia Támara',   'Directora de Tesis', 'Universidad de Sucre', '3128877665', 'ptamara@unisucre.edu.co', TRUE),
(2, 'Dr. Hernán Arboleda',   'Socio Principal', 'Arboleda Abogados', '3104455667', 'harboleda@arboleda.com', TRUE),
(3, 'Dra. Sandra Vergara',   'Jefe de Departamento', 'Unisinú Montería', '3157788990', 'svergara@unisinu.edu.co', TRUE),
(4, 'Mg. Gladys Ortega',     'Coordinadora Académica', 'Colegio Panamericano', '3203344556', 'gortega@panam.edu.co', TRUE),
(5, 'Dr. Wilfrido Chamorro', 'Decano de Posgrados', 'CECAR', '3116677889', 'wchamorro@cecar.edu.co', TRUE),
(6, 'Arq. Ernesto Samper',   'Director de Obra', 'Construcciones del Caribe', '3042233445', 'esamper@concaribe.com', TRUE),
(7, 'Ing. Lina Marcela Ruz', 'Líder Técnico', 'Globant Colombia', '3185566778', 'lina.ruz@globant.com', TRUE),
(8, 'C.P. Orlando Montes',   'Contador General', 'Alcaldía de Sincelejo', '3138899001', 'omontes@sincelejo.gov.co', TRUE),
(9, 'Lic. Beatriz Quiroz',   'Rectora', 'Institución Educativa Madre Amalia', '3174455661', 'bquiroz@iemadreamalia.edu.co', TRUE);

-- =============================================================================
-- 5. CATÁLOGO DE 26 ÍTEMS DE HOJA DE VIDA (HV-001 al HV-026)
-- =============================================================================
INSERT INTO tipo_item_hv (id_tipo_item, codigo, seccion, nombre, campos_minimos, soporte_esperado, obligatoriedad_base, repetible, almacenamiento) VALUES
(1,  'HV-001', 'Consentimientos',        'Autorización tratamiento de datos', 'Version, Fecha, Aceptacion', 'Aceptación electrónica', 'OBLIGATORIO', FALSE, 'CONSENTIMIENTO'),
(2,  'HV-002', 'Identificación',         'Documento de identidad', 'Tipo, Numero', 'PDF Copia Documento', 'CONDICIONAL', FALSE, 'ITEM_HV'),
(3,  'HV-003', 'Datos personales',       'Nombres y apellidos completos', 'Nombres, Apellidos', 'Documento de identidad', 'OBLIGATORIO', FALSE, 'ITEM_HV'),
(4,  'HV-004', 'Contacto',               'Información de contacto', 'Correo, Telefono, Ciudad', 'Sin soporte inicial', 'OBLIGATORIO', FALSE, 'ITEM_HV'),
(5,  'HV-005', 'Perfil',                 'Resumen profesional', 'Descripcion, Areas', 'Sin soporte directo', 'OPCIONAL', FALSE, 'ITEM_HV'),
(6,  'HV-006', 'Formación',              'Título de pregrado', 'Institucion, Programa, Fecha, Titulo', 'Diploma o acta de grado en PDF', 'CONDICIONAL', TRUE, 'ITEM_HV'),
(7,  'HV-007', 'Formación',              'Título de posgrado', 'Nivel, Institucion, Programa, Titulo', 'Diploma o acta en PDF', 'CONDICIONAL', TRUE, 'ITEM_HV'),
(8,  'HV-008', 'Formación',              'Estudio en curso', 'Programa, Institucion, Avance', 'Certificado vigente en PDF', 'OPCIONAL', TRUE, 'ITEM_HV'),
(9,  'HV-009', 'Experiencia',            'Experiencia laboral profesional', 'Entidad, Cargo, Inicio, Fin, Funciones', 'Certificación laboral en PDF', 'CONDICIONAL', TRUE, 'ITEM_HV'),
(10, 'HV-010', 'Experiencia',            'Experiencia docente universitaria', 'Institucion, Asignaturas, Horas, Periodo', 'Certificación docente en PDF', 'CONDICIONAL', TRUE, 'ITEM_HV'),
(11, 'HV-011', 'Experiencia',            'Experiencia investigativa', 'Grupo, Proyecto, Rol, Periodo', 'Certificación MinCiencias / Acta', 'OPCIONAL', TRUE, 'ITEM_HV'),
(12, 'HV-012', 'Licencias',              'Tarjeta profesional o matrícula', 'Numero, Entidad, Vigencia', 'Certificado de vigencia en PDF', 'CONDICIONAL', FALSE, 'ITEM_HV'),
(13, 'HV-013', 'Licencias',              'Otra licencia o acreditación técnica', 'Tipo, Numero, Expedicion', 'Licencia vigente en PDF', 'CONDICIONAL', TRUE, 'ITEM_HV'),
(14, 'HV-014', 'Educación continua',     'Cursos, diplomados o certificaciones', 'Nombre, Entidad, Horas, Fecha', 'Certificado en PDF', 'OPCIONAL', TRUE, 'ITEM_HV'),
(15, 'HV-015', 'Idiomas',                'Dominio de segunda lengua', 'Idioma, Nivel MCER', 'Certificado internacional en PDF', 'CONDICIONAL', TRUE, 'ITEM_HV'),
(16, 'HV-016', 'Competencias',           'Competencias digitales y técnicas', 'Competencia, Nivel', 'Certificado opcional', 'OPCIONAL', TRUE, 'ITEM_HV'),
(17, 'HV-017', 'Producción académica',   'Publicaciones científicas / artículos', 'Titulo, Revista, DOI, Año', 'Enlace DOI o PDF de publicación', 'OPCIONAL', TRUE, 'ITEM_HV'),
(18, 'HV-018', 'Producción académica',   'Libros o capítulos de libro', 'Titulo, Editorial, ISBN, Año', 'Página legal o certificación en PDF', 'OPCIONAL', TRUE, 'ITEM_HV'),
(19, 'HV-019', 'Reconocimientos',        'Premios y distinciones académicas', 'Nombre, Entidad, Fecha', 'Diploma o acto de distinción', 'OPCIONAL', TRUE, 'ITEM_HV'),
(20, 'HV-020', 'Referencias',            'Referencias laborales o profesionales', 'Nombre, Relacion, Telefono', 'Sin soporte inicial', 'CONDICIONAL', TRUE, 'REFERENCIA'),
(21, 'HV-021', 'Disponibilidad',         'Disponibilidad para vinculación', 'FechaDisponibilidad, Jornada', 'Sin soporte', 'CONDICIONAL', FALSE, 'ITEM_HV'),
(22, 'HV-022', 'Declaraciones',          'Declaración de veracidad juramentada', 'Aceptacion, Timestamp', 'Aceptación electrónica', 'OBLIGATORIO', FALSE, 'CONSENTIMIENTO'),
(23, 'HV-023', 'Específico cargo',       'Requisito institucional adicional', 'Nombre, Detalle', 'Documento en PDF', 'CONDICIONAL', TRUE, 'ITEM_HV'),
(24, 'HV-024', 'Seguridad social',       'Certificado de afiliación EPS/AFP', 'Entidad, Estado', 'Certificado vigente en PDF', 'CONDICIONAL', FALSE, 'ITEM_HV'),
(25, 'HV-025', 'Datos bancarios',        'Certificación bancaria', 'Banco, Tipo, Numero', 'Certificación bancaria en PDF', 'CONDICIONAL', FALSE, 'ITEM_HV'),
(26, 'HV-026', 'Antecedentes',           'Verificación antecedentes judiciales', 'Tipo, Fecha', 'Certificado Procuraduría/Policía', 'CONDICIONAL', FALSE, 'ITEM_HV');

-- =============================================================================
-- 6. CONVOCATORIAS, PERFILES Y REQUISITOS (RF-006 al RF-012, RN-002, RN-003)
-- Creadas inicialmente como PUBLICADAS con fechas vigentes para que los triggers
-- de postulación validen correctamente el periodo.
-- =============================================================================
INSERT INTO convocatoria (id_convocatoria, codigo, cargo, id_dependencia, id_facultad, id_programa, id_sede, id_periodo, vacantes, id_modalidad_contratacion, descripcion, fecha_apertura, fecha_cierre, estado, fecha_publicacion, id_creador) VALUES
(1,  'CONV-2026-001', 'Docente Asistente en Ingeniería de Software', 2, 1, 1, 1, 1, 2, 1, 'Orientar asignaturas de Arquitectura de Software y DevOps en el programa de Ingeniería de Sistemas.', '2026-10-01 08:00:00', '2026-11-30 23:59:59', 'PUBLICADA', '2026-10-01 08:00:00', 2),
(2,  'CONV-2026-002', 'Docente de Cátedra en Derecho Procesal Penal', 2, 4, 10, 1, 1, 1, 3, 'Orientar cátedra teórica y clínica jurídica en Sistema Penal Acusatorio.', '2026-10-05 08:00:00', '2026-11-28 23:59:59', 'PUBLICADA', '2026-10-05 08:00:00', 2),
(3,  'CONV-2026-003', 'Docente Investigador en Educación Infantil',  2, 2, 5, 1, 1, 1, 1, 'Liderar proyectos del grupo de investigación en Didácticas para la Primera Infancia.', '2026-09-15 08:00:00', '2026-11-25 23:59:59', 'PUBLICADA', '2026-09-15 08:00:00', 2),
(4,  'CONV-2026-004', 'Docente de Inglés Académico y Bilingüismo',  2, 2, 4, 2, 1, 2, 2, 'Cursos de inglés comunicativo niveles B1 y B2 para el Centro de Idiomas Montería.', '2026-10-10 08:00:00', '2026-12-05 23:59:59', 'PUBLICADA', '2026-10-10 08:00:00', 3),
(5,  'CONV-2026-005', 'Docente Medio Tiempo en Psicología Clínica',  2, 2, 6, 1, 1, 1, 2, 'Supervisión de prácticas clínicas y docencia en psicopatología infantil.', '2026-10-01 08:00:00', '2026-11-20 23:59:59', 'PUBLICADA', '2026-10-01 08:00:00', 2),
(6,  'CONV-2026-006', 'Profesional Universitario en Soporte Cloud',   4, NULL, NULL, 1, 1, 1, 4, 'Administración de servidores Linux, bases de datos MySQL y plataforma Moodle institucional.', '2026-10-15 08:00:00', '2026-12-10 23:59:59', 'PUBLICADA', '2026-10-15 08:00:00', 1),
(7,  'CONV-2026-007', 'Docente Cátedra en Taller de Diseño Urbano', 2, 1, 3, 1, 1, 1, 3, 'Orientación en taller de diseño urbano y proyectos bioclimáticos en Arquitectura.', '2026-10-01 08:00:00', '2026-11-30 23:59:59', 'PUBLICADA', '2026-10-01 08:00:00', 3),
(8,  'CONV-2026-008', 'Analista de Gestión de Talento Humano',       3, NULL, NULL, 1, 1, 1, 5, 'Gestión de planes de capacitación, clima organizacional y apoyo a procesos de selección.', '2026-09-01 08:00:00', '2026-10-15 23:59:59', 'PUBLICADA', '2026-09-01 08:00:00', 2),
(9,  'CONV-2026-009', 'Docente Tiempo Completo en Contabilidad NIIF',2, 3, 8, 3, 1, 1, 1, 'Orientación de cátedra NIIF para pymes y finanzas públicas en Centro Tutorial Corozal.', '2026-10-20 08:00:00', '2026-12-15 23:59:59', 'PUBLICADA', '2026-10-20 08:00:00', 3),
(10, 'CONV-2026-010', 'Docente Medio Tiempo en Emprendimiento e Innovación', 2, 3, 7, 1, 1, 1, 2, 'Orientar módulos de formulación de planes de negocio y modelo Canvas.', '2026-11-01 08:00:00', '2026-12-20 23:59:59', 'PUBLICADA', '2026-11-01 08:00:00', 2);

INSERT INTO perfil_cargo (id_convocatoria, formacion_requerida, experiencia_requerida, competencias, funciones, condiciones) VALUES
(1, 'Pregrado en Ingeniería de Sistemas, Computación o Afines. Título de Maestría en Ingeniería de Software o Ciencias de la Computación.', 'Mínimo 2 años de experiencia docente universitaria y 2 años de experiencia profesional en desarrollo de software.', 'Arquitectura Cloud, Docker, Kubernetes, Metodologías Ágiles (Scrum).', 'Orientar asignaturas teóricas y prácticas, dirigir trabajos de grado, formular proyectos de investigación.', 'Dedicación 40 horas semanales. Sede Sincelejo.'),
(2, 'Abogado graduado con Tarjeta Profesional vigente. Especialización o Maestría en Derecho Penal o Procesal Penal.', 'Mínimo 3 años de experiencia en litigio penal o rama judicial y 1 año de docencia universitaria.', 'Oratoria forense, argumentación jurídica, pedagogía activa.', 'Orientar cátedra de penal, coordinar audiencias simuladas.', 'Cátedra 10 horas semanales.'),
(3, 'Licenciado en Pedagogía Infantil o Educación Preescolar con Maestría en Educación.', 'Mínimo 2 años de docencia y experiencia acreditada en proyectos de investigación categorizados.', 'Formulación MinCiencias, redacción científica, liderazgo pedagógico.', 'Coordinar semillero de investigación, dictar cursos disciplinares.', 'Tiempo Completo.'),
(4, 'Licenciado en Lenguas Modernas o Filología con certificado C1 vigente (IELTS/TOEFL).', 'Mínimo 2 años de experiencia en enseñanza de inglés comunicativo para adultos.', 'Uso de laboratorios multimedia, metodologías comunicativas.', 'Orientar cursos presenciales en sede Montería.', 'Medio Tiempo.'),
(5, 'Psicólogo con Tarjeta Profesional y Especialización/Maestría en Psicología Clínica.', 'Mínimo 2 años de experiencia clínica y 1 año de supervisión de casos.', 'Evaluación diagnóstica, ética profesional, acompañamiento.', 'Supervisar consultorio psicológico y orientar cátedra clínica.', 'Medio Tiempo.'),
(6, 'Ingeniero de Sistemas o Telecomunicaciones.', 'Mínimo 2 años administrando servidores Linux, Nginx/Apache, MySQL y AWS/GCP.', 'Soporte DevOps, automatización bash/ansible, seguridad perimetral.', 'Monitorear servicios críticos, aplicar parches y soporte a usuarios.', 'Tiempo Completo.'),
(7, 'Arquitecto con Tarjeta Profesional (CPNAA) y Posgrado en Urbanismo.', 'Mínimo 3 años de diseño urbano y 1 año de docencia.', 'Manejo de AutoCAD, Revit, modelado BIM y sostenibilidad.', 'Docencia en taller urbano de quinto semestre.', 'Cátedra 12 horas.'),
(8, 'Profesional en Psicología, Administración o Ingeniería Industrial.', 'Mínimo 1 año de experiencia en áreas de talento humano o desarrollo organizacional.', 'Comunicación asertiva, manejo de Excel avanzado, servicio.', 'Apoyar el ciclo de inducción, bienestar y convocatorias.', 'Término Fijo 1 año.'),
(9, 'Contador Público con Tarjeta Profesional y Especialización en NIIF o Revisoría.', 'Mínimo 2 años de experiencia en asesoría contable y 1 año en docencia.', 'Manejo de software contable (Siigo/World Office), rigor normativo.', 'Docencia en sede Corozal.', 'Tiempo Completo.'),
(10, 'Administrador de Empresas o Economista con Maestría en Administración.', 'Mínimo 2 años en incubadoras de empresas o docencia de emprendimiento.', 'Pensamiento estratégico, innovación, design thinking.', 'Coordinar feria de emprendimiento y cursos transversales.', 'Medio Tiempo.');

-- Requisitos documentales para la Convocatoria 1 (Docente Ingeniería de Software)
INSERT INTO requisito_convocatoria (id_convocatoria, id_tipo_item, obligatoriedad, soporte_requerido, regla) VALUES
(1, 2,  'OBLIGATORIO', 'OBLIGATORIO', 'Cédula de ciudadanía legible por ambas caras'),
(1, 6,  'OBLIGATORIO', 'OBLIGATORIO', 'Título y acta de grado de Ingeniería de Sistemas o afín'),
(1, 7,  'OBLIGATORIO', 'OBLIGATORIO', 'Título y acta de Maestría afín a tecnologías de información'),
(1, 9,  'OBLIGATORIO', 'OBLIGATORIO', 'Certificaciones laborales que sumen mínimo 2 años en desarrollo'),
(1, 10, 'OBLIGATORIO', 'OBLIGATORIO', 'Certificaciones de docencia universitaria mínimo 2 años'),
(1, 12, 'OBLIGATORIO', 'OBLIGATORIO', 'Matrícula o tarjeta profesional COPNIA con vigencia'),
(1, 15, 'CONDICIONAL', 'OPCIONAL',    'Certificado de inglés B2 si posee acreditación');

-- Requisitos documentales para la Convocatoria 2 (Docente Derecho)
INSERT INTO requisito_convocatoria (id_convocatoria, id_tipo_item, obligatoriedad, soporte_requerido, regla) VALUES
(2, 2,  'OBLIGATORIO', 'OBLIGATORIO', 'Cédula de ciudadanía'),
(2, 6,  'OBLIGATORIO', 'OBLIGATORIO', 'Título y acta de Abogado'),
(2, 7,  'OBLIGATORIO', 'OBLIGATORIO', 'Título de Especialización o Maestría en Derecho Penal'),
(2, 9,  'OBLIGATORIO', 'OBLIGATORIO', 'Certificados de ejercicio profesional judicial mínimo 3 años'),
(2, 12, 'OBLIGATORIO', 'OBLIGATORIO', 'Tarjeta profesional vigente expedida por el CSJ');

-- Etapas del Proceso para la Convocatoria 1
INSERT INTO etapa_proceso (id_convocatoria, nombre, tipo, orden, obligatoria) VALUES
(1, 'Revisión y Conformidad Documental', 'REVISION_DOCUMENTAL', 1, TRUE),
(1, 'Evaluación de Hoja de Vida y Perfil', 'PRUEBAS', 2, TRUE),
(1, 'Prueba de Aula Docente Magistral',   'PRUEBAS', 3, TRUE),
(1, 'Entrevista con Comité de Facultad',  'ENTREVISTA', 4, TRUE),
(1, 'Decisión y Selección Final',         'DECISION_FINAL', 5, TRUE);

-- Criterios de Evaluación para la Convocatoria 1
INSERT INTO criterio_evaluacion (id_convocatoria, id_etapa, nombre, descripcion, puntaje_maximo, ponderacion, obligatorio) VALUES
(1, 2, 'Formación Académica y Títulos de Posgrado', 'Puntaje asignado a títulos de maestría y doctorado afines', 100.00, 25.00, TRUE),
(1, 2, 'Experiencia Docente e Investigativa',        'Años de docencia universitaria y productos de investigación', 100.00, 25.00, TRUE),
(1, 3, 'Dominio Pedagógico y Metodológico en Aula', 'Rúbrica de clase modelo evaluada por Desarrollo Profesoral', 100.00, 25.00, TRUE),
(1, 4, 'Entrevista de Competencias y Ajuste Institucional', 'Evaluación por Comité de Selección y Decanatura', 100.00, 25.00, TRUE);

-- =============================================================================
-- 7. POSTULACIONES DE CANDIDATOS (DIVERSIDAD DE ESTADOS)
-- Insertadas en BORRADOR para cumplir triggers de version_item y luego actualizadas
-- =============================================================================
INSERT INTO postulacion (id_postulacion, id_candidato, id_convocatoria, id_etapa_actual, estado, resultado_documental, fecha_inicio, fecha_envio) VALUES
(1,  1,  1, 4, 'BORRADOR', 'CONFORME', '2026-10-02 09:00:00', '2026-10-03 14:30:00'), -- Carlos Mendoza (Avanzará a SELECCIONADA)
(2,  3,  1, 3, 'BORRADOR', 'CONFORME', '2026-10-04 10:15:00', '2026-10-05 11:20:00'), -- Andrés Gómez (Avanzará a ENTREVISTADA)
(3,  7,  1, 2, 'BORRADOR', 'CONFORME', '2026-10-06 14:00:00', '2026-10-07 16:45:00'), -- Esteban Vargas (Avanzará a PRESELECCIONADA)
(4,  12, 1, 1, 'BORRADOR', 'OBSERVADO','2026-10-10 08:30:00', '2026-10-11 09:15:00'), -- Mónica Castillo (Avanzará a SUBSANACION_REQUERIDA)
(5,  5,  1, 1, 'BORRADOR', 'NO_CONFORME','2026-10-03 11:00:00', '2026-10-04 15:30:00'), -- Gustavo Martínez (Avanzará a NO_HABILITADA)
(6,  2,  2, 4, 'BORRADOR', 'CONFORME', '2026-10-06 10:00:00', '2026-10-07 12:00:00'), -- Laura Pérez (Avanzará a SELECCIONADA)
(7,  4,  4, 2, 'BORRADOR', 'CONFORME', '2026-10-12 11:20:00', '2026-10-13 14:10:00'), -- Valentina Arrieta (Avanzará a HABILITADA)
(8,  6,  7, 1, 'BORRADOR', 'PENDIENTE','2026-10-15 09:40:00', '2026-10-16 10:30:00'), -- Daniela Orozco (Avanzará a EN_REVISION)
(9,  8,  9, 1, 'BORRADOR', 'PENDIENTE','2026-10-22 15:00:00', '2026-10-23 16:20:00'), -- Mariana Duque (Avanzará a ENVIADA)
(10, 9,  4, 1, 'BORRADOR', 'PENDIENTE','2026-10-15 11:00:00', NULL),                  -- Felipe Álvarez (Se mantiene en BORRADOR)
(11, 10, 5, 1, 'BORRADOR', 'PENDIENTE','2026-10-08 14:30:00', '2026-10-09 10:15:00'), -- Paula Morales (Avanzará a RETIRADA)
(12, 11, 1, 1, 'BORRADOR', 'NO_CONFORME','2026-10-05 16:00:00', '2026-10-06 17:00:00'); -- Javier Ríos (Avanzará a NO_CONTINUA)

-- Aceptaciones de veracidad para las postulaciones enviadas
INSERT INTO consentimiento (id_candidato, id_postulacion, finalidad, version_texto, aceptado, ip) VALUES
(1,  1,  'VERACIDAD', 'v1.0_2026', TRUE, '186.84.90.15'),
(3,  2,  'VERACIDAD', 'v1.0_2026', TRUE, '181.143.22.40'),
(7,  3,  'VERACIDAD', 'v1.0_2026', TRUE, '186.84.90.17'),
(12, 4,  'VERACIDAD', 'v1.0_2026', TRUE, '186.84.90.19'),
(5,  5,  'VERACIDAD', 'v1.0_2026', TRUE, '190.157.30.22'),
(2,  6,  'VERACIDAD', 'v1.0_2026', TRUE, '186.84.90.16'),
(4,  7,  'VERACIDAD', 'v1.0_2026', TRUE, '181.143.22.41'),
(6,  8,  'VERACIDAD', 'v1.0_2026', TRUE, '190.157.30.23'),
(8,  9,  'VERACIDAD', 'v1.0_2026', TRUE, '181.143.22.42'),
(10, 11, 'VERACIDAD', 'v1.0_2026', TRUE, '181.143.22.43'),
(11, 12, 'VERACIDAD', 'v1.0_2026', TRUE, '190.157.30.24');

-- =============================================================================
-- 8. ÍTEMS DE HOJA DE VIDA, VERSIONES Y SOPORTES FÍSICOS
-- =============================================================================
-- Ítems para Carlos Mendoza (Postulación 1)
INSERT INTO item_hv (id_item_hv, id_postulacion, id_tipo_item, estado_revision) VALUES
(1, 1, 2,  'VERIFICADO'), -- Cédula
(2, 1, 6,  'VERIFICADO'), -- Pregrado
(3, 1, 7,  'VERIFICADO'), -- Posgrado
(4, 1, 9,  'VERIFICADO'), -- Exp laboral
(5, 1, 10, 'VERIFICADO'), -- Exp docente
(6, 1, 12, 'VERIFICADO'), -- Tarjeta prof
-- Ítems para Mónica Castillo (Postulación 4 - Sujeto a subsanación)
(7, 4, 2,  'VERIFICADO'),
(8, 4, 6,  'VERIFICADO'),
(9, 4, 7,  'OBSERVADO'),  -- Acta ilegible
(10,4, 9,  'VERIFICADO'),
-- Ítems para Gustavo Martínez (Postulación 5 - No cumple)
(11, 5, 2, 'VERIFICADO'),
(12, 5, 6, 'RECHAZADO');  -- Título no afín

-- Versiones JSON de los ítems
INSERT INTO version_item (id_version_item, id_item_hv, numero_version, datos) VALUES
(1, 1, 1, '{"tipo_documento":"CC","numero":"1102845920","lugar_expedicion":"Sincelejo"}'),
(2, 2, 1, '{"institucion":"Universidad de Sucre","programa":"Ingeniería de Sistemas","titulo":"Ingeniero de Sistemas","fecha_grado":"2018-03-23","acta_numero":"1045"}'),
(3, 3, 1, '{"institucion":"Universidad del Norte","nivel":"MAESTRIA","programa":"Maestría en Ingeniería de Sistemas y Computación","titulo":"Magíster en Ingeniería de Sistemas","fecha_grado":"2022-07-15"}'),
(4, 4, 1, '{"empresa":"Soluciones Cloud SAS","cargo":"Líder Técnico de Desarrollo","fecha_inicio":"2019-02-01","fecha_fin":"2024-08-30","funciones":"Diseño de microservicios en PHP y arquitectura AWS"}'),
(5, 5, 1, '{"institucion":"CECAR","asignaturas":"Programación Web, Bases de Datos","horas_semanales":12,"fecha_inicio":"2022-08-01","fecha_fin":"2026-06-30"}'),
(6, 6, 1, '{"numero_matricula":"SU205-18459","entidad_emisora":"COPNIA","fecha_expedicion":"2018-05-10","vigente":true}'),
(7, 7, 1, '{"tipo_documento":"CC","numero":"1103847291","lugar_expedicion":"Sincelejo"}'),
(8, 8, 1, '{"institucion":"Universidad de Sucre","programa":"Ingeniería de Sistemas","titulo":"Ingeniera de Sistemas","fecha_grado":"2019-09-12"}'),
(9, 9, 1, '{"institucion":"Universidad Nacional","nivel":"MAESTRIA","programa":"Maestría en Computación","fecha_grado":"2023-11-20"}'),
(10, 10, 1, '{"empresa":"SoftCaribe Ltda","cargo":"Desarrolladora Senior","fecha_inicio":"2020-01-15","fecha_fin":"2025-12-31"}'),
(11, 11, 1, '{"tipo_documento":"CC","numero":"73158942","lugar_expedicion":"Sincelejo"}'),
(12, 12, 1, '{"institucion":"Universidad Popular del Cesar","programa":"Licenciatura en Educación Física","titulo":"Licenciado","fecha_grado":"2010-06-18"}');

-- Soportes asociados en PDF
INSERT INTO soporte (id_soporte, id_version_item, nombre_original, nombre_almacenado, mime, tamano_bytes, hash_sha256, ruta_segura) VALUES
(1, 1, 'cedula_carlos_mendoza.pdf', 'sop_cc_mendoza_20261002_a1b2c3.pdf', 'application/pdf', 1048576, SHA2('sop_cc_mendoza_2026', 256), 'storage/soportes/post_1/sop_cc_mendoza_20261002_a1b2c3.pdf'),
(2, 2, 'diploma_pregrado_sistemas.pdf', 'sop_pre_mendoza_20261002_d4e5f6.pdf', 'application/pdf', 2097152, SHA2('sop_pre_mendoza_2026', 256), 'storage/soportes/post_1/sop_pre_mendoza_20261002_d4e5f6.pdf'),
(3, 3, 'acta_maestria_uninorte.pdf', 'sop_pos_mendoza_20261002_g7h8i9.pdf', 'application/pdf', 2450000, SHA2('sop_pos_mendoza_2026', 256), 'storage/soportes/post_1/sop_pos_mendoza_20261002_g7h8i9.pdf'),
(4, 4, 'cert_laboral_soluciones_cloud.pdf', 'sop_lab_mendoza_20261002_j1k2l3.pdf', 'application/pdf', 850000, SHA2('sop_lab_mendoza_2026', 256), 'storage/soportes/post_1/sop_lab_mendoza_20261002_j1k2l3.pdf'),
(5, 5, 'cert_docencia_cecar.pdf', 'sop_doc_mendoza_20261002_m4n5o6.pdf', 'application/pdf', 920000, SHA2('sop_doc_mendoza_2026', 256), 'storage/soportes/post_1/sop_doc_mendoza_20261002_m4n5o6.pdf'),
(6, 6, 'tarjeta_profesional_copnia.pdf', 'sop_tp_mendoza_20261002_p7q8r9.pdf', 'application/pdf', 640000, SHA2('sop_tp_mendoza_2026', 256), 'storage/soportes/post_1/sop_tp_mendoza_20261002_p7q8r9.pdf'),
(7, 9, 'acta_posgrado_escaneada_borrosa.pdf', 'sop_pos_castillo_20261010_s1t2u3.pdf', 'application/pdf', 512000, SHA2('sop_pos_castillo_2026', 256), 'storage/soportes/post_4/sop_pos_castillo_20261010_s1t2u3.pdf'),
(8, 12, 'diploma_edufisica.pdf', 'sop_pre_martinez_20261003_v4w5x6.pdf', 'application/pdf', 1800000, SHA2('sop_pre_martinez_2026', 256), 'storage/soportes/post_5/sop_pre_martinez_20261003_v4w5x6.pdf');

-- Actualización controlada de los estados reales de postulación
UPDATE postulacion SET estado = 'SELECCIONADA', id_etapa_actual = 5 WHERE id_postulacion = 1;
UPDATE postulacion SET estado = 'ENTREVISTADA',  id_etapa_actual = 4 WHERE id_postulacion = 2;
UPDATE postulacion SET estado = 'PRESELECCIONADA', id_etapa_actual = 3 WHERE id_postulacion = 3;
UPDATE postulacion SET estado = 'SUBSANACION_REQUERIDA', id_etapa_actual = 1 WHERE id_postulacion = 4;
UPDATE postulacion SET estado = 'NO_HABILITADA', id_etapa_actual = 1 WHERE id_postulacion = 5;
UPDATE postulacion SET estado = 'SELECCIONADA', id_etapa_actual = 5 WHERE id_postulacion = 6;
UPDATE postulacion SET estado = 'HABILITADA',   id_etapa_actual = 2 WHERE id_postulacion = 7;
UPDATE postulacion SET estado = 'EN_REVISION',  id_etapa_actual = 1 WHERE id_postulacion = 8;
UPDATE postulacion SET estado = 'ENVIADA',      id_etapa_actual = 1 WHERE id_postulacion = 9;
UPDATE postulacion SET estado = 'RETIRADA',     id_etapa_actual = 1 WHERE id_postulacion = 11;
UPDATE postulacion SET estado = 'NO_CONTINUA',  id_etapa_actual = 1 WHERE id_postulacion = 12;

-- =============================================================================
-- 9. REVISIÓN DOCUMENTAL Y SUBSANACIÓN (RF-045 al RF-053)
-- =============================================================================
INSERT INTO revision_item (id_revision, id_version_item, id_revisor, resultado, id_motivo, observacion) VALUES
(1, 1, 4, 'APROBADO', NULL, 'Cédula de ciudadanía legible y vigente.'),
(2, 2, 4, 'APROBADO', NULL, 'Título y acta de grado verificados en sistema SNIES.'),
(3, 3, 4, 'APROBADO', NULL, 'Título de maestría acreditado con acta legalizada.'),
(4, 4, 4, 'APROBADO', NULL, 'Certificado cumple con 5 años de experiencia técnica.'),
(5, 5, 4, 'APROBADO', NULL, 'Acredita 4 años de docencia en CECAR.'),
(6, 6, 4, 'APROBADO', NULL, 'Matrícula COPNIA verificada en portal oficial.'),
(7, 9, 5, 'OBSERVADO', 1,    'El soporte del acta de grado presenta baja resolución y está cortado en las firmas.'),
(8, 12, 4, 'RECHAZADO', 5,   'El título aportado (Educación Física) no corresponde al área de Ingeniería requerida.');

INSERT INTO solicitud_subsanacion (id_solicitud, id_postulacion, id_solicitante, fecha_solicitud, fecha_limite, estado) VALUES
(1, 4, 2, '2026-10-12 10:00:00', '2026-11-20 23:59:59', 'VIGENTE');

INSERT INTO detalle_subsanacion (id_detalle, id_solicitud, id_item_hv, id_revision, motivo, estado) VALUES
(1, 1, 9, 7, 'Por favor cargue nuevamente el acta de grado de maestría en un archivo PDF nítido y completo.', 'PENDIENTE');

-- =============================================================================
-- 10. PRESELECCIÓN, EVALUACIONES Y PRUEBAS (RF-054 al RF-058, RF-087, RF-089)
-- =============================================================================
INSERT INTO preseleccion (id_preseleccion, id_postulacion, id_usuario, fecha, fundamento, es_excepcion, estado) VALUES
(1, 1, 2, '2026-10-08 09:00:00', 'Expediente 100% conforme. Cumple requisitos de maestría y experiencia docente.', FALSE, 'VIGENTE'),
(2, 2, 2, '2026-10-08 09:30:00', 'Expediente conforme. Acredita formación y experiencia investigativa requerida.', FALSE, 'VIGENTE'),
(3, 3, 2, '2026-10-09 11:00:00', 'Expediente conforme. Acreditación de experiencia técnica relevante.', FALSE, 'VIGENTE'),
(4, 6, 2, '2026-10-10 14:00:00', 'Expediente conforme en Derecho Penal. Experiencia judicial comprobada.', FALSE, 'VIGENTE'),
(5, 7, 2, '2026-10-14 15:30:00', 'Expediente conforme en Licenciatura en Inglés. Certificado C1 acreditado.', FALSE, 'VIGENTE');

-- Evaluaciones de perfil (RF-054, RF-055)
INSERT INTO evaluacion (id_postulacion, id_criterio, id_evaluador, resultado, puntaje, observacion) VALUES
(1, 1, 6, 'PUNTUADO', 95.00, 'Maestría en Computación directamente afín a la vacante.'),
(1, 2, 6, 'PUNTUADO', 90.00, 'Más de 3 años de experiencia docente demostrada.'),
(2, 1, 6, 'PUNTUADO', 85.00, 'Maestría en curso de finalización con soporte de créditos.'),
(2, 2, 6, 'PUNTUADO', 88.00, 'Experiencia docente en cátedras afines.');

-- Pruebas y Resultados (RF-087, RF-088)
INSERT INTO prueba (id_prueba, id_convocatoria, tipo, nombre, descripcion, fecha_aplicacion, id_responsable) VALUES
(1, 1, 'CONOCIMIENTO', 'Prueba Técnica de Algoritmos y Arquitectura', 'Evaluación práctica de patrones de diseño y microservicios', '2026-10-18 10:00:00', 6),
(2, 1, 'PSICOTECNICA', 'Prueba de Competencias Docentes y Psicométricas', 'Batería 16PF y competencias comunicativas', '2026-10-19 14:00:00', 3);

INSERT INTO resultado_prueba (id_prueba, id_postulacion, calificacion, dictamen, observacion, soporte_evidencia) VALUES
(1, 1, 92.50, 'APROBADO', 'Excelente dominio técnico de contenedores y backend PHP/MySQL.', 'storage/evidencias/pruebas/res_prueba1_post1.pdf'),
(2, 1, 88.00, 'APROBADO', 'Apto con alto nivel de liderazgo y trabajo colaborativo.', 'storage/evidencias/pruebas/res_psico_post1.pdf'),
(1, 2, 78.00, 'APROBADO', 'Demuestra fundamentos adecuados de desarrollo web.', 'storage/evidencias/pruebas/res_prueba1_post2.pdf');

-- Prueba de aula docente (RF-089)
INSERT INTO prueba_aula (id_postulacion, tema, rubrica, id_evaluador, puntaje, dictamen, observaciones, soporte_acta) VALUES
(1, 'Arquitectura de Microservicios orientada a Eventos', 'Rúbrica CECAR Formato DP-04: Manejo del tiempo (20%), Didáctica (30%), Dominio del tema (30%), Recursos (20%)', 8, 96.00, 'APROBADA', 'Excelente desenvolvimiento en aula, interacción didáctica sobresaliente con los estudiantes.', 'storage/actas/aula/acta_aula_carlos_mendoza.pdf'),
(2, 'Patrones Arquitectónicos en Aplicaciones Distribuidas', 'Rúbrica CECAR Formato DP-04', 8, 84.00, 'APROBADA', 'Buen dominio disciplinar, sugerencia de incorporar más dinámicas activas.', 'storage/actas/aula/acta_aula_andres_gomez.pdf');

-- =============================================================================
-- 11. COMITÉS DE ENTREVISTAS (RF-061 al RF-071, RN-017, RN-018)
-- =============================================================================
INSERT INTO franja_entrevista (id_franja, id_convocatoria, id_etapa, inicio, fin, cupos, modalidad, recurso) VALUES
(1, 1, 4, '2026-10-25 08:00:00', '2026-10-25 12:00:00', 4, 'PRESENCIAL', 'Sala de Juntas - Facultad de Ingenierías (Piso 2)'),
(2, 1, 4, '2026-10-25 14:00:00', '2026-10-25 17:00:00', 3, 'PRESENCIAL', 'Sala de Juntas - Facultad de Ingenierías (Piso 2)'),
(3, 2, 4, '2026-10-26 09:00:00', '2026-10-26 12:00:00', 3, 'PRESENCIAL', 'Decanatura de Derecho - Campus Sincelejo');

INSERT INTO entrevista (id_entrevista, id_postulacion, id_etapa, id_franja, inicio, fin, modalidad, ubicacion, estado, confirmacion_candidato) VALUES
(1, 1, 4, 1, '2026-10-25 08:30:00', '2026-10-25 09:30:00', 'PRESENCIAL', 'Sala de Juntas - Facultad de Ingenierías (Piso 2)', 'REALIZADA', 'CONFIRMADA'),
(2, 2, 4, 1, '2026-10-25 09:45:00', '2026-10-25 10:45:00', 'PRESENCIAL', 'Sala de Juntas - Facultad de Ingenierías (Piso 2)', 'REALIZADA', 'CONFIRMADA'),
(3, 6, 4, 3, '2026-10-26 09:30:00', '2026-10-26 10:30:00', 'PRESENCIAL', 'Decanatura de Derecho - Campus Sincelejo',         'REALIZADA', 'CONFIRMADA');

INSERT INTO entrevistador (id_entrevistador, id_entrevista, id_usuario, rol_comite, estado) VALUES
(1, 1, 2, 'PRESIDE', 'CONFIRMADO'), -- Dra. María Claudia Montes
(2, 1, 6, 'MIEMBRO', 'CONFIRMADO'), -- Mg. Álvaro Berrío (Área técnica)
(3, 2, 2, 'PRESIDE', 'CONFIRMADO'),
(4, 2, 6, 'MIEMBRO', 'CONFIRMADO'),
(5, 3, 2, 'PRESIDE', 'CONFIRMADO'),
(6, 3, 7, 'MIEMBRO', 'CONFIRMADO'); -- Dra. Carmen Paternina

-- Calificaciones de entrevista independientes por miembro (RN-018)
INSERT INTO resultado_entrevista (id_entrevista, id_evaluador, tipo, puntaje, concepto, observacion) VALUES
(1, 2, 'INDIVIDUAL', 95.00, 'FAVORABLE', 'Excelente presentación, claridad de metas y motivación de pertenencia a CECAR.'),
(1, 6, 'INDIVIDUAL', 98.00, 'FAVORABLE', 'Sólido perfil técnico y disponibilidad inmediata para vinculación.'),
(1, NULL, 'CONSOLIDADO', 96.50, 'FAVORABLE', 'Candidato recomendado unánimemente por el comité.'),
(2, 2, 'INDIVIDUAL', 85.00, 'FAVORABLE', 'Buen candidato con potencial de crecimiento académico.'),
(2, 6, 'INDIVIDUAL', 82.00, 'FAVORABLE', 'Alineado con los objetivos del programa.');

-- =============================================================================
-- 12. CIERRE DE ETAPA, DECISIÓN FINAL Y TRAZABILIDAD (RF-059, RF-073, RF-093)
-- =============================================================================
INSERT INTO resultado_etapa (id_postulacion, id_etapa, dictamen, observacion, id_motivo_descarte, id_responsable) VALUES
(1, 1, 'APROBADO', 'Documentación completa y verificada con éxito.', NULL, 4),
(1, 2, 'APROBADO', 'Superó evaluación de perfil con 92.5 puntos.', NULL, 6),
(1, 3, 'APROBADO', 'Clase magistral aprobada con 96.0 puntos.', NULL, 8),
(1, 4, 'APROBADO', 'Entrevista favorable con promedio 96.5 puntos.', NULL, 2),
(5, 1, 'NO_APROBADO', 'El título profesional presentado no es afín a las ciencias de la computación.', 5, 4);

INSERT INTO decision_final (id_decision, id_postulacion, resultado, fundamento, id_usuario, fecha_decision, fecha_publicacion) VALUES
(1, 1, 'SELECCIONADO',    'Candidato con el mayor puntaje consolidado (94.8/100). Cumple a cabalidad con el perfil de Ingeniero de Software.', 2, '2026-10-28 11:00:00', '2026-10-28 14:00:00'),
(2, 2, 'ELEGIBLE',        'Superó todas las fases satisfactoriamente. Se ubica en segundo lugar de elegibilidad.', 2, '2026-10-28 11:15:00', '2026-10-28 14:00:00'),
(3, 5, 'NO_SELECCIONADO', 'No habilitado documentalmente por incompatibilidad del título académico con el perfil convocado.', 2, '2026-10-06 16:00:00', '2026-10-06 17:00:00'),
(4, 6, 'SELECCIONADO',    'Ganadora de la convocatoria docente en Derecho Procesal Penal con promedio 93.0.', 2, '2026-10-29 10:00:00', '2026-10-29 11:30:00');

-- Registro en la Línea de Tiempo (Movimientos Funcionales)
INSERT INTO movimiento (id_postulacion, id_usuario, accion, estado_anterior, estado_nuevo, id_motivo, detalle, visible_candidato, fecha_hora) VALUES
(1, 9, 'ENVIO_POSTULACION', 'BORRADOR', 'ENVIADA', NULL, 'El candidato completó el envío formal de su postulación.', TRUE, '2026-10-03 14:30:00'),
(1, 4, 'CONFORMIDAD_DOC',   'ENVIADA', 'EN_REVISION', NULL, 'Expediente asignado a revisión documental.', FALSE, '2026-10-04 09:00:00'),
(1, 4, 'APROBACION_DOC',    'EN_REVISION', 'HABILITADA', NULL, 'Expediente documental declarado Conforme.', TRUE, '2026-10-05 16:30:00'),
(1, 2, 'PRESELECCION',      'HABILITADA', 'PRESELECCIONADA', NULL, 'Candidato autorizado para programación de pruebas.', TRUE, '2026-10-08 09:00:00'),
(1, 2, 'CITACION_ENTREVISTA','PRESELECCIONADA', 'ENTREVISTADA', NULL, 'Entrevista realizada con el comité de facultad.', TRUE, '2026-10-25 10:00:00'),
(1, 2, 'DECISION_FINAL',    'ENTREVISTADA', 'SELECCIONADA', NULL, 'Candidato seleccionado para la vacante.', TRUE, '2026-10-28 14:00:00'),
(4, 2, 'SOLICITUD_SUBSANA', 'EN_REVISION', 'SUBSANACION_REQUERIDA', 1, 'Se requiere subsanar acta de grado ilegible.', TRUE, '2026-10-12 10:00:00'),
(5, 4, 'DESCARTE_DOCUMENTAL','EN_REVISION', 'NO_HABILITADA', 5, 'No cumple titulación mínima afín.', TRUE, '2026-10-06 16:00:00');

-- Notificaciones transaccionales
INSERT INTO notificacion (id_destinatario, id_postulacion, canal, asunto, mensaje, leida_en, estado_envio) VALUES
(9, 1, 'PORTAL', 'Confirmación de Postulación Exitosa', 'Su postulación a la vacante CONV-2026-001 ha sido radicada oficialmente con éxito.', '2026-10-03 15:00:00', 'ENVIADA'),
(9, 1, 'CORREO', 'Citación a Entrevista - Convocatoria Docente', 'Se le ha programado entrevista para el 25 de octubre a las 08:30 AM en la Sala de Juntas.', '2026-10-20 09:00:00', 'ENVIADA'),
(9, 1, 'PORTAL', 'Publicación de Resultados Finales', 'Felicitaciones, ha sido SELECCIONADO en la convocatoria CONV-2026-001.', '2026-10-28 14:10:00', 'ENVIADA'),
(12,4, 'PORTAL', 'Requerimiento de Subsanación Documental', 'Tiene un requerimiento pendiente de subsanación con fecha límite al 20 de noviembre.', '2026-10-12 10:30:00', 'ENVIADA'),
(13,5, 'PORTAL', 'Notificación de Estado en Convocatoria', 'Le informamos que su postulación no continúa en el proceso por causales de perfil.', '2026-10-06 16:30:00', 'ENVIADA');

-- =============================================================================
-- 13. PLANES DE TRABAJO Y TICKETS DE MESA DE AYUDA (RF-091, RF-099)
-- =============================================================================
INSERT INTO plan_trabajo (id_plan, id_convocatoria, id_responsable, fecha_inicio, fecha_estimada_fin, estado) VALUES
(1, 1, 2, '2026-10-01', '2026-11-15', 'COMPLETADO'),
(2, 2, 2, '2026-10-05', '2026-11-20', 'EN_CURSO');

INSERT INTO actividad_plan (id_actividad, id_plan, nombre, id_responsable, fecha_limite, completada, fecha_completada) VALUES
(1, 1, 'Revisión y preselección documental de aspirantes', 4, '2026-10-10', TRUE, '2026-10-09 17:00:00'),
(2, 1, 'Coordinación y aplicación de pruebas de aula docente', 8, '2026-10-22', TRUE, '2026-10-21 16:00:00'),
(3, 1, 'Jornada de entrevistas con comité de Decanatura', 2, '2026-10-26', TRUE, '2026-10-25 18:00:00'),
(4, 1, 'Publicación de actas y resolución de seleccionados', 2, '2026-10-30', TRUE, '2026-10-28 15:00:00'),
(5, 2, 'Cotejo de antecedentes y licencias profesionales CSJ', 5, '2026-11-15', FALSE, NULL);

INSERT INTO ticket_ayuda (id_ticket, id_usuario, categoria, asunto, mensaje, estado) VALUES
(1, 9,  'SOPORTES', 'Duda sobre formato de certificaciones laborales', '¿Las certificaciones laborales deben tener vigencia no mayor a 30 días?', 'CERRADO'),
(2, 12, 'PROBLEMA_TECNICO', 'Error al visualizar PDF adjunto', 'No me carga la previsualización del diploma desde el navegador móvil.', 'CERRADO'),
(3, 15, 'DUDA_CONVOCATORIA', 'Consulta sobre experiencia docente requerida', '¿Se convalida docencia secundaria para el perfil universitario?', 'EN_ATENCION');

INSERT INTO respuesta_ticket (id_respuesta, id_ticket, id_usuario, mensaje) VALUES
(1, 1, 3, 'Apreciado candidato: las certificaciones laborales deben indicar funciones y fechas claras, preferiblemente con vigencia inferior a 90 días.'),
(2, 2, 1, 'Estimada Mónica: se optimizó el visor en móvil. Le recomendamos limpiar caché o abrir desde Chrome.');

-- =============================================================================
-- 14. DATOS DEMO FASE 2: FORMALIZACIÓN Y VINCULACIÓN (RF-101 al RF-106)
-- Demuestra el flujo de contratación del candidato seleccionado Carlos Mendoza
-- =============================================================================
INSERT INTO examen_ocupacional (id_postulacion, proveedor_salud, fecha_programada, fecha_concepto, concepto_aptitud, observaciones_sst, id_responsable_sst) VALUES
(1, 'IPS Salud Ocupacional de Sucre SAS', '2026-11-02', '2026-11-03', 'APTO', 'Apto laboral para docencia universitaria sin restricciones.', 1);

INSERT INTO contratacion (id_contratacion, id_postulacion, estado_formalizacion, fecha_inicio_labores, id_responsable_gh, observaciones) VALUES
(1, 1, 'FORMALIZADO', '2026-11-15', 2, 'Documentación de vinculación completa y validada por Asesoría Jurídica.');

INSERT INTO documento_contratacion (id_contratacion, tipo_documento, ruta_archivo, estado_verificacion) VALUES
(1, 'RUT Actualizado', 'storage/contratacion/post_1/rut_carlos_mendoza.pdf', 'VALIDO'),
(1, 'Certificación Bancaria', 'storage/contratacion/post_1/banco_carlos_mendoza.pdf', 'VALIDO'),
(1, 'Contrato de Trabajo Firmado', 'storage/contratacion/post_1/contrato_firmado_carlos_mendoza.pdf', 'VALIDO');

INSERT INTO vinculacion (id_contratacion, codigo_colaborador, fecha_vinculacion, estado, id_autorizado_por) VALUES
(1, 'EMP-2026-0482', '2026-11-15', 'ACTIVO', 2);

INSERT INTO integracion_hv (id_postulacion, sistema_destino, id_externo_creado, estado, fecha_transferencia, reintentos) VALUES
(1, 'SISTEMA_TALENTO_CECAR', 'COLAB-9482', 'TRANSFERIDO', '2026-11-15 08:30:00', 0);

INSERT INTO bitacora_integracion (id_integracion, payload_enviado, respuesta_recibida, codigo_http) VALUES
(1, '{"identificacion":"1102845920","codigo":"EMP-2026-0482","cargo":"Docente Asistente"}', '{"status":"OK","id_empleado":9482,"mensaje":"Colaborador registrado exitosamente"}', 200);

-- Actualizar la postulación a VINCULADO formalmente
UPDATE postulacion SET estado = 'VINCULADO' WHERE id_postulacion = 1;

-- =============================================================================
-- 15. ACTUALIZACIÓN FINAL DE CONVOCATORIAS
-- =============================================================================
-- Convocatoria 8 cerrada por cumplimiento del cronograma
UPDATE convocatoria SET estado = 'CERRADA' WHERE id_convocatoria = 8;


-- =============================================================================
-- 16. CONSULTAS SELECT DE VERIFICACIÓN Y AUDITORÍA DE CONSISTENCIA
-- =============================================================================

-- 1. Verificación de conteo en tablas principales
SELECT 'usuario' AS tabla, COUNT(*) AS total_registros FROM usuario
UNION ALL SELECT 'candidato', COUNT(*) FROM candidato
UNION ALL SELECT 'convocatoria', COUNT(*) FROM convocatoria
UNION ALL SELECT 'postulacion', COUNT(*) FROM postulacion
UNION ALL SELECT 'item_hv', COUNT(*) FROM item_hv
UNION ALL SELECT 'version_item', COUNT(*) FROM version_item
UNION ALL SELECT 'soporte', COUNT(*) FROM soporte
UNION ALL SELECT 'entrevista', COUNT(*) FROM entrevista
UNION ALL SELECT 'movimiento', COUNT(*) FROM movimiento
UNION ALL SELECT 'notificacion', COUNT(*) FROM notificacion;

-- 2. Distribución de postulaciones por estado
SELECT estado, resultado_documental, COUNT(*) AS cantidad_postulaciones
FROM postulacion
GROUP BY estado, resultado_documental
ORDER BY cantidad_postulaciones DESC;

-- 3. Verificación de vista de seleccionados
SELECT * FROM v_seleccionados;

-- 4. Verificación de vista de aspirantes en proceso
SELECT * FROM v_en_proceso;

-- 5. Verificación de vista de aspirantes no aprobados con causal estandarizada
SELECT * FROM v_no_aprobados;

-- 6. Métricas del Dashboard por convocatoria (RF-086)
SELECT * FROM v_dashboard_indicadores;

-- ============================================================================
-- PLATAFORMA DE CONTRATACIÓN Y SELECCIÓN DE PERSONAL - CECAR
-- Datos Iniciales / Semilla para Demostración y Pruebas
-- ============================================================================

-- Roles
INSERT OR IGNORE INTO roles (id, nombre, descripcion) VALUES
('admin', 'Administrador del Sistema', 'Control total de configuración, usuarios y catálogos'),
('talento_humano', 'Gestor de Talento Humano', 'Administración de convocatorias, expedientes y contrataciones'),
('revisor', 'Revisor Documental', 'Verificación de requisitos y soportes probatorios'),
('entrevistador', 'Comité / Entrevistador', 'Evaluación de candidatos y pruebas técnicas'),
('desarrollo_profesoral', 'Desarrollo Profesoral', 'Evaluación de pruebas de aula pedagógicas'),
('candidato', 'Aspirante / Candidato', 'Registro de hoja de vida, postulación y subsanaciones'),
('auditor', 'Auditor de Control Interno', 'Supervisión de trazabilidad, equidad y procesos');

-- Dependencias
INSERT OR IGNORE INTO dependencias (id, nombre, sigla, facultad, sede, activa) VALUES
(1, 'Facultad de Educación y Ciencias Sociales', 'FECS', 'Facultad de Educación y Ciencias Sociales', 'Sincelejo', 1),
(2, 'Facultad de Ciencias Económicas y Administrativas', 'FACEA', 'Facultad de Ciencias Económicas y Administrativas', 'Sincelejo', 1),
(3, 'Facultad de Ciencias Básicas, Ingeniería y Arquitectura', 'FCBIA', 'Facultad de Ciencias Básicas, Ingeniería y Arquitectura', 'Sincelejo', 1),
(4, 'Facultad de Derecho y Ciencias Políticas', 'FDCP', 'Facultad de Derecho y Ciencias Políticas', 'Sincelejo', 1),
(5, 'Dirección de Gestión de Talento Humano', 'DGTH', 'Administración Central', 'Sincelejo', 1);

-- Usuarios de Prueba
-- Contraseñas:
-- admin@cecar.edu.co -> admin123
-- talento.humano@cecar.edu.co -> talento123
-- revisor@cecar.edu.co -> talento123
-- evaluador@cecar.edu.co -> talento123
-- candidato@correo.com -> candidato123
-- maria.gomez@correo.com -> candidato123
-- carlos.ruiz@correo.com -> candidato123

INSERT OR IGNORE INTO usuarios (id, nombres, apellidos, tipo_documento, numero_documento, correo, telefono, ciudad, direccion, password_hash, rol, dependencia_id, cargo_interno, activo) VALUES
(1, 'Administrador', 'CECAR Institucional', 'CC', '1000000001', 'admin@cecar.edu.co', '3001234567', 'Sincelejo', 'Campus CECAR', '$2y$10$y/QnV44AQjE1Lrlhg/MnqeYU7u0VscOotsTTO2O8v2iZH9tWYVMj2', 'admin', 5, 'Administrador del Sistema', 1),
(2, 'Laura Marcela', 'Montes Salcedo', 'CC', '1000000002', 'talento.humano@cecar.edu.co', '3009876543', 'Sincelejo', 'Campus CECAR Edificio A', '$2y$10$Ulp0lTC9/OdbFc697RBY6eF9OeW/ErBJ5zLf7PpculMXoIzIbZrya', 'talento_humano', 5, 'Coordinadora de Selección y Contratación', 1),
(3, 'Ricardo Andrés', 'Sierra Vergara', 'CC', '1000000003', 'revisor@cecar.edu.co', '3104567890', 'Sincelejo', 'Campus CECAR Edificio B', '$2y$10$Ulp0lTC9/OdbFc697RBY6eF9OeW/ErBJ5zLf7PpculMXoIzIbZrya', 'revisor', 5, 'Analista de Verificación Documental', 1),
(4, 'Dra. Carmen', 'Alvis Mendoza', 'CC', '1000000004', 'evaluador@cecar.edu.co', '3157890123', 'Sincelejo', 'Facultad de Educación', '$2y$10$Ulp0lTC9/OdbFc697RBY6eF9OeW/ErBJ5zLf7PpculMXoIzIbZrya', 'entrevistador', 1, 'Decana y Miembro Comité Evaluador', 1),
(5, 'Prof. Hernán', 'Pérez Paternina', 'CC', '1000000005', 'docencia@cecar.edu.co', '3166543210', 'Sincelejo', 'Desarrollo Profesoral', '$2y$10$Ulp0lTC9/OdbFc697RBY6eF9OeW/ErBJ5zLf7PpculMXoIzIbZrya', 'desarrollo_profesoral', 1, 'Líder de Evaluación de Aula Docente', 1),
(6, 'Aspirante Nuevo a', 'docente cátedra', 'CC', '1102839281', 'candidato@correo.com', '3205558899', 'Sincelejo', 'Barrio Venecia Calle 24 # 18-40', '$2y$10$moGhkr.D.R4yGKSipY4M8ePoiJ/Ly2shdAojPqt.uhZVS0pllpqPy', 'candidato', NULL, NULL, 1),
(7, 'María Elena', 'Gómez Arrieta', 'CC', '1102839282', 'maria.gomez@correo.com', '3114443322', 'Sincelejo', 'Calle 19 # 21-12', '$2y$10$moGhkr.D.R4yGKSipY4M8ePoiJ/Ly2shdAojPqt.uhZVS0pllpqPy', 'candidato', NULL, NULL, 1),
(8, 'Carlos Mario', 'Ruiz Benítez', 'CC', '1102839283', 'carlos.ruiz@correo.com', '3147778899', 'Montería', 'Carrera 4 # 15-30', '$2y$10$moGhkr.D.R4yGKSipY4M8ePoiJ/Ly2shdAojPqt.uhZVS0pllpqPy', 'candidato', NULL, NULL, 1);

-- Consentimientos
INSERT OR IGNORE INTO consentimientos (usuario_id, finalidad, version_texto, ip_origen) VALUES
(6, 'Tratamiento de Datos Personales Selección y Contratación CECAR', 'v3.0-2026', '127.0.0.1'),
(7, 'Tratamiento de Datos Personales Selección y Contratación CECAR', 'v3.0-2026', '127.0.0.1'),
(8, 'Tratamiento de Datos Personales Selección y Contratación CECAR', 'v3.0-2026', '127.0.0.1');

-- Convocatorias
INSERT OR IGNORE INTO convocatorias (id, codigo, cargo, dependencia_id, facultad, programa, sede, periodo_academico, vacantes, modalidad, tipo_convocatoria, descripcion, fecha_apertura, fecha_cierre, estado, creado_por) VALUES
(1, 'CONV-2026-DOC-01', 'Docente Cátedra - Facultad de Educación y Ciencias Sociales', 1, 'Facultad de Educación y Ciencias Sociales', 'Licenciatura en Pedagogía Infantil', 'Sincelejo', '2026-2', 2, 'Cátedra', 'Docente', 'Convocatoria pública de méritos para vinculación de docentes de cátedra en el área de pedagogía y modelos didácticos.', '2026-10-01 08:00:00', '2026-11-30 18:00:00', 'publicada', 2),
(2, 'CONV-2026-DOC-02', 'Docente Tiempo Completo - Ingeniería de Sistemas', 3, 'Facultad de Ciencias Básicas, Ingeniería y Arquitectura', 'Ingeniería de Sistemas', 'Sincelejo', '2026-2', 1, 'Tiempo Completo', 'Docente', 'Docente con experiencia en desarrollo de software, inteligencia artificial y arquitectura en la nube.', '2026-10-15 08:00:00', '2026-12-15 18:00:00', 'publicada', 2),
(3, 'CONV-2026-ADM-01', 'Profesional de Apoyo Administrativo - Talento Humano', 5, 'Administración Central', 'Gestión de Talento Humano', 'Sincelejo', '2026-2', 1, 'Tiempo Completo', 'Administrativo', 'Profesional en administración o psicología para el área de selección y contratación institucional.', '2026-10-20 08:00:00', '2026-11-25 18:00:00', 'publicada', 2);

-- Perfiles de Cargo
INSERT OR IGNORE INTO perfil_cargo (convocatoria_id, formacion_minima, experiencia_minima_meses, competencias, funciones, condiciones) VALUES
(1, 'Título profesional en Pedagogía, Educación o afines con título de posgrado (Maestría en Educación requerida).', 24, 'Competencias pedagógicas, manejo de TIC en el aula, investigación formativa.', 'Orientar cursos presenciales y virtuales, acompañar proyectos de aula, participar en comités curriculares.', 'Disponibilidad horaria diurna y nocturna según programación académica.'),
(2, 'Título en Ingeniería de Sistemas con Maestría o Doctorado en Computación o Inteligencia Artificial.', 36, 'Liderazgo técnico, docencia universitaria, producción científica indexada.', 'Docencia en pregrado y posgrado, formulación de proyectos de investigación aplicada y semilleros.', 'Dedicación exclusiva en sede Sincelejo.');

-- Criterios de Evaluación
INSERT OR IGNORE INTO criterios_evaluacion (convocatoria_id, etapa, nombre, descripcion, puntaje_maximo, ponderacion, obligatorio) VALUES
(1, 'documental', 'Requisitos Mínimos y Formación', 'Validación de títulos de pregrado y posgrado con sus actas correspondientes', 100, 25, 1),
(1, 'psicotecnica', 'Prueba Psicotécnica y Competencias', 'Evaluación de competencias institucionales, ética y habilidades interpersonales', 100, 20, 1),
(1, 'aula', 'Prueba de Aula Docente', 'Demostración pedagógica ante comité evaluador y rúbrica docente', 100, 30, 1),
(1, 'entrevista', 'Entrevista Institucional de Comité', 'Evaluación integral con Decanatura y Gestión Humana', 100, 25, 1);

-- Postulaciones
-- Postulacion 1: Candidato Juan Carlos (Aspirante Nuevo a docente cátedra) -> MATCHING IMAGES 2 & 3:
-- Proceso: "Facultad de Educación y Ciencias Sociales", Estado: "contratado" (badge "23/23 Contratado", "23" aprobados)
INSERT OR IGNORE INTO postulaciones (id, convocatoria_id, usuario_id, codigo_radicado, estado, resultado_documental, porcentaje_avance, version, declaracion_veracidad, fecha_declaracion, fecha_postulacion, fecha_envio, etapa_actual, banco_talento_consentimiento, notas_internas) VALUES
(1, 1, 6, 'RAD-2026-FECS-001', 'contratado', 'conforme', 100, 1, 1, '2026-10-10 10:15:00', '2026-10-10 09:00:00', '2026-10-10 10:30:00', 'contratacion', 1, 'Candidato con excelente perfil docente y convalidación de posgrado verificada.'),
(2, 1, 7, 'RAD-2026-FECS-002', 'en_revision', 'pendiente', 85, 1, 1, '2026-10-18 11:00:00', '2026-10-18 10:00:00', '2026-10-18 11:30:00', 'revision_documental', 1, 'Expediente pendiente de verificar certificación laboral más reciente.'),
(3, 1, 8, 'RAD-2026-FECS-003', 'descartada', 'no_conforme', 60, 1, 1, '2026-10-12 14:00:00', '2026-10-12 12:00:00', '2026-10-12 14:15:00', 'revision_documental', 0, 'No acredita el título de posgrado exigido por la convocatoria.');

-- Causales de descarte
INSERT OR IGNORE INTO catalogos (grupo, codigo, nombre, descripcion, activo, orden) VALUES
('causales_descarte', 'DES-01', 'No cumple requisitos mínimos de formación académica', 'El aspirante no acredita el título de pregrado o posgrado exigido.', 1, 1),
('causales_descarte', 'DES-02', 'No acredita experiencia mínima requerida', 'Los certificados laborales no suman el tiempo de experiencia establecido.', 1, 2),
('causales_descarte', 'DES-03', 'No presentó o no superó prueba psicotécnica', 'Calificación inferior al umbral mínimo aprobatorio.', 1, 3),
('causales_descarte', 'DES-04', 'No superó prueba de aula docente', 'Concepto pedagógico no favorable emitido por Desarrollo Profesoral.', 1, 4),
('causales_descarte', 'DES-05', 'Concepto no favorable en entrevista de comité', 'Decisión motivada del comité evaluador de la convocatoria.', 1, 5),
('causales_descarte', 'DES-06', 'Vencimiento de plazo de subsanación documental', 'No remitió las correcciones solicitadas dentro del plazo institucional.', 1, 6),
('causales_descarte', 'DES-07', 'Concepto no apto en examen ocupacional', 'Dictamen médico laboral no favorable para las funciones del cargo.', 1, 7),
('causales_descarte', 'DES-08', 'Inhabilidad o antecedente legal restrictivo', 'Reporte sancionatorio en antecedentes disciplinarios, fiscales o penales.', 1, 8),

('categorias_documentales', 'contrato', 'Contrato', 'Documentos de minuta y firma contractual', 1, 1),
('categorias_documentales', 'informacion_personal', 'Información Personal', 'Documentos de identidad y hoja de vida', 1, 2),
('categorias_documentales', 'formacion_academica', 'Formación Académica', 'Títulos de pregrado, posgrados y actas', 1, 3),
('categorias_documentales', 'experiencia_laboral', 'Experiencia Laboral', 'Certificaciones de desempeño laboral y docente', 1, 4),
('categorias_documentales', 'seguridad_social', 'Seguridad Social', 'Certificados de afiliación a EPS y Pensión', 1, 5),
('categorias_documentales', 'informacion_bancaria', 'Información Bancaria', 'Certificación de cuenta de ahorros autorizada', 1, 6),
('categorias_documentales', 'salud_ocupacional', 'Salud Ocupacional', 'Exámenes médicos de ingreso y conceptos de aptitud', 1, 7),
('categorias_documentales', 'antecedentes_legales', 'Antecedentes Legales', 'Certificados de control y medidas correctivas', 1, 8);

-- Registrar Items de Hoja de Vida y Documentos para Postulación 1 (MATCHING IMAGE 3)
-- Total 23 ítems aprobados organizados en las 8 categorías:
-- 1. Contrato (1/1)
-- 2. Información Personal (3/3)
-- 3. Formación Académica (5/5)
-- 4. Experiencia Laboral (1/1)
-- 5. Seguridad Social (2/2)
-- 6. Información Bancaria (1/1 - Davivienda)
-- 7. Salud Ocupacional (1/1)
-- 8. Antecedentes Legales (8/8)

-- 1. Contrato (1)
INSERT OR IGNORE INTO items_hv (id, postulacion_id, categoria, tipo_item, titulo, datos_json, version, estado) VALUES
(1, 1, 'contrato', 'minuta_firmada', 'Minuta de Contrato Laboral Docente Cátedra 2026-2', '{"tipo_contrato":"Docente Cátedra","periodo":"2026-2","horas_semanales":16,"fecha_firma":"2026-10-25"}', 1, 'aprobado');

-- 2. Información Personal (3)
INSERT OR IGNORE INTO items_hv (id, postulacion_id, categoria, tipo_item, titulo, datos_json, version, estado) VALUES
(2, 1, 'informacion_personal', 'documento_identidad', 'Cédula de Ciudadanía Colombiana', '{"tipo":"CC","numero":"1102839281","fecha_expedicion":"2012-05-14","lugar_expedicion":"Sincelejo"}', 1, 'aprobado'),
(3, 1, 'informacion_personal', 'hoja_vida_formato', 'Hoja de Vida en Formato Institucional CECAR', '{"formato":"F-TH-02","version":"4.0","actualizacion":"2026-10"}', 1, 'aprobado'),
(4, 1, 'informacion_personal', 'certificado_residencia', 'Certificado de Residencia Municipal', '{"ciudad":"Sincelejo","departamento":"Sucre","direccion":"Barrio Venecia Calle 24 # 18-40"}', 1, 'aprobado');

-- 3. Formación Académica (5)
INSERT OR IGNORE INTO items_hv (id, postulacion_id, categoria, tipo_item, titulo, datos_json, version, estado) VALUES
(5, 1, 'formacion_academica', 'pregrado_diploma', 'Diploma de Pregrado - Licenciatura en Pedagogía', '{"nivel":"Pregrado","institucion":"Universidad de Sucre","programa":"Licenciatura en Pedagogía Infantil","ano":2015,"titulo":"Licenciado en Pedagogía Infantil"}', 1, 'aprobado'),
(6, 1, 'formacion_academica', 'pregrado_acta', 'Acta de Grado de Pregrado', '{"acta_numero":"ACT-1542","folio":"089","fecha_grado":"2015-12-18"}', 1, 'aprobado'),
(7, 1, 'formacion_academica', 'posgrado_diploma', 'Diploma de Maestría en Educación', '{"nivel":"Maestría","institucion":"Universidad del Norte","programa":"Maestría en Educación","ano":2019,"titulo":"Magíster en Educación"}', 1, 'aprobado'),
(8, 1, 'formacion_academica', 'posgrado_acta', 'Acta de Grado de Maestría', '{"acta_numero":"ACT-2810","folio":"142","fecha_grado":"2019-11-29"}', 1, 'aprobado'),
(9, 1, 'formacion_academica', 'posgrado_diplomado', 'Diplomado en Docencia Universitaria y Ambientes Virtuales', '{"institucion":"CECAR","duracion_horas":120,"ano":2021}', 1, 'aprobado');

-- 4. Experiencia Laboral (1)
INSERT OR IGNORE INTO items_hv (id, postulacion_id, categoria, tipo_item, titulo, datos_json, version, estado) VALUES
(10, 1, 'experiencia_laboral', 'experiencia_docente_univ', 'Certificación Docente Universitaria (3 años y 6 meses)', '{"empresa":"Institución de Educación Superior","cargo":"Docente de Cátedra","inicio":"2021-02-01","fin":"2024-08-30","funciones":"Diseño curricular, cátedra en didáctica, tutorías"}', 1, 'aprobado');

-- 5. Seguridad Social (2)
INSERT OR IGNORE INTO items_hv (id, postulacion_id, categoria, tipo_item, titulo, datos_json, version, estado) VALUES
(11, 1, 'seguridad_social', 'eps_certificado', 'Certificado de Afiliación Vigente a EPS Sanitas', '{"entidad":"EPS Sanitas","tipo_afiliacion":"Cotizante","estado":"Activo"}', 1, 'aprobado'),
(12, 1, 'seguridad_social', 'pension_certificado', 'Certificado de Afiliación a Fondo de Pensiones Porvenir', '{"entidad":"Porvenir Cesantías y Pensiones","tipo":"Obligatoria","estado":"Activo"}', 1, 'aprobado');

-- 6. Información Bancaria (1) - EXACT MATCH IMAGE 3
INSERT OR IGNORE INTO items_hv (id, postulacion_id, categoria, tipo_item, titulo, datos_json, version, estado) VALUES
(13, 1, 'informacion_bancaria', 'certificado_cuenta', 'Certificado Apertura Cuenta Davivienda *', '{"banco":"Banco Davivienda","tipo_cuenta":"Ahorros","numero_cuenta":"04829103948","titular":"Juan Carlos Pérez Gómez"}', 1, 'aprobado');

-- 7. Salud Ocupacional (1)
INSERT OR IGNORE INTO items_hv (id, postulacion_id, categoria, tipo_item, titulo, datos_json, version, estado) VALUES
(14, 1, 'salud_ocupacional', 'examen_ingreso', 'Concepto de Examen Médico Ocupacional de Ingreso (Apto)', '{"proveedor":"IPS Salud Ocupacional del Caribe","concepto":"Apto sin restricciones","fecha":"2026-10-22"}', 1, 'aprobado');

-- 8. Antecedentes Legales (8)
INSERT OR IGNORE INTO items_hv (id, postulacion_id, categoria, tipo_item, titulo, datos_json, version, estado) VALUES
(15, 1, 'antecedentes_legales', 'procuraduria', 'Certificado de Antecedentes Disciplinarios - Procuraduría', '{"entidad":"Procuraduría General de la Nación","resultado":"Sin sanciones ni inhabilidades vigentes"}', 1, 'aprobado'),
(16, 1, 'antecedentes_legales', 'contraloria', 'Certificado de Antecedentes Fiscales - Contraloría', '{"entidad":"Contraloría General de la República","resultado":"No reporta como responsable fiscal"}', 1, 'aprobado'),
(17, 1, 'antecedentes_legales', 'policia', 'Certificado de Antecedentes Judiciales - Policía Nacional', '{"entidad":"Policía Nacional de Colombia","resultado":"No registra antecedentes penales"}', 1, 'aprobado'),
(18, 1, 'antecedentes_legales', 'medidas_correctivas', 'Registro Nacional de Medidas Correctivas (RNMC)', '{"entidad":"Policía Nacional","resultado":"Sin medidas correctivas pendientes"}', 1, 'aprobado'),
(19, 1, 'antecedentes_legales', 'delitos_sexuales', 'Certificado de Inhabilidades por Delitos Sexuales (Ley 1918)', '{"entidad":"Policía Nacional","resultado":"Sin inhabilidades registradas"}', 1, 'aprobado'),
(20, 1, 'antecedentes_legales', 'redam', 'Certificado de Deudores Alimentarios Morosos (REDAM)', '{"entidad":"Ministerio de Tecnologías y MinJusticia","resultado":"No se encuentra registrado en el REDAM"}', 1, 'aprobado'),
(21, 1, 'antecedentes_legales', 'tarjeta_profesional', 'Constancia de Vigencia de Tarjeta Profesional', '{"entidad":"Colegio Profesional","numero":"TP-928174","estado":"Vigente"}', 1, 'aprobado'),
(22, 1, 'antecedentes_legales', 'lista_clintono_ofac', 'Consulta de Cumplimiento en Listas Restrictivas (OFAC / ONU)', '{"entidad":"Oficina de Cumplimiento CECAR","resultado":"Sin coincidencias registradas"}', 1, 'aprobado');

-- Soportes de Archivos vinculados a los 22 ítems de Postulación 1
INSERT OR IGNORE INTO soportes (id, item_hv_id, postulacion_id, nombre_original, nombre_almacenado, ruta, mime, tamano_bytes, hash_sha256, version) VALUES
(1, 1, 1, 'Minuta_Contrato_Docente_Firmada.pdf', 'sop_1_contrato.pdf', 'storage/uploads/sop_1_contrato.pdf', 'application/pdf', 312000, 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855', 1),
(2, 2, 1, 'Cedula_Ciudadania_JuanPerez.pdf', 'sop_2_cedula.pdf', 'storage/uploads/sop_2_cedula.pdf', 'application/pdf', 145000, 'd41d8cd98f00b204e9800998ecf8427e', 1),
(3, 3, 1, 'Formato_HV_CECAR_Diligenciado.pdf', 'sop_3_hv.pdf', 'storage/uploads/sop_3_hv.pdf', 'application/pdf', 280000, '9e107d9d372bb6826bd81d3542a419d6', 1),
(4, 4, 1, 'Certificado_Residencia_Sincelejo.pdf', 'sop_4_residencia.pdf', 'storage/uploads/sop_4_residencia.pdf', 'application/pdf', 120000, 'c4ca4238a0b923820dcc509a6f75849b', 1),
(5, 5, 1, 'Diploma_Pregrado_Licenciatura.pdf', 'sop_5_diploma_preg.pdf', 'storage/uploads/sop_5_diploma_preg.pdf', 'application/pdf', 450000, 'c81e728d9d4c2f636f067f89cc14862c', 1),
(6, 6, 1, 'Acta_Grado_Pregrado.pdf', 'sop_6_acta_preg.pdf', 'storage/uploads/sop_6_acta_preg.pdf', 'application/pdf', 220000, 'eccbc87e4b5ce2fe28308fd9f2a7baf3', 1),
(7, 7, 1, 'Diploma_Maestria_Educacion.pdf', 'sop_7_diploma_posg.pdf', 'storage/uploads/sop_7_diploma_posg.pdf', 'application/pdf', 490000, 'a87ff679a2f3e71d9181a67b7542122c', 1),
(8, 8, 1, 'Acta_Grado_Maestria_Educacion.pdf', 'sop_8_acta_posg.pdf', 'storage/uploads/sop_8_acta_posg.pdf', 'application/pdf', 215000, 'e4da3b7fbbce2345d7772b0674a318d5', 1),
(9, 9, 1, 'Diplomado_Docencia_Virtual_CECAR.pdf', 'sop_9_diplomado.pdf', 'storage/uploads/sop_9_diplomado.pdf', 'application/pdf', 310000, '1679091c5a880faf6fb5e6087eb1b2dc', 1),
(10, 10, 1, 'Certificacion_Docencia_Universitaria.pdf', 'sop_10_experiencia.pdf', 'storage/uploads/sop_10_experiencia.pdf', 'application/pdf', 340000, '8f14e45fceea167a5a36dedd4bea2543', 1),
(11, 11, 1, 'Certificado_EPS_Sanitas_Vigente.pdf', 'sop_11_eps.pdf', 'storage/uploads/sop_11_eps.pdf', 'application/pdf', 180000, 'c9f0f895fb98ab9159f51fd0297e236d', 1),
(12, 12, 1, 'Certificado_Pensiones_Porvenir.pdf', 'sop_12_pension.pdf', 'storage/uploads/sop_12_pension.pdf', 'application/pdf', 175000, '45c48cce2e2d7fbdea1afc51c7c6ad26', 1),
(13, 13, 1, 'Certificado_Cuenta_Ahorros_Davivienda.pdf', 'sop_13_davivienda.pdf', 'storage/uploads/sop_13_davivienda.pdf', 'application/pdf', 160000, 'd3d9446802a44259755d38e6d163e820', 1),
(14, 14, 1, 'Concepto_Medico_Ingreso_SST.pdf', 'sop_14_examen_sst.pdf', 'storage/uploads/sop_14_examen_sst.pdf', 'application/pdf', 290000, '6512bd43d9caa6e02c990b0a82652dca', 1),
(15, 15, 1, 'Antecedentes_Procuraduria.pdf', 'sop_15_procuraduria.pdf', 'storage/uploads/sop_15_procuraduria.pdf', 'application/pdf', 110000, 'c20ad4d76fe97759aa27a0c99bff6710', 1),
(16, 16, 1, 'Antecedentes_Contraloria.pdf', 'sop_16_contraloria.pdf', 'storage/uploads/sop_16_contraloria.pdf', 'application/pdf', 115000, 'c51ce410c124a10e0db5e4b97fc2af39', 1),
(17, 17, 1, 'Antecedentes_Policia_Nacional.pdf', 'sop_17_policia.pdf', 'storage/uploads/sop_17_policia.pdf', 'application/pdf', 105000, 'aab3238922bcc25a6f606eb525ffdc56', 1),
(18, 18, 1, 'Certificado_RNMC_Policia.pdf', 'sop_18_rnmc.pdf', 'storage/uploads/sop_18_rnmc.pdf', 'application/pdf', 125000, '9bf31c7ff062936a96d3c8bd1f8f2ff3', 1),
(19, 19, 1, 'Inhabilidades_Ley1918_Policia.pdf', 'sop_19_ley1918.pdf', 'storage/uploads/sop_19_ley1918.pdf', 'application/pdf', 130000, 'c74d97b01eae257e44aa9d5bade97baf', 1),
(20, 20, 1, 'Certificado_REDAM_MinTIC.pdf', 'sop_20_redam.pdf', 'storage/uploads/sop_20_redam.pdf', 'application/pdf', 112000, '70efdf2ec9b086079795c442636b55fb', 1),
(21, 21, 1, 'Tarjeta_Profesional_Licenciado.pdf', 'sop_21_tp.pdf', 'storage/uploads/sop_21_tp.pdf', 'application/pdf', 190000, '6f4922f45568161a8cdf4ad2299f6d23', 1),
(22, 22, 1, 'Verificacion_Listas_Restrictivas_CECAR.pdf', 'sop_22_listas.pdf', 'storage/uploads/sop_22_listas.pdf', 'application/pdf', 140000, '1f0e3dad99908345f7439f8ffabdffc4', 1);

-- Revisiones de Item para Postulación 1 (Todos Aprobados por Revisor)
INSERT OR IGNORE INTO revisiones_item (item_hv_id, revisor_id, resultado, motivo, observacion) VALUES
(1, 3, 'aprobado', 'Documento verificado', 'Minuta de contrato firmada electrónicamente con validez'),
(2, 3, 'aprobado', 'Documento verificado', 'Cédula de ciudadanía legible y vigente'),
(3, 3, 'aprobado', 'Documento verificado', 'Formato institucional diligenciado en su totalidad'),
(4, 3, 'aprobado', 'Documento verificado', 'Residencia en Sincelejo confirmada'),
(5, 3, 'aprobado', 'Documento verificado', 'Título de pregrado legalmente emitido por universidad acreditada'),
(6, 3, 'aprobado', 'Documento verificado', 'Acta de grado auténtica'),
(7, 3, 'aprobado', 'Documento verificado', 'Maestría en educación requerida por perfil cumplida a cabalidad'),
(8, 3, 'aprobado', 'Documento verificado', 'Acta de grado de posgrado conforme'),
(9, 3, 'aprobado', 'Documento verificado', 'Diplomado institucional acreditado'),
(10, 3, 'aprobado', 'Documento verificado', 'Experiencia docente universitaria superior a los 24 meses exigidos'),
(11, 3, 'aprobado', 'Documento verificado', 'Afiliación a seguridad social activa'),
(12, 3, 'aprobado', 'Documento verificado', 'Pensión activa en Porvenir'),
(13, 3, 'aprobado', 'Documento verificado', 'Certificación bancaria emitida por Banco Davivienda en cuenta de ahorros a su nombre'),
(14, 3, 'aprobado', 'Documento verificado', 'Concepto médico de aptitud ocupacional: Apto'),
(15, 3, 'aprobado', 'Documento verificado', 'Procuraduría sin antecedentes'),
(16, 3, 'aprobado', 'Documento verificado', 'Contraloría sin antecedentes'),
(17, 3, 'aprobado', 'Documento verificado', 'Policía sin antecedentes'),
(18, 3, 'aprobado', 'Documento verificado', 'RNMC sin medidas'),
(19, 3, 'aprobado', 'Documento verificado', 'Ley 1918 sin inhabilidad'),
(20, 3, 'aprobado', 'Documento verificado', 'REDAM no reportado'),
(21, 3, 'aprobado', 'Documento verificado', 'Tarjeta profesional vigente'),
(22, 3, 'aprobado', 'Documento verificado', 'Listas restrictivas limpias');

-- Examen Ocupacional Postulación 1
INSERT OR IGNORE INTO examenes_ocupacionales (postulacion_id, proveedor, fecha_programada, fecha_examen, concepto_aptitud, registrado_por) VALUES
(1, 'IPS Salud Ocupacional del Caribe', '2026-10-20', '2026-10-22', 'apto', 2);

-- Contratación Postulación 1 (MATCHING IMAGE 2: Estado Contratado, Carga Completa)
INSERT OR IGNORE INTO contrataciones (id, postulacion_id, responsable_id, estado_formalizacion, estado_carga_documentos, salario_asignado, fecha_inicio_labores, fecha_firma, observaciones, soporte_contrato_id) VALUES
(1, 1, 2, 'formalizado', 'completa', 2850000, '2026-11-01', '2026-10-25 15:30:00', 'Expediente formalizado y validado con todos los requisitos de contratación.', 1);

-- Vinculación Postulación 1
INSERT OR IGNORE INTO vinculaciones (id, contratacion_id, postulacion_id, codigo_colaborador, fecha_vinculacion, estado, confirmado_por) VALUES
(1, 1, 1, 'COL-CECAR-2026-0941', '2026-11-01', 'vinculado', 2);

-- Integración Hoja de Vida Institucional Postulación 1
INSERT OR IGNORE INTO integraciones_hv (postulacion_id, sistema_destino, estado, codigo_respuesta, payload_transferido) VALUES
(1, 'Sistema_Colaboradores_CECAR', 'exitosa', '200_OK', '{"colaborador_id":"COL-CECAR-2026-0941","nombres":"Juan Carlos","apellidos":"Pérez Gómez","documento":"1102839281","cargo":"Docente Cátedra","facultad":"Educación y Ciencias Sociales","documentos_transferidos":22}');

-- Pruebas de Evaluación Postulación 1
INSERT OR IGNORE INTO pruebas (postulacion_id, tipo, responsable_id, calificacion, dictamen, observaciones) VALUES
(1, 'psicotecnica', 2, 95.0, 'aprobado', 'Altas competencias de liderazgo, ética docente y empatía pedagógica.'),
(1, 'aula_docente', 5, 96.5, 'aprobado', 'Excelente dominio de grupo, manejo de herramientas didácticas y claridad metodológica.');

-- Entrevistas Postulación 1
INSERT OR IGNORE INTO franjas_entrevista (id, convocatoria_id, fecha, hora_inicio, hora_fin, modalidad, ubicacion, enlace, cupos) VALUES
(1, 1, '2026-10-21', '09:00:00', '10:00:00', 'virtual', 'Sala Virtual Meet CECAR', 'https://meet.google.com/cec-conv-001', 1);

INSERT OR IGNORE INTO entrevistas (id, postulacion_id, franja_id, fecha, hora_inicio, hora_fin, modalidad, ubicacion, enlace, estado) VALUES
(1, 1, 1, '2026-10-21', '09:00:00', '10:00:00', 'virtual', 'Sala Virtual Meet CECAR', 'https://meet.google.com/cec-conv-001', 'realizada');

INSERT OR IGNORE INTO entrevistadores (entrevista_id, usuario_id, rol_comite) VALUES
(1, 4, 'Presidente de Comité / Decana'),
(1, 2, 'Representante Talento Humano');

INSERT OR IGNORE INTO resultados_entrevista (entrevista_id, evaluador_id, puntaje, concepto, observacion, tipo) VALUES
(1, 4, 98.0, 'favorable', 'Candidato idóneo con gran vocación pedagógica y compromiso institucional.', 'individual'),
(1, 2, 95.0, 'favorable', 'Alineación total con la cultura y principios CECAR.', 'individual'),
(1, 4, 96.5, 'favorable', 'Concepto unánime de comité: Candidato seleccionado para vinculación.', 'consolidado');

-- Decisión Final Postulación 1
INSERT OR IGNORE INTO decisiones_finales (postulacion_id, resultado, fundamento, usuario_id) VALUES
(1, 'seleccionado', 'Aprobación unánime en todas las etapas evaluativas (prueba psicotécnica, prueba de aula, entrevista y revisión documental conforme).', 2);

-- Plan de Trabajo de Selección (RF-091)
INSERT OR IGNORE INTO planes_trabajo (id, convocatoria_id, titulo, responsable_id, estado, fecha_inicio, fecha_fin) VALUES
(1, 1, 'Plan Operativo de Selección Docente 2026-2', 2, 'completado', '2026-10-01', '2026-10-31');

INSERT OR IGNORE INTO actividades_plan (plan_id, actividad, responsable_id, fecha_limite, estado, observaciones) VALUES
(1, 'Publicación de convocatoria en portal web', 2, '2026-10-05', 'completada', 'Publicado sin novedades'),
(1, 'Revisión y verificación documental de aspirantes', 3, '2026-10-18', 'completada', 'Expedientes verificados al 100%'),
(1, 'Aplicación de prueba psicotécnica', 2, '2026-10-20', 'completada', 'Resultados sistematizados'),
(1, 'Realización y calificación de prueba de aula docente', 5, '2026-10-21', 'completada', 'Rúbricas firmadas por Desarrollo Profesoral'),
(1, 'Entrevistas de comité evaluador con Decanatura', 4, '2026-10-22', 'completada', 'Consolidado final emitido'),
(1, 'Formalización y vinculación de seleccionados', 2, '2026-10-26', 'completada', 'Contratos firmados y transferidos a HV');

-- Mesa de Ayuda (RF-099)
INSERT OR IGNORE INTO tickets_ayuda (id, usuario_id, categoria, asunto, mensaje, estado, respuesta, respondido_por, fecha_respuesta) VALUES
(1, 6, 'soporte_postulacion', 'Consulta sobre formato de certificado Davivienda', 'Buenas tardes, ¿es obligatorio que la certificación de cuenta de Davivienda tenga menos de 30 días de expedición?', 'resuelto', 'Estimado aspirante, sí, la certificación debe tener fecha de expedición no mayor a 30 días conforme al instructivo de vinculación.', 2, '2026-10-19 14:20:00');

-- Parámetros del Sistema
INSERT OR IGNORE INTO parametros_sistema (clave, valor, descripcion) VALUES
('smtp_activo', '0', 'Habilitación de pasarela de correos SMTP complementarios'),
('plazo_subsanacion_dias', '5', 'Días hábiles permitidos para responder requerimiento de subsanación'),
('banco_talento_vigencia_meses', '12', 'Meses de permanencia de candidatos elegibles en el banco de talentos'),
('tamano_maximo_mb', '10', 'Límite de tamaño por archivo de soporte'),
('nombre_institucion', 'Corporación Universitaria del Caribe - CECAR', 'Nombre oficial institucional');

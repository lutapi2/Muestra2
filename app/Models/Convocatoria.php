<?php

class Convocatoria {
    public static function getAll(array $filtros = []): array {
        $pdo = Database::getConnection();
        $sql = "
            SELECT c.*, d.nombre as dependencia_nombre,
                   (SELECT COUNT(*) FROM postulaciones p WHERE p.convocatoria_id = c.id) as total_postulados
            FROM convocatorias c
            LEFT JOIN dependencias d ON c.dependencia_id = d.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filtros['estado'])) {
            $sql .= " AND c.estado = :estado";
            $params[':estado'] = $filtros['estado'];
        }

        if (!empty($filtros['facultad'])) {
            $sql .= " AND (c.facultad LIKE :facultad OR d.nombre LIKE :facultad)";
            $params[':facultad'] = '%' . $filtros['facultad'] . '%';
        }

        if (!empty($filtros['buscar'])) {
            $sql .= " AND (c.cargo LIKE :buscar OR c.codigo LIKE :buscar)";
            $params[':buscar'] = '%' . $filtros['buscar'] . '%';
        }

        $sql .= " ORDER BY c.fecha_creacion DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function getPublicadas(): array {
        return self::getAll(['estado' => 'publicada']);
    }

    public static function findById(int $id): ?array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT c.*, d.nombre as dependencia_nombre, d.sede as sede_nombre,
                   pc.formacion_minima, pc.experiencia_minima_meses, pc.competencias, pc.funciones, pc.condiciones
            FROM convocatorias c
            LEFT JOIN dependencias d ON c.dependencia_id = d.id
            LEFT JOIN perfil_cargo pc ON c.id = pc.convocatoria_id
            WHERE c.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public static function codigoExiste(string $codigo, ?int $excluirId = null): bool {
        $pdo = Database::getConnection();
        $sql = "SELECT COUNT(*) FROM convocatorias WHERE UPPER(TRIM(codigo)) = UPPER(TRIM(:codigo))";
        $params = [':codigo' => trim($codigo)];

        if ($excluirId !== null) {
            $sql .= " AND id != :excluirId";
            $params[':excluirId'] = $excluirId;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function generarCodigoUnico(string $tipo = 'DOC'): string {
        $year = date('Y');
        $prefix = ($tipo === 'Administrativo') ? 'ADM' : 'DOC';
        $i = 1;
        do {
            $codigo = sprintf('CONV-%s-%s-%02d', $year, $prefix, $i);
            $i++;
        } while (self::codigoExiste($codigo));

        return $codigo;
    }

    public static function crear(array $datos): int {
        $codigo = strtoupper(trim($datos['codigo']));
        if (empty($codigo)) {
            $tipo = $datos['tipo_convocatoria'] ?? 'Docente';
            $codigo = self::generarCodigoUnico($tipo);
        }

        if (self::codigoExiste($codigo)) {
            throw new Exception("El código de convocatoria '{$codigo}' ya existe en el sistema.");
        }

        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare("
                INSERT INTO convocatorias (codigo, cargo, dependencia_id, facultad, programa, sede, periodo_academico, vacantes, modalidad, tipo_convocatoria, descripcion, fecha_apertura, fecha_cierre, estado, creado_por, fecha_creacion)
                VALUES (:codigo, :cargo, :dependencia_id, :facultad, :programa, :sede, :periodo_academico, :vacantes, :modalidad, :tipo_convocatoria, :descripcion, :fecha_apertura, :fecha_cierre, :estado, :creado_por, CURRENT_TIMESTAMP)
            ");
            $stmt->execute([
                ':codigo' => $codigo,
                ':cargo' => trim($datos['cargo']),
                ':dependencia_id' => (int)$datos['dependencia_id'],
                ':facultad' => trim($datos['facultad']),
                ':programa' => trim($datos['programa'] ?? ''),
                ':sede' => trim($datos['sede'] ?? 'Sincelejo'),
                ':periodo_academico' => trim($datos['periodo_academico'] ?? '2026-2'),
                ':vacantes' => max(1, (int)($datos['vacantes'] ?? 1)),
                ':modalidad' => trim($datos['modalidad'] ?? 'Tiempo Completo'),
                ':tipo_convocatoria' => trim($datos['tipo_convocatoria'] ?? 'Docente'),
                ':descripcion' => trim($datos['descripcion'] ?? ''),
                ':fecha_apertura' => $datos['fecha_apertura'],
                ':fecha_cierre' => $datos['fecha_cierre'],
                ':estado' => $datos['estado'] ?? 'publicada',
                ':creado_por' => !empty($datos['creado_por']) ? (int)$datos['creado_por'] : null
            ]);

            $convocatoriaId = (int)$pdo->lastInsertId();

            // Registrar perfil de cargo
            $stmtPerfil = $pdo->prepare("
                INSERT INTO perfil_cargo (convocatoria_id, formacion_minima, experiencia_minima_meses, competencias, funciones, condiciones)
                VALUES (:convocatoria_id, :formacion_minima, :experiencia_minima_meses, :competencias, :funciones, :condiciones)
            ");
            $stmtPerfil->execute([
                ':convocatoria_id' => $convocatoriaId,
                ':formacion_minima' => trim($datos['formacion_minima'] ?? 'Título universitario afín al área'),
                ':experiencia_minima_meses' => max(0, (int)($datos['experiencia_minima_meses'] ?? 12)),
                ':competencias' => trim($datos['competencias'] ?? 'Ética, pedagogía, compromiso institucional.'),
                ':funciones' => trim($datos['funciones'] ?? 'Desarrollo de labores académicas y curriculares institucionales.'),
                ':condiciones' => trim($datos['condiciones'] ?? 'Disponibilidad horaria y presencialidad según asignación.')
            ]);

            // Requisitos estándar de postulación
            $requisitosEstandar = [
                ['formacion', 'titulo_pregrado', 'Título y Acta de Grado Pregrado', 'obligatorio'],
                ['formacion', 'titulo_posgrado', 'Título y Acta de Grado Posgrado', 'obligatorio'],
                ['laboral', 'certificacion_laboral', 'Certificaciones de Experiencia Acreditada', 'obligatorio'],
                ['personal', 'documento_identidad', 'Copia de Documento de Identidad', 'obligatorio'],
                ['personal', 'hoja_vida', 'Hoja de Vida Institucional F-TH-02', 'obligatorio'],
                ['antecedentes', 'antecedentes_judiciales', 'Certificados de Antecedentes Legales y Medidas', 'obligatorio']
            ];

            $stmtReq = $pdo->prepare("
                INSERT INTO requisitos_convocatoria (convocatoria_id, categoria, tipo_item, nombre, obligatoriedad, soporte_requerido)
                VALUES (:cid, :cat, :tipo, :nom, :ob, 1)
            ");
            foreach ($requisitosEstandar as $req) {
                $stmtReq->execute([
                    ':cid' => $convocatoriaId,
                    ':cat' => $req[0],
                    ':tipo' => $req[1],
                    ':nom' => $req[2],
                    ':ob' => $req[3]
                ]);
            }

            // Criterios estándar de evaluación
            $criteriosEstandar = [
                ['documental', 'Requisitos Mínimos y Formación', 'Validación de títulos de pregrado y posgrado con sus actas correspondientes', 100, 25, 1],
                ['psicotecnica', 'Prueba Psicotécnica y Competencias', 'Evaluación de competencias institucionales, ética y habilidades interpersonales', 100, 20, 1],
                ['aula', 'Prueba de Aula Docente / Técnica', 'Demostración metodológica ante comité evaluador y rúbrica institucional', 100, 30, 1],
                ['entrevista', 'Entrevista Institucional de Comité', 'Evaluación integral con Decanatura y Gestión Humana', 100, 25, 1]
            ];

            $stmtCrit = $pdo->prepare("
                INSERT INTO criterios_evaluacion (convocatoria_id, etapa, nombre, descripcion, puntaje_maximo, ponderacion, obligatorio)
                VALUES (:cid, :etapa, :nom, :desc, :max, :pond, :ob)
            ");
            foreach ($criteriosEstandar as $crit) {
                $stmtCrit->execute([
                    ':cid' => $convocatoriaId,
                    ':etapa' => $crit[0],
                    ':nom' => $crit[1],
                    ':desc' => $crit[2],
                    ':max' => $crit[3],
                    ':pond' => $crit[4],
                    ':ob' => $crit[5]
                ]);
            }

            $pdo->commit();
            return $convocatoriaId;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function actualizar(int $id, array $datos): bool {
        $codigo = strtoupper(trim($datos['codigo']));
        if (self::codigoExiste($codigo, $id)) {
            throw new Exception("El código '{$codigo}' ya pertenece a otra convocatoria.");
        }

        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare("
                UPDATE convocatorias
                SET codigo = :codigo, cargo = :cargo, dependencia_id = :dependencia_id,
                    facultad = :facultad, programa = :programa, sede = :sede,
                    periodo_academico = :periodo_academico, vacantes = :vacantes,
                    modalidad = :modalidad, tipo_convocatoria = :tipo_convocatoria,
                    descripcion = :descripcion, fecha_apertura = :fecha_apertura,
                    fecha_cierre = :fecha_cierre, estado = :estado
                WHERE id = :id
            ");
            $stmt->execute([
                ':id' => $id,
                ':codigo' => $codigo,
                ':cargo' => trim($datos['cargo']),
                ':dependencia_id' => (int)$datos['dependencia_id'],
                ':facultad' => trim($datos['facultad']),
                ':programa' => trim($datos['programa'] ?? ''),
                ':sede' => trim($datos['sede'] ?? 'Sincelejo'),
                ':periodo_academico' => trim($datos['periodo_academico'] ?? '2026-2'),
                ':vacantes' => max(1, (int)($datos['vacantes'] ?? 1)),
                ':modalidad' => trim($datos['modalidad'] ?? 'Tiempo Completo'),
                ':tipo_convocatoria' => trim($datos['tipo_convocatoria'] ?? 'Docente'),
                ':descripcion' => trim($datos['descripcion'] ?? ''),
                ':fecha_apertura' => $datos['fecha_apertura'],
                ':fecha_cierre' => $datos['fecha_cierre'],
                ':estado' => $datos['estado'] ?? 'publicada'
            ]);

            $stmtPerfil = $pdo->prepare("
                UPDATE perfil_cargo
                SET formacion_minima = :formacion_minima,
                    experiencia_minima_meses = :experiencia_minima_meses,
                    competencias = :competencias,
                    funciones = :funciones,
                    condiciones = :condiciones
                WHERE convocatoria_id = :id
            ");
            $stmtPerfil->execute([
                ':id' => $id,
                ':formacion_minima' => trim($datos['formacion_minima'] ?? ''),
                ':experiencia_minima_meses' => max(0, (int)($datos['experiencia_minima_meses'] ?? 0)),
                ':competencias' => trim($datos['competencias'] ?? ''),
                ':funciones' => trim($datos['funciones'] ?? ''),
                ':condiciones' => trim($datos['condiciones'] ?? '')
            ]);

            $pdo->commit();
            return true;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Eliminación permanente (Hard Delete) de una convocatoria y todos sus registros subordinados
     */
    public static function eliminar(int $id): bool {
        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            // 1. Obtener todas las postulaciones asociadas a la convocatoria
            $stmtP = $pdo->prepare("SELECT id FROM postulaciones WHERE convocatoria_id = :id");
            $stmtP->execute([':id' => $id]);
            $postulacionIds = $stmtP->fetchAll(PDO::FETCH_COLUMN);

            foreach ($postulacionIds as $pId) {
                // Eliminar soportes físicos en disco creados por los aspirantes
                $stmtS = $pdo->prepare("SELECT ruta FROM soportes WHERE postulacion_id = :pid");
                $stmtS->execute([':pid' => $pId]);
                $archivos = $stmtS->fetchAll(PDO::FETCH_COLUMN);
                foreach ($archivos as $archivo) {
                    $rutaAbs = ROOT_PATH . '/' . $archivo;
                    if (file_exists($rutaAbs) && !str_contains($archivo, 'sop_1_contrato') && !str_contains($archivo, 'sop_2_cedula')) {
                        @unlink($rutaAbs);
                    }
                }

                $pdo->prepare("DELETE FROM vinculaciones WHERE postulacion_id = :pid")->execute([':pid' => $pId]);
                $pdo->prepare("DELETE FROM integraciones_hv WHERE postulacion_id = :pid")->execute([':pid' => $pId]);
                $pdo->prepare("DELETE FROM contrataciones WHERE postulacion_id = :pid")->execute([':pid' => $pId]);
                $pdo->prepare("DELETE FROM examenes_ocupacionales WHERE postulacion_id = :pid")->execute([':pid' => $pId]);
                $pdo->prepare("DELETE FROM decisiones_finales WHERE postulacion_id = :pid")->execute([':pid' => $pId]);
                $pdo->prepare("DELETE FROM resultados_entrevista WHERE entrevista_id IN (SELECT id FROM entrevistas WHERE postulacion_id = :pid)")->execute([':pid' => $pId]);
                $pdo->prepare("DELETE FROM entrevistadores WHERE entrevista_id IN (SELECT id FROM entrevistas WHERE postulacion_id = :pid)")->execute([':pid' => $pId]);
                $pdo->prepare("DELETE FROM entrevistas WHERE postulacion_id = :pid")->execute([':pid' => $pId]);
                $pdo->prepare("DELETE FROM pruebas WHERE postulacion_id = :pid")->execute([':pid' => $pId]);
                $pdo->prepare("DELETE FROM evaluaciones WHERE postulacion_id = :pid")->execute([':pid' => $pId]);
                $pdo->prepare("DELETE FROM detalles_subsanacion WHERE solicitud_id IN (SELECT id FROM solicitudes_subsanacion WHERE postulacion_id = :pid)")->execute([':pid' => $pId]);
                $pdo->prepare("DELETE FROM solicitudes_subsanacion WHERE postulacion_id = :pid")->execute([':pid' => $pId]);
                $pdo->prepare("DELETE FROM revisiones_item WHERE item_hv_id IN (SELECT id FROM items_hv WHERE postulacion_id = :pid)")->execute([':pid' => $pId]);
                $pdo->prepare("DELETE FROM soportes WHERE postulacion_id = :pid")->execute([':pid' => $pId]);
                $pdo->prepare("DELETE FROM items_hv WHERE postulacion_id = :pid")->execute([':pid' => $pId]);
                $pdo->prepare("DELETE FROM notificaciones WHERE postulacion_id = :pid")->execute([':pid' => $pId]);
                $pdo->prepare("DELETE FROM movimientos_historial WHERE postulacion_id = :pid")->execute([':pid' => $pId]);
                $pdo->prepare("DELETE FROM postulaciones WHERE id = :pid")->execute([':pid' => $pId]);
            }

            // 2. Eliminar planes de trabajo e hitos
            $pdo->prepare("DELETE FROM actividades_plan WHERE plan_id IN (SELECT id FROM planes_trabajo WHERE convocatoria_id = :id)")->execute([':id' => $id]);
            $pdo->prepare("DELETE FROM planes_trabajo WHERE convocatoria_id = :id")->execute([':id' => $id]);

            // 3. Eliminar franjas de entrevista
            $pdo->prepare("DELETE FROM franjas_entrevista WHERE convocatoria_id = :id")->execute([':id' => $id]);

            // 4. Eliminar criterios y requisitos
            $pdo->prepare("DELETE FROM criterios_evaluacion WHERE convocatoria_id = :id")->execute([':id' => $id]);
            $pdo->prepare("DELETE FROM requisitos_convocatoria WHERE convocatoria_id = :id")->execute([':id' => $id]);
            $pdo->prepare("DELETE FROM perfil_cargo WHERE convocatoria_id = :id")->execute([':id' => $id]);

            // 5. Eliminar la convocatoria permanentemente
            $stmt = $pdo->prepare("DELETE FROM convocatorias WHERE id = :id");
            $stmt->execute([':id' => $id]);

            $pdo->commit();
            return true;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function cambiarEstado(int $id, string $nuevoEstado, ?string $motivo = null): void {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            UPDATE convocatorias 
            SET estado = :estado, motivo_retiro = :motivo
            WHERE id = :id
        ");
        $stmt->execute([
            ':id' => $id,
            ':estado' => $nuevoEstado,
            ':motivo' => $motivo
        ]);
    }
}

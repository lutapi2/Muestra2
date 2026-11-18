<?php

class Usuario {
    public static function findByEmail(string $correo, bool $soloActivos = true): ?array {
        $pdo = Database::getConnection();
        $sql = "
            SELECT u.*, d.nombre as dependencia_nombre, d.facultad, d.sede, r.nombre as rol_nombre
            FROM usuarios u
            LEFT JOIN dependencias d ON u.dependencia_id = d.id
            LEFT JOIN roles r ON u.rol = r.id
            WHERE LOWER(TRIM(u.correo)) = LOWER(TRIM(:correo))
        ";
        if ($soloActivos) {
            $sql .= " AND u.activo = 1";
        }
        $sql .= " LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':correo' => trim($correo)]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function findById(int $id): ?array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT u.*, d.nombre as dependencia_nombre, d.facultad, d.sede, r.nombre as rol_nombre
            FROM usuarios u
            LEFT JOIN dependencias d ON u.dependencia_id = d.id
            LEFT JOIN roles r ON u.rol = r.id
            WHERE u.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function emailExiste(string $correo, ?int $excluirId = null): bool {
        $pdo = Database::getConnection();
        $sql = "SELECT COUNT(*) FROM usuarios WHERE LOWER(TRIM(correo)) = LOWER(TRIM(:correo))";
        $params = [':correo' => trim($correo)];

        if ($excluirId !== null) {
            $sql .= " AND id != :excluirId";
            $params[':excluirId'] = $excluirId;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function documentoExiste(string $numeroDoc, ?int $excluirId = null): bool {
        $pdo = Database::getConnection();
        $sql = "SELECT COUNT(*) FROM usuarios WHERE TRIM(numero_documento) = TRIM(:doc)";
        $params = [':doc' => trim($numeroDoc)];

        if ($excluirId !== null) {
            $sql .= " AND id != :excluirId";
            $params[':excluirId'] = $excluirId;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function registrarCandidato(array $datos): int {
        $correo = strtolower(trim($datos['correo']));
        $documento = trim($datos['numero_documento']);

        if (self::emailExiste($correo)) {
            throw new Exception("El correo electrónico '{$correo}' ya se encuentra registrado.");
        }

        if (self::documentoExiste($documento)) {
            throw new Exception("El documento de identidad '{$documento}' ya se encuentra registrado.");
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            INSERT INTO usuarios (nombres, apellidos, tipo_documento, numero_documento, correo, telefono, ciudad, direccion, password_hash, rol, activo, fecha_creacion)
            VALUES (:nombres, :apellidos, :tipo_documento, :numero_documento, :correo, :telefono, :ciudad, :direccion, :password_hash, 'candidato', 1, CURRENT_TIMESTAMP)
        ");
        $stmt->execute([
            ':nombres' => trim($datos['nombres']),
            ':apellidos' => trim($datos['apellidos']),
            ':tipo_documento' => $datos['tipo_documento'] ?? 'CC',
            ':numero_documento' => $documento,
            ':correo' => $correo,
            ':telefono' => trim($datos['telefono'] ?? ''),
            ':ciudad' => trim($datos['ciudad'] ?? 'Sincelejo'),
            ':direccion' => trim($datos['direccion'] ?? ''),
            ':password_hash' => password_hash($datos['password'], PASSWORD_BCRYPT)
        ]);

        $userId = (int)$pdo->lastInsertId();

        // Registrar consentimiento inicial de tratamiento de datos (RF-015, HV-001)
        $consentStmt = $pdo->prepare("
            INSERT INTO consentimientos (usuario_id, finalidad, version_texto, ip_origen, fecha_hora)
            VALUES (:usuario_id, 'Tratamiento de Datos Personales Selección y Contratación CECAR', 'v3.0-2026', :ip, CURRENT_TIMESTAMP)
        ");
        $consentStmt->execute([
            ':usuario_id' => $userId,
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ]);

        return $userId;
    }

    public static function crearUsuarioAdmin(array $datos): int {
        $correo = strtolower(trim($datos['correo']));
        $documento = trim($datos['numero_documento']);

        if (self::emailExiste($correo)) {
            throw new Exception("El correo electrónico '{$correo}' ya se encuentra registrado en el sistema.");
        }

        if (self::documentoExiste($documento)) {
            throw new Exception("El número de documento '{$documento}' ya pertenece a otro usuario.");
        }

        $password = !empty($datos['password']) ? $datos['password'] : 'cecar' . date('Y');
        $dependenciaId = !empty($datos['dependencia_id']) ? (int)$datos['dependencia_id'] : null;

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            INSERT INTO usuarios (nombres, apellidos, tipo_documento, numero_documento, correo, telefono, ciudad, direccion, password_hash, rol, dependencia_id, cargo_interno, activo, fecha_creacion)
            VALUES (:nombres, :apellidos, :tipo_documento, :numero_documento, :correo, :telefono, :ciudad, :direccion, :password_hash, :rol, :dependencia_id, :cargo_interno, :activo, CURRENT_TIMESTAMP)
        ");
        $stmt->execute([
            ':nombres' => trim($datos['nombres']),
            ':apellidos' => trim($datos['apellidos']),
            ':tipo_documento' => $datos['tipo_documento'] ?? 'CC',
            ':numero_documento' => $documento,
            ':correo' => $correo,
            ':telefono' => trim($datos['telefono'] ?? ''),
            ':ciudad' => trim($datos['ciudad'] ?? 'Sincelejo'),
            ':direccion' => trim($datos['direccion'] ?? 'Campus CECAR'),
            ':password_hash' => password_hash($password, PASSWORD_BCRYPT),
            ':rol' => trim($datos['rol'] ?? 'candidato'),
            ':dependencia_id' => $dependenciaId,
            ':cargo_interno' => trim($datos['cargo_interno'] ?? ''),
            ':activo' => isset($datos['activo']) ? (int)$datos['activo'] : 1
        ]);

        return (int)$pdo->lastInsertId();
    }

    public static function actualizarUsuario(int $id, array $datos): bool {
        $correo = strtolower(trim($datos['correo']));
        $documento = trim($datos['numero_documento']);

        if (self::emailExiste($correo, $id)) {
            throw new Exception("El correo electrónico '{$correo}' ya está en uso por otro usuario.");
        }

        if (self::documentoExiste($documento, $id)) {
            throw new Exception("El número de documento '{$documento}' ya está registrado.");
        }

        $pdo = Database::getConnection();
        $sql = "
            UPDATE usuarios 
            SET nombres = :nombres, apellidos = :apellidos, tipo_documento = :tipo_documento,
                numero_documento = :numero_documento, correo = :correo, telefono = :telefono,
                ciudad = :ciudad, direccion = :direccion, rol = :rol,
                dependencia_id = :dependencia_id, cargo_interno = :cargo_interno, activo = :activo
        ";
        $params = [
            ':id' => $id,
            ':nombres' => trim($datos['nombres']),
            ':apellidos' => trim($datos['apellidos']),
            ':tipo_documento' => $datos['tipo_documento'] ?? 'CC',
            ':numero_documento' => $documento,
            ':correo' => $correo,
            ':telefono' => trim($datos['telefono'] ?? ''),
            ':ciudad' => trim($datos['ciudad'] ?? 'Sincelejo'),
            ':direccion' => trim($datos['direccion'] ?? ''),
            ':rol' => trim($datos['rol']),
            ':dependencia_id' => !empty($datos['dependencia_id']) ? (int)$datos['dependencia_id'] : null,
            ':cargo_interno' => trim($datos['cargo_interno'] ?? ''),
            ':activo' => isset($datos['activo']) ? (int)$datos['activo'] : 1
        ];

        if (!empty($datos['password'])) {
            $sql .= ", password_hash = :password_hash";
            $params[':password_hash'] = password_hash($datos['password'], PASSWORD_BCRYPT);
        }

        $sql .= " WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Eliminación permanente (Hard Delete) de un usuario y toda su información asociada
     */
    public static function eliminar(int $id): bool {
        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            // 1. Obtener todas las postulaciones asociadas a este usuario
            $stmtP = $pdo->prepare("SELECT id FROM postulaciones WHERE usuario_id = :uid");
            $stmtP->execute([':uid' => $id]);
            $postulacionIds = $stmtP->fetchAll(PDO::FETCH_COLUMN);

            foreach ($postulacionIds as $pId) {
                // Limpiar soportes físicos no semilla
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

            // 2. Eliminar registros directos del usuario
            $pdo->prepare("DELETE FROM consentimientos WHERE usuario_id = :uid")->execute([':uid' => $id]);
            $pdo->prepare("DELETE FROM notificaciones WHERE usuario_id = :uid")->execute([':uid' => $id]);
            $pdo->prepare("DELETE FROM tickets_ayuda WHERE usuario_id = :uid")->execute([':uid' => $id]);
            $pdo->prepare("DELETE FROM movimientos_historial WHERE usuario_id = :uid")->execute([':uid' => $id]);
            $pdo->prepare("DELETE FROM entrevistadores WHERE usuario_id = :uid")->execute([':uid' => $id]);

            // 3. Desvincular referencias institucionales (donde actuó como gestor, revisor o evaluador)
            $pdo->prepare("UPDATE convocatorias SET creado_por = NULL WHERE creado_por = :uid")->execute([':uid' => $id]);
            $pdo->prepare("UPDATE revisiones_item SET revisor_id = NULL WHERE revisor_id = :uid")->execute([':uid' => $id]);
            $pdo->prepare("UPDATE solicitudes_subsanacion SET solicitante_id = NULL WHERE solicitante_id = :uid")->execute([':uid' => $id]);
            $pdo->prepare("UPDATE evaluaciones SET evaluador_id = NULL WHERE evaluador_id = :uid")->execute([':uid' => $id]);
            $pdo->prepare("UPDATE pruebas SET responsable_id = NULL WHERE responsable_id = :uid")->execute([':uid' => $id]);
            $pdo->prepare("UPDATE resultados_entrevista SET evaluador_id = NULL WHERE evaluador_id = :uid")->execute([':uid' => $id]);
            $pdo->prepare("UPDATE decisiones_finales SET usuario_id = NULL WHERE usuario_id = :uid")->execute([':uid' => $id]);
            $pdo->prepare("UPDATE examenes_ocupacionales SET registrado_por = NULL WHERE registrado_por = :uid")->execute([':uid' => $id]);
            $pdo->prepare("UPDATE contrataciones SET responsable_id = NULL WHERE responsable_id = :uid")->execute([':uid' => $id]);
            $pdo->prepare("UPDATE vinculaciones SET confirmado_por = NULL WHERE confirmado_por = :uid")->execute([':uid' => $id]);
            $pdo->prepare("UPDATE planes_trabajo SET responsable_id = NULL WHERE responsable_id = :uid")->execute([':uid' => $id]);
            $pdo->prepare("UPDATE actividades_plan SET responsable_id = NULL WHERE responsable_id = :uid")->execute([':uid' => $id]);
            $pdo->prepare("UPDATE tickets_ayuda SET respondido_por = NULL WHERE respondido_por = :uid")->execute([':uid' => $id]);
            $pdo->prepare("UPDATE auditoria SET usuario_id = NULL WHERE usuario_id = :uid")->execute([':uid' => $id]);

            // 4. Eliminar el usuario definitivamente de la base de datos
            $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = :uid");
            $stmt->execute([':uid' => $id]);

            $pdo->commit();
            return true;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function getAll(string $rolFiltro = ''): array {
        $pdo = Database::getConnection();
        $sql = "
            SELECT u.*, d.nombre as dependencia_nombre, r.nombre as rol_nombre
            FROM usuarios u
            LEFT JOIN dependencias d ON u.dependencia_id = d.id
            LEFT JOIN roles r ON u.rol = r.id
        ";
        $params = [];
        if (!empty($rolFiltro)) {
            $sql .= " WHERE u.rol = :rol";
            $params[':rol'] = $rolFiltro;
        }
        $sql .= " ORDER BY u.fecha_creacion DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function actualizarUltimoAcceso(int $id): void {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE usuarios SET ultimo_acceso = CURRENT_TIMESTAMP WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }
}

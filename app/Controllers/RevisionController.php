<?php

class RevisionController {
    public function index(Request $request): void {
        AuthHelper::exigirRol(['admin', 'talento_humano', 'revisor']);

        $pdo = Database::getConnection();

        $estadoFiltro = $request->get('estado', 'todos');
        $convocatoriaIdFiltro = (int)$request->get('convocatoria_id', 0);

        $where = ["p.estado != 'retirada'"];
        $params = [];

        if ($estadoFiltro === 'enviadas') {
            $where[] = "p.estado IN ('enviada', 'en_revision')";
        } elseif ($estadoFiltro === 'borradores') {
            $where[] = "p.estado = 'borrador'";
        } elseif ($estadoFiltro === 'habilitadas') {
            $where[] = "p.estado = 'habilitada'";
        } elseif ($estadoFiltro !== 'todos' && !empty($estadoFiltro)) {
            $where[] = "p.estado = :estado";
            $params[':estado'] = $estadoFiltro;
        }

        if ($convocatoriaIdFiltro > 0) {
            $where[] = "p.convocatoria_id = :cid";
            $params[':cid'] = $convocatoriaIdFiltro;
        }

        $whereSql = implode(' AND ', $where);

        $sql = "
            SELECT p.*, c.cargo, c.codigo as convocatoria_codigo, c.facultad,
                   u.nombres, u.apellidos, u.correo, u.numero_documento,
                   (SELECT COUNT(*) FROM items_hv i WHERE i.postulacion_id = p.id) as total_items,
                   (SELECT COUNT(*) FROM items_hv i WHERE i.postulacion_id = p.id AND i.estado = 'aprobado') as aprobados,
                   (SELECT COUNT(*) FROM items_hv i WHERE i.postulacion_id = p.id AND i.estado = 'observado') as observados,
                   (SELECT COUNT(*) FROM items_hv i WHERE i.postulacion_id = p.id AND i.estado = 'rechazado') as rechazados
            FROM postulaciones p
            JOIN convocatorias c ON p.convocatoria_id = c.id
            JOIN usuarios u ON p.usuario_id = u.id
            WHERE {$whereSql}
            ORDER BY p.fecha_postulacion DESC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $postulaciones = $stmt->fetchAll();

        $conteoTotal = (int)$pdo->query("SELECT COUNT(*) FROM postulaciones WHERE estado != 'retirada'")->fetchColumn();
        $conteoEnviadas = (int)$pdo->query("SELECT COUNT(*) FROM postulaciones WHERE estado IN ('enviada', 'en_revision')")->fetchColumn();
        $conteoBorradores = (int)$pdo->query("SELECT COUNT(*) FROM postulaciones WHERE estado = 'borrador'")->fetchColumn();
        $conteoHabilitadas = (int)$pdo->query("SELECT COUNT(*) FROM postulaciones WHERE estado = 'habilitada'")->fetchColumn();
        $convocatorias = Convocatoria::getAll();

        View::render('revision.index', [
            'postulaciones' => $postulaciones,
            'estadoFiltro' => $estadoFiltro,
            'convocatoriaIdFiltro' => $convocatoriaIdFiltro,
            'conteoTotal' => $conteoTotal,
            'conteoEnviadas' => $conteoEnviadas,
            'conteoBorradores' => $conteoBorradores,
            'conteoHabilitadas' => $conteoHabilitadas,
            'convocatorias' => $convocatorias
        ], 'main');
    }

    public function expediente(Request $request): void {
        AuthHelper::exigirRol(['admin', 'talento_humano', 'revisor']);

        $postulacionId = (int)$request->get('id');
        $postulacion = Postulacion::findById($postulacionId);

        if (!$postulacion) {
            AuthHelper::setFlash('danger', 'Postulación no encontrada.');
            Response::redirect('c=Revision&a=index');
        }

        $itemsPorCategoria = ItemHV::getOrganizadosPorCategoria($postulacionId);
        $pdo = Database::getConnection();

        // Check if there is an active rectification request (solicitud de subsanación)
        $stmtSub = $pdo->prepare("
            SELECT * FROM solicitudes_subsanacion 
            WHERE postulacion_id = :id 
            ORDER BY id DESC LIMIT 1
        ");
        $stmtSub->execute([':id' => $postulacionId]);
        $solicitudActiva = $stmtSub->fetch();

        $causales = $pdo->query("SELECT * FROM catalogo_causales_rechazo WHERE activo = 1 ORDER BY id ASC")->fetchAll();

        View::render('revision.expediente', [
            'postulacion' => $postulacion,
            'itemsPorCategoria' => $itemsPorCategoria,
            'solicitudActiva' => $solicitudActiva,
            'causales' => $causales,
            'csrf_token' => $request->getCsrfToken()
        ], 'main');
    }

    public function verificarItem(Request $request): void {
        AuthHelper::exigirRol(['admin', 'talento_humano', 'revisor']);

        $itemId = (int)$request->post('item_id');
        $postulacionId = (int)$request->post('postulacion_id');
        $estado = $request->post('estado'); // aprobado, observado, rechazado
        $motivo = trim($request->post('motivo', ''));
        $causalId = $request->post('causal_id', null);

        if (!$itemId || !in_array($estado, ['aprobado', 'observado', 'rechazado'])) {
            AuthHelper::setFlash('danger', 'Parámetros de verificación inválidos.');
            Response::redirect('c=Revision&a=expediente&id=' . $postulacionId);
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            UPDATE items_hv 
            SET estado = :estado,
                revision_motivo = :motivo,
                revision_causal_id = :causal_id,
                revisado_por = :revisor_id,
                fecha_revision = CURRENT_TIMESTAMP
            WHERE id = :id
        ");
        $stmt->execute([
            ':estado' => $estado,
            ':motivo' => $motivo ?: null,
            ':causal_id' => $causalId ?: null,
            ':revisor_id' => AuthHelper::getUsuarioId(),
            ':id' => $itemId
        ]);

        AuditHelper::log('verificar_item_hv', 'items_hv', $itemId, null, [
            'estado' => $estado,
            'postulacion_id' => $postulacionId,
            'motivo' => $motivo
        ]);

        // Check if all items are verified to update postulation documental status
        $total = (int)$pdo->query("SELECT COUNT(*) FROM items_hv WHERE postulacion_id = $postulacionId")->fetchColumn();
        $aprobados = (int)$pdo->query("SELECT COUNT(*) FROM items_hv WHERE postulacion_id = $postulacionId AND estado = 'aprobado'")->fetchColumn();
        $observados = (int)$pdo->query("SELECT COUNT(*) FROM items_hv WHERE postulacion_id = $postulacionId AND estado = 'observado'")->fetchColumn();
        $rechazados = (int)$pdo->query("SELECT COUNT(*) FROM items_hv WHERE postulacion_id = $postulacionId AND estado = 'rechazado'")->fetchColumn();

        if ($total > 0 && $aprobados === $total) {
            $pdo->prepare("UPDATE postulaciones SET resultado_documental = 'conforme', estado = 'habilitada' WHERE id = :id")->execute([':id' => $postulacionId]);
            AuditHelper::logMovimiento($postulacionId, 'dictamen_documental', 'en_revision', 'habilitada', 'Todos los soportes probatorios aprobados con éxito (RF-044).');
        } elseif ($rechazados > 0) {
            $pdo->prepare("UPDATE postulaciones SET resultado_documental = 'no_conforme' WHERE id = :id")->execute([':id' => $postulacionId]);
            AuditHelper::logMovimiento($postulacionId, 'dictamen_documental', 'en_revision', 'en_revision', 'Ítem documental rechazado.');
        } elseif ($observados > 0) {
            $pdo->prepare("UPDATE postulaciones SET resultado_documental = 'observado' WHERE id = :id")->execute([':id' => $postulacionId]);
            AuditHelper::logMovimiento($postulacionId, 'dictamen_documental', 'en_revision', 'en_revision', 'Ítem documental observado.');
        }

        AuthHelper::setFlash('success', "Ítem marcado como {$estado} con éxito.");
        Response::redirect('c=Revision&a=expediente&id=' . $postulacionId);
    }

    public function emitirSubsanacion(Request $request): void {
        AuthHelper::exigirRol(['admin', 'talento_humano', 'revisor']);

        $postulacionId = (int)$request->post('postulacion_id');
        $plazoDias = (int)$request->post('plazo_dias', 5);
        $observaciones = trim($request->post('observaciones', ''));

        $fechaLimite = date('Y-m-d H:i:s', strtotime("+$plazoDias days"));

        $pdo = Database::getConnection();

        // Get observed items
        $stmtObs = $pdo->prepare("SELECT id, titulo FROM items_hv WHERE postulacion_id = :id AND estado = 'observado'");
        $stmtObs->execute([':id' => $postulacionId]);
        $itemsObservados = $stmtObs->fetchAll();

        if (empty($itemsObservados)) {
            AuthHelper::setFlash('warning', 'No hay ítems con estado "observado" para solicitar subsanación.');
            Response::redirect('c=Revision&a=expediente&id=' . $postulacionId);
        }

        $stmtSub = $pdo->prepare("
            INSERT INTO solicitudes_subsanacion (postulacion_id, solicitante_id, fecha_limite, estado, observaciones)
            VALUES (:pid, :uid, :flim, 'pendiente', :obs)
        ");
        $stmtSub->execute([
            ':pid' => $postulacionId,
            ':uid' => AuthHelper::getUsuarioId(),
            ':flim' => $fechaLimite,
            ':obs' => $observaciones
        ]);
        $subsanacionId = (int)$pdo->lastInsertId();

        $stmtItemSub = $pdo->prepare("INSERT INTO items_subsanacion (solicitud_id, item_hv_id, descripcion_observacion) VALUES (:sid, :iid, :desc)");
        foreach ($itemsObservados as $io) {
            $stmtItemSub->execute([
                ':sid' => $subsanacionId,
                ':iid' => $io['id'],
                ':desc' => "Subsanación requerida para: {$io['titulo']}"
            ]);
        }

        // Update postulation state to 'subsanacion_requerida' (RF-048)
        $pdo->prepare("UPDATE postulaciones SET estado = 'subsanacion_requerida', resultado_documental = 'observado' WHERE id = :id")->execute([':id' => $postulacionId]);

        AuditHelper::logMovimiento($postulacionId, 'solicitud_subsanacion', 'en_revision', 'subsanacion_requerida', "Solicitud de subsanación emitida con plazo hasta $fechaLimite.");

        $postulacion = Postulacion::findById($postulacionId);
        NotifyHelper::enviar(
            $postulacion['usuario_id'],
            'Solicitud de Subsanación de Documentos',
            "Se ha emitido una solicitud de subsanación para su postulación a {$postulacion['convocatoria_cargo']}. Por favor ingrese al portal y corrija los ítems observados antes del " . date('d/m/Y', strtotime($fechaLimite)) . ".",
            $postulacionId
        );

        AuthHelper::setFlash('success', "Solicitud de subsanación emitida exitosamente. Se notificó al candidato y se fijó fecha límite al " . date('d/m/Y', strtotime($fechaLimite)) . ".");
        Response::redirect('c=Revision&a=expediente&id=' . $postulacionId);
    }
}

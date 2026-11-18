<?php

class ConvocatoriaController {
    public function index(Request $request): void {
        $filtros = [
            'facultad' => $request->get('facultad', ''),
            'buscar' => $request->get('buscar', '')
        ];

        if (!AuthHelper::estaAutenticado() || AuthHelper::esCandidato()) {
            $filtros['estado'] = 'publicada';
        } else {
            $filtros['estado'] = $request->get('estado', '');
        }

        $convocatorias = Convocatoria::getAll($filtros);

        View::render('convocatorias.index', [
            'convocatorias' => $convocatorias,
            'filtros' => $filtros,
            'esGestor' => AuthHelper::esTalentoHumano(),
            'csrf_token' => $request->getCsrfToken()
        ], 'main');
    }

    public function detalle(Request $request): void {
        $id = (int)$request->get('id');
        $convocatoria = Convocatoria::findById($id);

        if (!$convocatoria) {
            AuthHelper::setFlash('danger', 'Convocatoria no encontrada en la base de datos.');
            Response::redirect('c=Convocatoria&a=index');
        }

        $pdo = Database::getConnection();
        $stmtReq = $pdo->prepare("SELECT * FROM requisitos_convocatoria WHERE convocatoria_id = :id ORDER BY id ASC");
        $stmtReq->execute([':id' => $id]);
        $requisitos = $stmtReq->fetchAll();

        $stmtCrit = $pdo->prepare("SELECT * FROM criterios_evaluacion WHERE convocatoria_id = :id ORDER BY id ASC");
        $stmtCrit->execute([':id' => $id]);
        $criterios = $stmtCrit->fetchAll();

        View::render('convocatorias.detalle', [
            'convocatoria' => $convocatoria,
            'requisitos' => $requisitos,
            'criterios' => $criterios,
            'esGestor' => AuthHelper::esTalentoHumano(),
            'csrf_token' => $request->getCsrfToken()
        ], 'main');
    }

    public function crear(Request $request): void {
        AuthHelper::exigirRol(['admin', 'talento_humano']);

        $pdo = Database::getConnection();
        $dependencias = $pdo->query("SELECT * FROM dependencias WHERE activa = 1 ORDER BY nombre ASC")->fetchAll();

        $formData = $_SESSION['form_convocatoria'] ?? [];
        unset($_SESSION['form_convocatoria']);

        $codigoSugerido = !empty($formData['codigo']) ? $formData['codigo'] : Convocatoria::generarCodigoUnico('DOC');

        View::render('convocatorias.crear', [
            'dependencias' => $dependencias,
            'codigoSugerido' => $codigoSugerido,
            'form' => $formData,
            'csrf_token' => $request->getCsrfToken()
        ], 'main');
    }

    public function guardar(Request $request): void {
        AuthHelper::exigirRol(['admin', 'talento_humano']);

        $codigo = strtoupper(trim($request->post('codigo', '')));
        $cargo = trim($request->post('cargo', ''));
        $dependenciaId = (int)$request->post('dependencia_id', 0);
        $facultad = trim($request->post('facultad', ''));
        $programa = trim($request->post('programa', ''));
        $sede = trim($request->post('sede', 'Sincelejo'));
        $periodo = trim($request->post('periodo_academico', '2026-2'));
        $vacantes = max(1, (int)$request->post('vacantes', 1));
        $modalidad = trim($request->post('modalidad', 'Tiempo Completo'));
        $tipoConv = trim($request->post('tipo_convocatoria', 'Docente'));
        $descripcion = trim($request->post('descripcion', ''));
        $fechaApertura = $request->post('fecha_apertura', '');
        $fechaCierre = $request->post('fecha_cierre', '');
        $estado = $request->post('estado', 'publicada');
        $formacionMinima = trim($request->post('formacion_minima', ''));
        $experienciaMeses = max(0, (int)$request->post('experiencia_minima_meses', 12));
        $competencias = trim($request->post('competencias', ''));
        $funciones = trim($request->post('funciones', ''));
        $condiciones = trim($request->post('condiciones', ''));

        $_SESSION['form_convocatoria'] = [
            'codigo' => $codigo,
            'cargo' => $cargo,
            'dependencia_id' => $dependenciaId,
            'facultad' => $facultad,
            'programa' => $programa,
            'sede' => $sede,
            'periodo_academico' => $periodo,
            'vacantes' => $vacantes,
            'modalidad' => $modalidad,
            'tipo_convocatoria' => $tipoConv,
            'descripcion' => $descripcion,
            'fecha_apertura' => $fechaApertura,
            'fecha_cierre' => $fechaCierre,
            'estado' => $estado,
            'formacion_minima' => $formacionMinima,
            'experiencia_minima_meses' => $experienciaMeses,
            'competencias' => $competencias,
            'funciones' => $funciones,
            'condiciones' => $condiciones
        ];

        if (empty($cargo) || empty($dependenciaId) || empty($fechaApertura) || empty($fechaCierre) || empty($formacionMinima)) {
            AuthHelper::setFlash('danger', 'Por favor diligencie todos los campos requeridos (Cargo, Dependencia, Fechas y Formación).');
            Response::redirect('c=Convocatoria&a=crear');
        }

        if (strtotime($fechaCierre) <= strtotime($fechaApertura)) {
            AuthHelper::setFlash('danger', 'La fecha de cierre debe ser posterior a la fecha de apertura.');
            Response::redirect('c=Convocatoria&a=crear');
        }

        if (empty($codigo)) {
            $codigo = Convocatoria::generarCodigoUnico($tipoConv);
        }

        if (Convocatoria::codigoExiste($codigo)) {
            AuthHelper::setFlash('danger', "El código de convocatoria '{$codigo}' ya está en uso. Por favor elija un código diferente o use el código automático.");
            Response::redirect('c=Convocatoria&a=crear');
        }

        try {
            $convId = Convocatoria::crear([
                'codigo' => $codigo,
                'cargo' => $cargo,
                'dependencia_id' => $dependenciaId,
                'facultad' => $facultad,
                'programa' => $programa,
                'sede' => $sede,
                'periodo_academico' => $periodo,
                'vacantes' => $vacantes,
                'modalidad' => $modalidad,
                'tipo_convocatoria' => $tipoConv,
                'descripcion' => $descripcion,
                'fecha_apertura' => $fechaApertura,
                'fecha_cierre' => $fechaCierre,
                'estado' => $estado,
                'formacion_minima' => $formacionMinima,
                'experiencia_minima_meses' => $experienciaMeses,
                'competencias' => $competencias,
                'funciones' => $funciones,
                'condiciones' => $condiciones,
                'creado_por' => AuthHelper::getUsuarioId()
            ]);

            unset($_SESSION['form_convocatoria']);

            AuditHelper::log('crear_convocatoria', 'convocatorias', $convId, null, [
                'codigo' => $codigo,
                'cargo' => $cargo,
                'vacantes' => $vacantes
            ]);

            AuthHelper::setFlash('success', "¡Convocatoria '{$codigo} - {$cargo}' creada y guardada permanentemente con {$vacantes} vacante(s)!");
            Response::redirect('c=Convocatoria&a=detalle&id=' . $convId);
        } catch (Throwable $e) {
            AuthHelper::setFlash('danger', 'Error al registrar la convocatoria en la base de datos: ' . $e->getMessage());
            Response::redirect('c=Convocatoria&a=crear');
        }
    }

    public function editar(Request $request): void {
        AuthHelper::exigirRol(['admin', 'talento_humano']);

        $id = (int)$request->get('id');
        $convocatoria = Convocatoria::findById($id);

        if (!$convocatoria) {
            AuthHelper::setFlash('danger', 'Convocatoria no encontrada.');
            Response::redirect('c=Convocatoria&a=index');
        }

        $pdo = Database::getConnection();
        $dependencias = $pdo->query("SELECT * FROM dependencias WHERE activa = 1 ORDER BY nombre ASC")->fetchAll();

        View::render('convocatorias.editar', [
            'convocatoria' => $convocatoria,
            'dependencias' => $dependencias,
            'csrf_token' => $request->getCsrfToken()
        ], 'main');
    }

    public function actualizar(Request $request): void {
        AuthHelper::exigirRol(['admin', 'talento_humano']);

        $id = (int)$request->post('id');
        $convocatoria = Convocatoria::findById($id);
        if (!$convocatoria) {
            AuthHelper::setFlash('danger', 'Convocatoria no encontrada.');
            Response::redirect('c=Convocatoria&a=index');
        }

        $codigo = strtoupper(trim($request->post('codigo', '')));
        $cargo = trim($request->post('cargo', ''));
        $dependenciaId = (int)$request->post('dependencia_id', 0);
        $facultad = trim($request->post('facultad', ''));
        $programa = trim($request->post('programa', ''));
        $sede = trim($request->post('sede', 'Sincelejo'));
        $periodo = trim($request->post('periodo_academico', '2026-2'));
        $vacantes = max(1, (int)$request->post('vacantes', 1));
        $modalidad = trim($request->post('modalidad', 'Tiempo Completo'));
        $tipoConv = trim($request->post('tipo_convocatoria', 'Docente'));
        $descripcion = trim($request->post('descripcion', ''));
        $fechaApertura = $request->post('fecha_apertura', '');
        $fechaCierre = $request->post('fecha_cierre', '');
        $estado = $request->post('estado', 'publicada');
        $formacionMinima = trim($request->post('formacion_minima', ''));
        $experienciaMeses = max(0, (int)$request->post('experiencia_minima_meses', 12));
        $competencias = trim($request->post('competencias', ''));
        $funciones = trim($request->post('funciones', ''));
        $condiciones = trim($request->post('condiciones', ''));

        if (empty($codigo) || empty($cargo) || empty($dependenciaId) || empty($fechaApertura) || empty($fechaCierre)) {
            AuthHelper::setFlash('danger', 'Todos los campos obligatorios deben ser diligenciados.');
            Response::redirect('c=Convocatoria&a=editar&id=' . $id);
        }

        if (Convocatoria::codigoExiste($codigo, $id)) {
            AuthHelper::setFlash('danger', "El código '{$codigo}' ya pertenece a otra convocatoria.");
            Response::redirect('c=Convocatoria&a=editar&id=' . $id);
        }

        try {
            Convocatoria::actualizar($id, [
                'codigo' => $codigo,
                'cargo' => $cargo,
                'dependencia_id' => $dependenciaId,
                'facultad' => $facultad,
                'programa' => $programa,
                'sede' => $sede,
                'periodo_academico' => $periodo,
                'vacantes' => $vacantes,
                'modalidad' => $modalidad,
                'tipo_convocatoria' => $tipoConv,
                'descripcion' => $descripcion,
                'fecha_apertura' => $fechaApertura,
                'fecha_cierre' => $fechaCierre,
                'estado' => $estado,
                'formacion_minima' => $formacionMinima,
                'experiencia_minima_meses' => $experienciaMeses,
                'competencias' => $competencias,
                'funciones' => $funciones,
                'condiciones' => $condiciones
            ]);

            AuditHelper::log('actualizar_convocatoria', 'convocatorias', $id);
            AuthHelper::setFlash('success', "Convocatoria '{$codigo}' actualizada exitosamente.");
            Response::redirect('c=Convocatoria&a=detalle&id=' . $id);
        } catch (Throwable $e) {
            AuthHelper::setFlash('danger', 'Error al actualizar convocatoria: ' . $e->getMessage());
            Response::redirect('c=Convocatoria&a=editar&id=' . $id);
        }
    }

    public function cambiarEstado(Request $request): void {
        AuthHelper::exigirRol(['admin', 'talento_humano']);

        $id = (int)$request->post('id');
        $estado = $request->post('estado');
        $motivo = $request->post('motivo_retiro', null);

        Convocatoria::cambiarEstado($id, $estado, $motivo);
        AuditHelper::log('cambiar_estado_convocatoria', 'convocatorias', $id, null, ['estado' => $estado, 'motivo' => $motivo]);

        AuthHelper::setFlash('success', "Estado de la convocatoria modificado a: {$estado}.");
        Response::redirect('c=Convocatoria&a=detalle&id=' . $id);
    }

    public function eliminar(Request $request): void {
        AuthHelper::exigirRol(['admin', 'talento_humano']);

        $id = (int)$request->post('id');
        if (!$id) {
            AuthHelper::setFlash('danger', 'Identificador de convocatoria inválido.');
            Response::redirect('c=Convocatoria&a=index');
        }

        $convocatoria = Convocatoria::findById($id);
        if (!$convocatoria) {
            AuthHelper::setFlash('warning', 'La convocatoria a eliminar no existe.');
            Response::redirect('c=Convocatoria&a=index');
        }

        try {
            Convocatoria::eliminar($id);

            AuditHelper::log('eliminar_convocatoria_permanente', 'convocatorias', $id, [
                'codigo' => $convocatoria['codigo'],
                'cargo' => $convocatoria['cargo']
            ], null);

            AuthHelper::setFlash('success', "La convocatoria #{$id} ({$convocatoria['codigo']} - {$convocatoria['cargo']}) ha sido eliminada para siempre de la base de datos.");
            Response::redirect('c=Convocatoria&a=index');
        } catch (Throwable $e) {
            AuthHelper::setFlash('danger', 'Error al eliminar convocatoria de la base de datos: ' . $e->getMessage());
            Response::redirect('c=Convocatoria&a=index');
        }
    }
}

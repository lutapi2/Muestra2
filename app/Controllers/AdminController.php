<?php

class AdminController {
    public function usuarios(Request $request): void {
        AuthHelper::exigirRol(['admin']);

        $usuarios = Usuario::getAll();
        $pdo = Database::getConnection();
        $roles = $pdo->query("SELECT * FROM roles ORDER BY nombre ASC")->fetchAll();
        $dependencias = $pdo->query("SELECT * FROM dependencias WHERE activa = 1 ORDER BY nombre ASC")->fetchAll();

        View::render('admin.usuarios', [
            'usuarios' => $usuarios,
            'roles' => $roles,
            'dependencias' => $dependencias,
            'csrf_token' => $request->getCsrfToken()
        ], 'main');
    }

    public function guardarUsuario(Request $request): void {
        AuthHelper::exigirRol(['admin']);

        $nombres = trim($request->post('nombres', ''));
        $apellidos = trim($request->post('apellidos', ''));
        $tipoDoc = trim($request->post('tipo_documento', 'CC'));
        $numeroDoc = trim($request->post('numero_documento', ''));
        $correo = strtolower(trim($request->post('correo', '')));
        $telefono = trim($request->post('telefono', ''));
        $ciudad = trim($request->post('ciudad', 'Sincelejo'));
        $direccion = trim($request->post('direccion', 'Campus CECAR'));
        $rol = trim($request->post('rol', 'candidato'));
        $dependenciaId = $request->post('dependencia_id');
        $cargoInterno = trim($request->post('cargo_interno', ''));
        $password = $request->post('password', '');
        $activo = (int)$request->post('activo', 1);

        if (empty($nombres) || empty($apellidos) || empty($numeroDoc) || empty($correo)) {
            AuthHelper::setFlash('danger', 'Los campos Nombres, Apellidos, Documento y Correo son obligatorios.');
            Response::redirect('c=Admin&a=usuarios');
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            AuthHelper::setFlash('danger', 'El formato del correo electrónico no es válido.');
            Response::redirect('c=Admin&a=usuarios');
        }

        if (Usuario::emailExiste($correo)) {
            AuthHelper::setFlash('danger', "El correo '{$correo}' ya se encuentra registrado por otro usuario.");
            Response::redirect('c=Admin&a=usuarios');
        }

        if (Usuario::documentoExiste($numeroDoc)) {
            AuthHelper::setFlash('danger', "El documento '{$numeroDoc}' ya está registrado en el sistema.");
            Response::redirect('c=Admin&a=usuarios');
        }

        if (empty($password)) {
            $password = 'cecar' . date('Y');
        }

        try {
            $userId = Usuario::crearUsuarioAdmin([
                'nombres' => $nombres,
                'apellidos' => $apellidos,
                'tipo_documento' => $tipoDoc,
                'numero_documento' => $numeroDoc,
                'correo' => $correo,
                'telefono' => $telefono,
                'ciudad' => $ciudad,
                'direccion' => $direccion,
                'password' => $password,
                'rol' => $rol,
                'dependencia_id' => !empty($dependenciaId) ? (int)$dependenciaId : null,
                'cargo_interno' => $cargoInterno,
                'activo' => $activo
            ]);

            AuditHelper::log('crear_usuario_admin', 'usuarios', $userId, null, [
                'nombres' => $nombres . ' ' . $apellidos,
                'correo' => $correo,
                'rol' => $rol
            ]);

            AuthHelper::setFlash('success', "¡Usuario '{$nombres} {$apellidos}' registrado y guardado permanentemente con rol '{$rol}'!");
            Response::redirect('c=Admin&a=usuarios');
        } catch (Throwable $e) {
            AuthHelper::setFlash('danger', 'Error al registrar usuario: ' . $e->getMessage());
            Response::redirect('c=Admin&a=usuarios');
        }
    }

    public function eliminarUsuario(Request $request): void {
        AuthHelper::exigirRol(['admin']);

        $id = (int)$request->post('id');
        if (!$id) {
            AuthHelper::setFlash('danger', 'Identificador de usuario inválido.');
            Response::redirect('c=Admin&a=usuarios');
        }

        if ($id === AuthHelper::getUsuarioId()) {
            AuthHelper::setFlash('danger', 'No es posible eliminar su propio usuario con sesión activa.');
            Response::redirect('c=Admin&a=usuarios');
        }

        $usuario = Usuario::findById($id);
        if (!$usuario) {
            AuthHelper::setFlash('warning', 'El usuario a eliminar no existe en la base de datos.');
            Response::redirect('c=Admin&a=usuarios');
        }

        try {
            Usuario::eliminar($id);

            AuditHelper::log('eliminar_usuario_permanente', 'usuarios', $id, [
                'nombres' => $usuario['nombres'] . ' ' . $usuario['apellidos'],
                'correo' => $usuario['correo'],
                'rol' => $usuario['rol']
            ], null);

            AuthHelper::setFlash('success', "El usuario #{$id} ({$usuario['nombres']} {$usuario['apellidos']}) ha sido eliminado para siempre de la base de datos.");
            Response::redirect('c=Admin&a=usuarios');
        } catch (Throwable $e) {
            AuthHelper::setFlash('danger', 'Error al eliminar usuario de la base de datos: ' . $e->getMessage());
            Response::redirect('c=Admin&a=usuarios');
        }
    }

    public function catalogos(Request $request): void {
        AuthHelper::exigirRol(['admin']);

        $pdo = Database::getConnection();
        $catalogos = $pdo->query("SELECT * FROM catalogos ORDER BY grupo, orden ASC")->fetchAll();

        View::render('admin.catalogos', [
            'catalogos' => $catalogos
        ], 'main');
    }

    public function auditoria(Request $request): void {
        AuthHelper::exigirRol(['admin', 'auditor']);

        $pdo = Database::getConnection();
        $registros = $pdo->query("
            SELECT a.*, u.nombres, u.apellidos, u.correo, u.rol
            FROM auditoria a
            LEFT JOIN usuarios u ON a.usuario_id = u.id
            ORDER BY a.fecha_hora DESC LIMIT 100
        ")->fetchAll();

        View::render('admin.auditoria', [
            'registros' => $registros
        ], 'main');
    }
}

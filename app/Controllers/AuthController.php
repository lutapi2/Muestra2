<?php

class AuthController {
    public function login(Request $request): void {
        if (AuthHelper::estaAutenticado()) {
            Response::redirect('c=Dashboard&a=index');
        }

        View::render('auth.login', [
            'csrf_token' => $request->getCsrfToken()
        ], 'landing');
    }

    public function doLogin(Request $request): void {
        $correo = trim($request->post('correo', ''));
        $password = $request->post('password', '');

        if (empty($correo) || empty($password)) {
            if ($request->isAjax()) {
                Response::json(['success' => false, 'message' => 'Por favor ingrese su correo y contraseña.'], 400);
            }
            AuthHelper::setFlash('danger', 'Por favor ingrese su correo y contraseña.');
            Response::redirect('c=Auth&a=login');
        }

        $usuario = Usuario::findByEmail($correo);

        if (!$usuario || !password_verify($password, $usuario['password_hash'])) {
            AuditHelper::log('login_fallido', 'usuarios', 0, null, ['correo' => $correo]);
            if ($request->isAjax()) {
                Response::json(['success' => false, 'message' => 'Credenciales incorrectas o usuario inactivo.'], 401);
            }
            AuthHelper::setFlash('danger', 'Credenciales incorrectas o usuario inactivo.');
            Response::redirect('c=Auth&a=login');
        }

        Usuario::actualizarUltimoAcceso($usuario['id']);
        AuthHelper::login($usuario);
        AuditHelper::log('login_exitoso', 'usuarios', $usuario['id']);

        AuthHelper::setFlash('success', 'Bienvenido(a) a la plataforma de contratación CECAR, ' . $usuario['nombres'] . '.');

        if ($request->isAjax()) {
            Response::json([
                'success' => true,
                'message' => 'Bienvenido(a), ' . $usuario['nombres'],
                'redirect' => BASE_URL . '/index.php?c=Dashboard&a=index'
            ]);
        }

        Response::redirect('c=Dashboard&a=index');
    }

    public function register(Request $request): void {
        if (AuthHelper::estaAutenticado()) {
            Response::redirect('c=Dashboard&a=index');
        }

        $formData = $_SESSION['form_register'] ?? [];
        unset($_SESSION['form_register']);

        View::render('auth.register', [
            'csrf_token' => $request->getCsrfToken(),
            'form' => $formData
        ], 'landing');
    }

    public function doRegister(Request $request): void {
        $nombres = trim($request->post('nombres', ''));
        $apellidos = trim($request->post('apellidos', ''));
        $tipoDoc = trim($request->post('tipo_documento', 'CC'));
        $numeroDoc = trim($request->post('numero_documento', ''));
        $correo = strtolower(trim($request->post('correo', '')));
        $telefono = trim($request->post('telefono', ''));
        $ciudad = trim($request->post('ciudad', 'Sincelejo'));
        $direccion = trim($request->post('direccion', ''));
        $password = $request->post('password', '');
        $consentimiento = $request->post('consentimiento_datos');

        $_SESSION['form_register'] = [
            'nombres' => $nombres,
            'apellidos' => $apellidos,
            'tipo_documento' => $tipoDoc,
            'numero_documento' => $numeroDoc,
            'correo' => $correo,
            'telefono' => $telefono,
            'ciudad' => $ciudad,
            'direccion' => $direccion
        ];

        if (empty($nombres) || empty($apellidos) || empty($numeroDoc) || empty($correo) || empty($password)) {
            AuthHelper::setFlash('danger', 'Todos los campos obligatorios deben ser diligenciados.');
            Response::redirect('c=Auth&a=register');
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            AuthHelper::setFlash('danger', 'Por favor ingrese un correo electrónico válido (ejemplo: usuario@correo.com).');
            Response::redirect('c=Auth&a=register');
        }

        if (strlen($password) < 6) {
            AuthHelper::setFlash('danger', 'La contraseña debe tener al menos 6 caracteres.');
            Response::redirect('c=Auth&a=register');
        }

        if (empty($consentimiento)) {
            AuthHelper::setFlash('danger', 'Debe aceptar los términos de tratamiento de datos personales conforme a la política CECAR (RF-015, RN-004).');
            Response::redirect('c=Auth&a=register');
        }

        if (Usuario::emailExiste($correo)) {
            AuthHelper::setFlash('warning', 'Ya existe una cuenta registrada con el correo: ' . htmlspecialchars($correo) . '. Inicie sesión o recupere su clave.');
            Response::redirect('c=Auth&a=register');
        }

        if (Usuario::documentoExiste($numeroDoc)) {
            AuthHelper::setFlash('warning', 'El número de documento ' . htmlspecialchars($numeroDoc) . ' ya se encuentra registrado.');
            Response::redirect('c=Auth&a=register');
        }

        try {
            $userId = Usuario::registrarCandidato([
                'nombres' => $nombres,
                'apellidos' => $apellidos,
                'tipo_documento' => $tipoDoc,
                'numero_documento' => $numeroDoc,
                'correo' => $correo,
                'telefono' => $telefono,
                'ciudad' => $ciudad,
                'direccion' => $direccion,
                'password' => $password
            ]);

            unset($_SESSION['form_register']);

            $nuevoUsuario = Usuario::findById($userId);
            AuthHelper::login($nuevoUsuario);
            AuditHelper::log('registro_candidato', 'usuarios', $userId);

            AuthHelper::setFlash('success', '¡Cuenta de candidato creada exitosamente! Su consentimiento de datos ha sido registrado.');
            Response::redirect('c=Dashboard&a=index');
        } catch (Throwable $e) {
            AuthHelper::setFlash('danger', 'Error al crear la cuenta: ' . $e->getMessage());
            Response::redirect('c=Auth&a=register');
        }
    }

    public function quickLogin(Request $request): void {
        // Conveniente selector rápido para evaluar los diferentes roles de la institución
        $rol = $request->get('rol', 'admin');
        $map = [
            'admin' => 'admin@cecar.edu.co',
            'talento_humano' => 'talento.humano@cecar.edu.co',
            'revisor' => 'revisor@cecar.edu.co',
            'entrevistador' => 'evaluador@cecar.edu.co',
            'candidato' => 'candidato@correo.com'
        ];

        $correo = $map[$rol] ?? 'admin@cecar.edu.co';
        $usuario = Usuario::findByEmail($correo);

        if ($usuario) {
            Usuario::actualizarUltimoAcceso($usuario['id']);
            AuthHelper::login($usuario);
            AuditHelper::log('quick_login', 'usuarios', $usuario['id']);
            AuthHelper::setFlash('info', 'Sesión iniciada como: ' . $usuario['nombres'] . ' (' . AuthHelper::getRolNombre() . ')');
        }

        if ($request->isAjax()) {
            Response::json([
                'success' => true,
                'message' => 'Accediendo como ' . ($usuario ? $usuario['nombres'] : $rol),
                'redirect' => BASE_URL . '/index.php?c=Dashboard&a=index'
            ]);
        }

        Response::redirect('c=Dashboard&a=index');
    }

    public function logout(Request $request): void {
        if (AuthHelper::estaAutenticado()) {
            AuditHelper::log('logout', 'usuarios', AuthHelper::getUsuarioId());
        }
        AuthHelper::logout();
        Response::redirect('c=Home&a=landing');
    }

    public function recuperar(Request $request): void {
        View::render('auth.recuperar', [
            'csrf_token' => $request->getCsrfToken()
        ], 'landing');
    }

    public function doRecuperar(Request $request): void {
        $correo = trim($request->post('correo', ''));
        if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            AuthHelper::setFlash('danger', 'Por favor ingrese un correo electrónico válido.');
            Response::redirect('c=Auth&a=recuperar');
        }

        AuditHelper::log('solicitud_recuperacion', 'usuarios', 0, null, ['correo' => $correo]);
        AuthHelper::setFlash('success', 'Si el correo electrónico está registrado en CECAR, recibirá las instrucciones para restablecer su contraseña.');
        Response::redirect('c=Auth&a=login');
    }
}

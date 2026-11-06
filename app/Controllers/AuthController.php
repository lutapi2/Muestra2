<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Helpers\AuthHelper;
use App\Helpers\AuditHelper;
use App\Models\Usuario;

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
            AuthHelper::setFlash('danger', 'Por favor ingrese su correo y contraseña.');
            Response::redirect('c=Auth&a=login');
        }

        $usuario = Usuario::findByEmail($correo);

        if (!$usuario || !password_verify($password, $usuario['password_hash'])) {
            AuditHelper::log('login_fallido', 'usuarios', 0, null, ['correo' => $correo]);
            AuthHelper::setFlash('danger', 'Credenciales incorrectas o usuario inactivo.');
            Response::redirect('c=Auth&a=login');
        }

        Usuario::actualizarUltimoAcceso($usuario['id']);
        AuthHelper::login($usuario);
        AuditHelper::log('login_exitoso', 'usuarios', $usuario['id']);

        AuthHelper::setFlash('success', 'Bienvenido(a) a la plataforma de contratación CECAR, ' . $usuario['nombres'] . '.');
        Response::redirect('c=Dashboard&a=index');
    }

    public function register(Request $request): void {
        if (AuthHelper::estaAutenticado()) {
            Response::redirect('c=Dashboard&a=index');
        }

        View::render('auth.register', [
            'csrf_token' => $request->getCsrfToken()
        ], 'landing');
    }

    public function doRegister(Request $request): void {
        $nombres = trim($request->post('nombres', ''));
        $apellidos = trim($request->post('apellidos', ''));
        $numeroDoc = trim($request->post('numero_documento', ''));
        $correo = trim($request->post('correo', ''));
        $password = $request->post('password', '');
        $consentimiento = $request->post('consentimiento_datos');

        if (empty($nombres) || empty($apellidos) || empty($numeroDoc) || empty($correo) || empty($password)) {
            AuthHelper::setFlash('danger', 'Todos los campos obligatorios deben ser diligenciados.');
            Response::redirect('c=Auth&a=register');
        }

        if (empty($consentimiento)) {
            AuthHelper::setFlash('danger', 'Debe aceptar los términos de tratamiento de datos personales conforme a la política CECAR (RF-015, RN-004).');
            Response::redirect('c=Auth&a=register');
        }

        if (Usuario::findByEmail($correo)) {
            AuthHelper::setFlash('warning', 'Ya existe una cuenta registrada con este correo electrónico.');
            Response::redirect('c=Auth&a=register');
        }

        try {
            $userId = Usuario::registrarCandidato([
                'nombres' => $nombres,
                'apellidos' => $apellidos,
                'tipo_documento' => $request->post('tipo_documento', 'CC'),
                'numero_documento' => $numeroDoc,
                'correo' => $correo,
                'telefono' => $request->post('telefono', ''),
                'ciudad' => $request->post('ciudad', 'Sincelejo'),
                'direccion' => $request->post('direccion', ''),
                'password' => $password
            ]);

            $nuevoUsuario = Usuario::findById($userId);
            AuthHelper::login($nuevoUsuario);
            AuditHelper::log('registro_candidato', 'usuarios', $userId);

            AuthHelper::setFlash('success', '¡Cuenta de candidato creada exitosamente! Su consentimiento de datos ha sido registrado.');
            Response::redirect('c=Dashboard&a=index');
        } catch (\Exception $e) {
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
        View::render('auth.recuperar', [], 'landing');
    }
}

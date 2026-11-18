<?php

declare(strict_types=1);

namespace Controller;

use Model\DAO\CandidatoDAO;
use Model\DAO\UsuarioDAO;
use Model\VO\CandidatoVO;
use Model\VO\UsuarioVO;

/**
 * Controlador de Autenticación y Registro de Candidatos.
 * Satisface:
 *   - RF-001: Registrar candidato (correo, contraseña, nombres, apellidos, documento, teléfono, ciudad).
 *   - RF-002: Iniciar sesión de candidato.
 *   - RN-004: Políticas de longitud y seguridad de contraseñas (mínimo 8 caracteres).
 *   - RNF-009: Hashing seguro de contraseñas con BCRYPT cost 12.
 *   - RNF-024: Manejo seguro sin revelación de datos confidenciales ni trazas internas.
 */
class AuthController extends BaseController
{
    public function __construct(
        private ?UsuarioDAO $usuarioDAO = null,
        private ?CandidatoDAO $candidatoDAO = null
    ) {
        $this->usuarioDAO = $this->usuarioDAO ?? new UsuarioDAO();
        $this->candidatoDAO = $this->candidatoDAO ?? new CandidatoDAO();
    }

    /**
     * Endpoint: POST /api/auth/register
     * Satisface: RF-001, RN-004, RNF-009.
     * Permite a un candidato postularse registrando sus credenciales y datos personales.
     */
    public function register(): void
    {
        $body = $this->getJsonBody();

        // 1. Validación de campos obligatorios requeridos por RF-001
        $requiredFields = [
            'correo',
            'password',
            'nombres',
            'apellidos',
            'id_tipo_documento',
            'numero_documento',
            'telefono',
            'ciudad',
        ];
        $errors = $this->validateRequired($body, $requiredFields);

        // 2. Validación de formato de correo
        $correo = trim((string)($body['correo'] ?? ''));
        if ($correo !== '' && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errors['correo'] = 'El formato del correo electrónico es inválido.';
        }

        // 3. Validación de complejidad mínima de contraseña (RN-004)
        $password = (string)($body['password'] ?? '');
        if ($password !== '' && strlen($password) < 8) {
            $errors['password'] = 'La contraseña debe tener una longitud mínima de 8 caracteres (RN-004).';
        }

        // 4. Validación de tipo de documento
        $idTipoDocumento = filter_var($body['id_tipo_documento'] ?? null, FILTER_VALIDATE_INT);
        if ($idTipoDocumento === false || $idTipoDocumento <= 0) {
            $errors['id_tipo_documento'] = 'El tipo de documento debe ser un identificador numérico válido mayor a 0.';
        }

        // 5. Validación de longitud de número de documento
        $numeroDocumento = trim((string)($body['numero_documento'] ?? ''));
        if ($numeroDocumento !== '' && (strlen($numeroDocumento) < 3 || strlen($numeroDocumento) > 20)) {
            $errors['numero_documento'] = 'El número de documento debe tener entre 3 y 20 caracteres.';
        }

        if (!empty($errors)) {
            $this->jsonResponse(
                success: false,
                status: 400,
                message: 'Error de validación en los datos del candidato.',
                error: $errors
            );
        }

        // 6. Validación de unicidad de correo electrónico
        if ($this->usuarioDAO->buscarPorCorreo($correo) !== null) {
            $this->jsonResponse(
                success: false,
                status: 400,
                message: 'El correo electrónico suministrado ya se encuentra registrado en el sistema.',
                error: ['correo' => 'El correo electrónico ya existe.']
            );
        }

        // 7. Validación de unicidad de documento de identidad
        if ($this->candidatoDAO->existeDocumento((int)$idTipoDocumento, $numeroDocumento)) {
            $this->jsonResponse(
                success: false,
                status: 400,
                message: 'Ya existe un candidato registrado con el mismo tipo y número de documento.',
                error: ['numero_documento' => 'Documento de identidad ya registrado en el sistema.']
            );
        }

        // 8. Creación atómica transaccional de Usuario + Candidato + Rol CANDIDATO (id 6)
        $this->usuarioDAO->beginTransaction();
        try {
            $nombres = trim((string)$body['nombres']);
            $apellidos = trim((string)$body['apellidos']);
            $nombreCompleto = $nombres . ' ' . $apellidos;
            $contrasenaHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

            // Persistir UsuarioVO
            $usuarioVO = new UsuarioVO(
                correo: strtolower($correo),
                contrasenaHash: $contrasenaHash,
                tipo: 'CANDIDATO',
                nombreCompleto: $nombreCompleto,
                idDependencia: null,
                estado: 'ACTIVO'
            );
            $usuarioCreado = $this->usuarioDAO->crear($usuarioVO);

            if ($usuarioCreado->idUsuario === null) {
                throw new \RuntimeException('Error al generar el identificador de usuario.');
            }

            // Persistir CandidatoVO
            $candidatoVO = new CandidatoVO(
                idUsuario: $usuarioCreado->idUsuario,
                nombres: $nombres,
                apellidos: $apellidos,
                idTipoDocumento: (int)$idTipoDocumento,
                numeroDocumento: $numeroDocumento,
                telefono: trim((string)$body['telefono']),
                ciudad: trim((string)$body['ciudad']),
                direccion: isset($body['direccion']) && trim((string)$body['direccion']) !== '' ? trim((string)$body['direccion']) : null
            );
            $candidatoCreado = $this->candidatoDAO->crear($candidatoVO);

            // Asignar rol institucional de CANDIDATO (id_rol = 6)
            $this->usuarioDAO->asignarRol($usuarioCreado->idUsuario, 6);

            $this->usuarioDAO->commit();

            // Respuesta 201 Created exitosa
            $this->jsonResponse(
                success: true,
                status: 201,
                data: [
                    'id_usuario'       => $usuarioCreado->idUsuario,
                    'id_candidato'     => $candidatoCreado->idCandidato,
                    'correo'           => $usuarioCreado->correo,
                    'nombre_completo'  => $usuarioCreado->nombreCompleto,
                    'tipo'             => $usuarioCreado->tipo,
                    'rol'              => 'CANDIDATO',
                    'estado'           => $usuarioCreado->estado,
                ],
                message: 'Candidato registrado exitosamente en el Sistema de Selección CECAR.'
            );
        } catch (\Throwable $e) {
            $this->usuarioDAO->rollBack();
            throw $e;
        }
    }

    /**
     * Endpoint: POST /api/auth/login
     * Satisface: RF-002, RN-004, RNF-009, RNF-012.
     * Autenticación de candidatos y verificación de estado activo.
     */
    public function login(): void
    {
        $body = $this->getJsonBody();

        $errors = $this->validateRequired($body, ['correo', 'password']);
        if (!empty($errors)) {
            $this->jsonResponse(
                success: false,
                status: 400,
                message: 'Debe ingresar correo y contraseña para autenticarse.',
                error: $errors
            );
        }

        $correo = trim((string)$body['correo']);
        $password = (string)$body['password'];

        $usuario = $this->usuarioDAO->buscarPorCorreo(strtolower($correo));
        if ($usuario === null || !password_verify($password, $usuario->contrasenaHash)) {
            $this->jsonResponse(
                success: false,
                status: 401,
                message: 'Credenciales inválidas. Por favor verifique el correo y la contraseña ingresados.',
                error: ['auth' => 'Acceso no autorizado.']
            );
        }

        if ($usuario->estado !== 'ACTIVO') {
            $this->jsonResponse(
                success: false,
                status: 403,
                message: 'La cuenta de usuario se encuentra inactiva o bloqueada. Comuníquese con Talento Humano CECAR.',
                error: ['estado' => $usuario->estado]
            );
        }

        // Actualizar último acceso
        if ($usuario->idUsuario !== null) {
            $this->usuarioDAO->actualizarUltimoAcceso($usuario->idUsuario);
        }

        // Si es candidato, obtener ficha de candidato
        $candidatoInfo = null;
        if ($usuario->idUsuario !== null && $usuario->tipo === 'CANDIDATO') {
            $candidato = $this->candidatoDAO->buscarPorUsuarioId($usuario->idUsuario);
            if ($candidato !== null) {
                $candidatoInfo = [
                    'id_candidato'      => $candidato->idCandidato,
                    'nombres'           => $candidato->nombres,
                    'apellidos'         => $candidato->apellidos,
                    'id_tipo_documento' => $candidato->idTipoDocumento,
                    'numero_documento'  => $candidato->numeroDocumento,
                    'telefono'          => $candidato->telefono,
                    'ciudad'            => $candidato->ciudad,
                ];
            }
        }

        // Generar token de sesión seguro
        $sessionToken = bin2hex(random_bytes(32));

        $this->jsonResponse(
            success: true,
            status: 200,
            data: [
                'token'           => $sessionToken,
                'id_usuario'      => $usuario->idUsuario,
                'correo'          => $usuario->correo,
                'nombre_completo' => $usuario->nombreCompleto,
                'tipo'            => $usuario->tipo,
                'estado'          => $usuario->estado,
                'candidato'       => $candidatoInfo,
            ],
            message: 'Autenticación exitosa.'
        );
    }
}

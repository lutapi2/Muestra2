<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Helpers\AuthHelper;
use App\Helpers\AuditHelper;
use App\Models\Usuario;

class AdminController {
    public function usuarios(Request $request): void {
        AuthHelper::exigirRol(['admin']);

        $usuarios = Usuario::getAll();
        $pdo = \App\Core\Database::getConnection();
        $roles = $pdo->query("SELECT * FROM roles")->fetchAll();
        $dependencias = $pdo->query("SELECT * FROM dependencias WHERE activa = 1")->fetchAll();

        View::render('admin.usuarios', [
            'usuarios' => $usuarios,
            'roles' => $roles,
            'dependencias' => $dependencias
        ], 'main');
    }

    public function catalogos(Request $request): void {
        AuthHelper::exigirRol(['admin']);

        $pdo = \App\Core\Database::getConnection();
        $catalogos = $pdo->query("SELECT * FROM catalogos ORDER BY grupo, orden ASC")->fetchAll();

        View::render('admin.catalogos', [
            'catalogos' => $catalogos
        ], 'main');
    }

    public function auditoria(Request $request): void {
        AuthHelper::exigirRol(['admin', 'auditor']);

        $pdo = \App\Core\Database::getConnection();
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

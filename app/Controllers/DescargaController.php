<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Helpers\AuthHelper;
use App\Helpers\FileHelper;

class DescargaController {
    public function ver(Request $request): void {
        AuthHelper::exigirAutenticacion();

        $soporteId = (int)$request->get('id');

        $pdo = \App\Core\Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT s.*, p.usuario_id
            FROM soportes s
            JOIN postulaciones p ON s.postulacion_id = p.id
            WHERE s.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $soporteId]);
        $soporte = $stmt->fetch();

        if (!$soporte) {
            http_response_code(404);
            die("Documento soporte no encontrado en base de datos.");
        }

        // Authorization check (RNF-010, RNF-013)
        $usuarioActualId = AuthHelper::getUsuarioId();
        if (AuthHelper::esCandidato() && $soporte['usuario_id'] != $usuarioActualId) {
            http_response_code(403);
            die("Acceso no autorizado a este documento.");
        }

        FileHelper::asegurarArchivosSemilla();

        $filePath = ROOT_PATH . '/' . $soporte['ruta'];
        if (!file_exists($filePath)) {
            // Check in uploads directly
            $filePath = UPLOADS_PATH . '/' . $soporte['nombre_almacenado'];
        }

        if (!file_exists($filePath)) {
            // Generate dummy PDF if not exists
            FileHelper::asegurarArchivosSemilla();
        }

        Response::download($filePath, $soporte['nombre_original'], $soporte['mime']);
    }
}

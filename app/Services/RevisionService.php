<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Helpers\AuditHelper;
use App\Helpers\NotifyHelper;
use App\Models\Postulacion;
use Exception;

class RevisionService {
    public function registrarCalificacionItem(int $itemId, int $postulacionId, int $revisorId, string $resultado, string $motivo, string $observacion): void {
        $pdo = Database::getConnection();

        $stmtRev = $pdo->prepare("
            INSERT INTO revisiones_item (item_hv_id, revisor_id, resultado, motivo, observacion, fecha_revision)
            VALUES (:item_id, :revisor_id, :resultado, :motivo, :observacion, CURRENT_TIMESTAMP)
        ");
        $stmtRev->execute([
            ':item_id' => $itemId,
            ':revisor_id' => $revisorId,
            ':resultado' => $resultado,
            ':motivo' => $motivo,
            ':observacion' => $observacion
        ]);

        $reqSub = ($resultado === 'observado') ? 1 : 0;
        $pdo->prepare("UPDATE items_hv SET estado = :estado, requiere_subsanacion = :req WHERE id = :id")
            ->execute([':estado' => $resultado, ':req' => $reqSub, ':id' => $itemId]);

        $this->recalcularEstadoDocumental($postulacionId);
    }

    public function recalcularEstadoDocumental(int $postulacionId): string {
        $pdo = Database::getConnection();

        $total = (int)$pdo->query("SELECT COUNT(*) FROM items_hv WHERE postulacion_id = $postulacionId")->fetchColumn();
        $aprobados = (int)$pdo->query("SELECT COUNT(*) FROM items_hv WHERE postulacion_id = $postulacionId AND estado = 'aprobado'")->fetchColumn();
        $observados = (int)$pdo->query("SELECT COUNT(*) FROM items_hv WHERE postulacion_id = $postulacionId AND estado = 'observado'")->fetchColumn();
        $rechazados = (int)$pdo->query("SELECT COUNT(*) FROM items_hv WHERE postulacion_id = $postulacionId AND estado = 'rechazado'")->fetchColumn();

        $estadoDoc = 'pendiente';
        if ($aprobados === $total && $total > 0) {
            $estadoDoc = 'conforme';
            $pdo->prepare("UPDATE postulaciones SET resultado_documental = 'conforme', estado = 'habilitada' WHERE id = :id")->execute([':id' => $postulacionId]);
        } elseif ($rechazados > 0) {
            $estadoDoc = 'no_conforme';
            $pdo->prepare("UPDATE postulaciones SET resultado_documental = 'no_conforme' WHERE id = :id")->execute([':id' => $postulacionId]);
        } elseif ($observados > 0) {
            $estadoDoc = 'observado';
            $pdo->prepare("UPDATE postulaciones SET resultado_documental = 'observado' WHERE id = :id")->execute([':id' => $postulacionId]);
        }

        return $estadoDoc;
    }
}

<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class ItemHV {
    public static function getByPostulacion(int $postulacionId): array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT i.*, 
                   s.id as soporte_id, s.nombre_original, s.nombre_almacenado, s.ruta, s.tamano_bytes, s.mime,
                   r.resultado as revision_resultado, r.motivo as revision_motivo, r.observacion as revision_observacion
            FROM items_hv i
            LEFT JOIN soportes s ON s.item_hv_id = i.id
            LEFT JOIN revisiones_item r ON r.item_hv_id = i.id
            WHERE i.postulacion_id = :postulacion_id
            ORDER BY i.categoria, i.id ASC
        ");
        $stmt->execute([':postulacion_id' => $postulacionId]);
        return $stmt->fetchAll();
    }

    public static function getOrganizadosPorCategoria(int $postulacionId): array {
        $items = self::getByPostulacion($postulacionId);
        $categorias = [
            'contrato' => ['titulo' => 'Contrato', 'items' => []],
            'informacion_personal' => ['titulo' => 'Información Personal', 'items' => []],
            'formacion_academica' => ['titulo' => 'Formación Académica', 'items' => []],
            'experiencia_laboral' => ['titulo' => 'Experiencia Laboral', 'items' => []],
            'seguridad_social' => ['titulo' => 'Seguridad Social', 'items' => []],
            'informacion_bancaria' => ['titulo' => 'Información Bancaria', 'items' => []],
            'salud_ocupacional' => ['titulo' => 'Salud Ocupacional', 'items' => []],
            'antecedentes_legales' => ['titulo' => 'Antecedentes Legales', 'items' => []]
        ];

        foreach ($items as $item) {
            $cat = $item['categoria'];
            if (!isset($categorias[$cat])) {
                $categorias[$cat] = ['titulo' => ucwords(str_replace('_', ' ', $cat)), 'items' => []];
            }
            $categorias[$cat]['items'][] = $item;
        }

        return $categorias;
    }

    public static function findById(int $id): ?array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT i.*, s.id as soporte_id, s.nombre_original, s.ruta, s.mime, s.tamano_bytes
            FROM items_hv i
            LEFT JOIN soportes s ON s.item_hv_id = i.id
            WHERE i.id = :id LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public static function registrar(array $datos, ?array $archivoSubido = null): int {
        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare("
                INSERT INTO items_hv (postulacion_id, categoria, tipo_item, titulo, datos_json, version, estado, fecha_registro)
                VALUES (:postulacion_id, :categoria, :tipo_item, :titulo, :datos_json, :version, :estado, CURRENT_TIMESTAMP)
            ");
            $stmt->execute([
                ':postulacion_id' => (int)$datos['postulacion_id'],
                ':categoria' => $datos['categoria'],
                ':tipo_item' => $datos['tipo_item'],
                ':titulo' => $datos['titulo'],
                ':datos_json' => is_string($datos['datos_json']) ? $datos['datos_json'] : json_encode($datos['datos_json'], JSON_UNESCAPED_UNICODE),
                ':version' => (int)($datos['version'] ?? 1),
                ':estado' => $datos['estado'] ?? 'borrador'
            ]);

            $itemId = (int)$pdo->lastInsertId();

            if ($archivoSubido) {
                $stmtSop = $pdo->prepare("
                    INSERT INTO soportes (item_hv_id, postulacion_id, nombre_original, nombre_almacenado, ruta, mime, tamano_bytes, hash_sha256, version)
                    VALUES (:item_hv_id, :postulacion_id, :nombre_original, :nombre_almacenado, :ruta, :mime, :tamano_bytes, :hash_sha256, 1)
                ");
                $stmtSop->execute([
                    ':item_hv_id' => $itemId,
                    ':postulacion_id' => (int)$datos['postulacion_id'],
                    ':nombre_original' => $archivoSubido['nombre_original'],
                    ':nombre_almacenado' => $archivoSubido['nombre_almacenado'],
                    ':ruta' => $archivoSubido['ruta'],
                    ':mime' => $archivoSubido['mime'],
                    ':tamano_bytes' => (int)$archivoSubido['tamano_bytes'],
                    ':hash_sha256' => $archivoSubido['hash_sha256']
                ]);
            }

            $pdo->commit();
            return $itemId;
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function actualizar(int $id, array $datos, ?array $archivoSubido = null): void {
        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare("
                UPDATE items_hv 
                SET titulo = :titulo, datos_json = :datos_json, estado = :estado, requiere_subsanacion = 0
                WHERE id = :id
            ");
            $stmt->execute([
                ':id' => $id,
                ':titulo' => $datos['titulo'],
                ':datos_json' => is_string($datos['datos_json']) ? $datos['datos_json'] : json_encode($datos['datos_json'], JSON_UNESCAPED_UNICODE),
                ':estado' => $datos['estado'] ?? 'enviado'
            ]);

            if ($archivoSubido) {
                // Delete old or replace
                $stmtSop = $pdo->prepare("
                    INSERT INTO soportes (item_hv_id, postulacion_id, nombre_original, nombre_almacenado, ruta, mime, tamano_bytes, hash_sha256, version)
                    VALUES (:item_hv_id, :postulacion_id, :nombre_original, :nombre_almacenado, :ruta, :mime, :tamano_bytes, :hash_sha256, 2)
                ");
                $stmtSop->execute([
                    ':item_hv_id' => $id,
                    ':postulacion_id' => (int)$datos['postulacion_id'],
                    ':nombre_original' => $archivoSubido['nombre_original'],
                    ':nombre_almacenado' => $archivoSubido['nombre_almacenado'],
                    ':ruta' => $archivoSubido['ruta'],
                    ':mime' => $archivoSubido['mime'],
                    ':tamano_bytes' => (int)$archivoSubido['tamano_bytes'],
                    ':hash_sha256' => $archivoSubido['hash_sha256']
                ]);
            }

            $pdo->commit();
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}

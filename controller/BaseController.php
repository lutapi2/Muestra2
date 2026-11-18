<?php

declare(strict_types=1);

namespace Controller;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;

/**
 * Controlador Base del Sistema de Selección CECAR.
 * Centraliza la lectura de payloads JSON, emisión de respuestas HTTP estandarizadas
 * y cabeceras de seguridad requeridas por RNF-009, RNF-024.
 */
abstract class BaseController
{
    /**
     * Obtiene y decodifica el cuerpo de la petición HTTP en formato JSON.
     * Si el JSON es inválido, emite automáticamente respuesta 400 Bad Request.
     *
     * @return array<string, mixed>
     */
    protected function getJsonBody(): array
    {
        $raw = file_get_contents('php://input');
        if ($raw === false || trim($raw) === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->jsonResponse(
                success: false,
                status: 400,
                message: 'Cuerpo de la petición inválido: el JSON no tiene una sintaxis válida.',
                error: ['json_error' => json_last_error_msg()]
            );
        }

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Emite una respuesta JSON estructurada con código HTTP estándar.
     *
     * @param bool $success Indica si la operación fue exitosa
     * @param int $status Código de estado HTTP (200, 201, 400, 401, 403, 404, 405, 500)
     * @param mixed $data Datos devueltos al cliente
     * @param string|null $message Mensaje descriptivo de la operación
     * @param array<string, mixed>|null $error Detalle de errores de validación o del cliente
     */
    protected function jsonResponse(
        bool $success,
        int $status,
        mixed $data = null,
        ?string $message = null,
        ?array $error = null
    ): void {
        http_response_code($status);
        header('Content-Type: application/json; charset=UTF-8');
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');

        $timezone = new DateTimeZone('America/Bogota');
        $now = new DateTimeImmutable('now', $timezone);

        $payload = [
            'success'   => $success,
            'status'    => $status,
            'message'   => $message,
            'data'      => $data,
            'error'     => $error,
            'timestamp' => $now->format(DateTimeInterface::ATOM),
        ];

        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Valida la presencia de campos obligatorios en un arreglo asociativo.
     * Retorna lista de errores si faltan campos o están vacíos.
     *
     * @param array<string, mixed> $data
     * @param list<string> $requiredFields
     * @return array<string, string>
     */
    protected function validateRequired(array $data, array $requiredFields): array
    {
        $errors = [];
        foreach ($requiredFields as $field) {
            if (!array_key_exists($field, $data) || $data[$field] === null || trim((string)$data[$field]) === '') {
                $errors[$field] = "El campo '{$field}' es obligatorio.";
            }
        }
        return $errors;
    }
}

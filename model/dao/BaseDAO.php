<?php

declare(strict_types=1);

namespace Model\DAO;

use PDO;

/**
 * Clase base para todos los Data Access Objects (DAO).
 * Proporciona acceso a la conexión PDO y gestión transaccional ACID.
 */
abstract class BaseDAO
{
    protected readonly PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? \Config\Database::getConnection();
    }

    /**
     * Inicia una transacción si no hay una activa.
     */
    public function beginTransaction(): bool
    {
        if (!$this->db->inTransaction()) {
            return $this->db->beginTransaction();
        }
        return true;
    }

    /**
     * Confirma la transacción actual.
     */
    public function commit(): bool
    {
        if ($this->db->inTransaction()) {
            return $this->db->commit();
        }
        return true;
    }

    /**
     * Revierte la transacción actual.
     */
    public function rollBack(): bool
    {
        if ($this->db->inTransaction()) {
            return $this->db->rollBack();
        }
        return true;
    }

    /**
     * Verifica si existe una transacción activa.
     */
    public function inTransaction(): bool
    {
        return $this->db->inTransaction();
    }

    /**
     * Retorna el último ID autoincremental generado.
     */
    protected function lastInsertId(): int
    {
        return (int)$this->db->lastInsertId();
    }
}

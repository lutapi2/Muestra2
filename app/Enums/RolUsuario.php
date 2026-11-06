<?php
declare(strict_types=1);

namespace App\Enums;

enum RolUsuario: string {
    case ADMIN = 'admin';
    case TALENTO_HUMANO = 'talento_humano';
    case REVISOR = 'revisor';
    case ENTREVISTADOR = 'entrevistador';
    case DESARROLLO_PROFESORAL = 'desarrollo_profesoral';
    case CANDIDATO = 'candidato';
    case AUDITOR = 'auditor';

    public function label(): string {
        return match($this) {
            self::ADMIN => 'Administrador',
            self::TALENTO_HUMANO => 'Talento Humano',
            self::REVISOR => 'Revisor Documental',
            self::ENTREVISTADOR => 'Evaluador / Comité',
            self::DESARROLLO_PROFESORAL => 'Desarrollo Profesoral',
            self::CANDIDATO => 'Aspirante / Candidato',
            self::AUDITOR => 'Auditor Institucional',
        };
    }
}

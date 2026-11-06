<?php
namespace App\Helpers;

class FileHelper {
    public static function guardarArchivo(array $file): array {
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new \Exception('Parámetros de archivo inválidos.');
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new \Exception('No se seleccionó ningún archivo.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new \Exception('El archivo excede el tamaño máximo permitido por el servidor.');
            default:
                throw new \Exception('Error desconocido al subir el archivo.');
        }

        if ($file['size'] > MAX_FILE_SIZE_BYTES) {
            throw new \Exception('El archivo supera el tamaño máximo permitido de 10 MB.');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);

        if (!array_key_exists($mime, ALLOWED_MIME_TYPES)) {
            throw new \Exception('Formato de archivo no permitido. Solo se aceptan PDF, JPG o PNG.');
        }

        $extension = ALLOWED_MIME_TYPES[$mime];
        $safeName = sprintf('%s_%s.%s', uniqid('soporte_', true), bin2hex(random_bytes(4)), $extension);
        $destination = UPLOADS_PATH . '/' . $safeName;

        if (!is_dir(UPLOADS_PATH)) {
            mkdir(UPLOADS_PATH, 0777, true);
        }

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new \Exception('Error al mover el archivo al almacenamiento seguro.');
        }

        $hash = hash_file('sha256', $destination);

        return [
            'nombre_original' => basename($file['name']),
            'nombre_almacenado' => $safeName,
            'ruta' => 'storage/uploads/' . $safeName,
            'mime' => $mime,
            'tamano_bytes' => (int)$file['size'],
            'hash_sha256' => $hash
        ];
    }

    public static function asegurarArchivosSemilla(): void {
        if (!is_dir(UPLOADS_PATH)) {
            mkdir(UPLOADS_PATH, 0777, true);
        }

        $seedFiles = [
            'sop_1_contrato.pdf' => 'CECAR - Minuta de Contrato Laboral Docente Cátedra 2026-2. Documento válido y legalizado.',
            'sop_2_cedula.pdf' => 'CECAR - Copia de Documento de Identidad (Cédula de Ciudadanía Colombiana).',
            'sop_3_hv.pdf' => 'CECAR - Hoja de Vida Formato Institucional F-TH-02.',
            'sop_4_residencia.pdf' => 'CECAR - Certificado de Residencia Municipal Sincelejo Sucre.',
            'sop_5_diploma_preg.pdf' => 'CECAR - Diploma de Pregrado Licenciatura en Pedagogía.',
            'sop_6_acta_preg.pdf' => 'CECAR - Acta de Grado Pregrado N° 1542.',
            'sop_7_diploma_posg.pdf' => 'CECAR - Diploma de Posgrado Maestría en Educación.',
            'sop_8_acta_posg.pdf' => 'CECAR - Acta de Grado Maestría en Educación N° 2810.',
            'sop_9_diplomado.pdf' => 'CECAR - Certificación Diplomado Docencia Universitaria CECAR.',
            'sop_10_experiencia.pdf' => 'CECAR - Certificación de Experiencia Docente Universitaria (3 años y medio).',
            'sop_11_eps.pdf' => 'CECAR - Certificado de Afiliación Activa EPS Sanitas.',
            'sop_12_pension.pdf' => 'CECAR - Certificado de Afiliación a Pensiones y Cesantías Porvenir.',
            'sop_13_davivienda.pdf' => 'CECAR - Certificación Bancaria Apertura de Cuenta de Ahorros Banco Davivienda.',
            'sop_14_examen_sst.pdf' => 'CECAR - Concepto Médico Ocupacional de Ingreso: APTO.',
            'sop_15_procuraduria.pdf' => 'CECAR - Certificado de Antecedentes Disciplinarios Procuraduría.',
            'sop_16_contraloria.pdf' => 'CECAR - Certificado de Antecedentes Fiscales Contraloría.',
            'sop_17_policia.pdf' => 'CECAR - Certificado de Antecedentes Judiciales Policía Nacional.',
            'sop_18_rnmc.pdf' => 'CECAR - Certificado de Medidas Correctivas Policía Nacional RNMC.',
            'sop_19_ley1918.pdf' => 'CECAR - Certificado Inhabilidades por Delitos Sexuales Ley 1918.',
            'sop_20_redam.pdf' => 'CECAR - Certificado Deudores Alimentarios Morosos REDAM.',
            'sop_21_tp.pdf' => 'CECAR - Tarjeta Profesional y Certificado de Vigencia.',
            'sop_22_listas.pdf' => 'CECAR - Certificado de Verificación en Listas Restrictivas OFAC/ONU.'
        ];

        foreach ($seedFiles as $filename => $sampleText) {
            $dest = UPLOADS_PATH . '/' . $filename;
            if (!file_exists($dest)) {
                // Generate a minimal valid dummy PDF file with text
                $pdfContent = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R/Resources<<>>/Contents 4 0 R>>endobj\n4 0 obj<</Length 85>>stream\nBT /F1 12 Tf 50 700 Td (" . addcslashes($sampleText, "()\n\r") . ") Tj ET\nendstream\nendobj\nxref\n0 5\n0000000000 65535 f \n0000000009 00000 n \n0000000056 00000 n \n0000000111 00000 n \n0000000212 00000 n \ntrailer<</Size 5/Root 1 0 R>>\nstartxref\n348\n%%EOF";
                file_put_contents($dest, $pdfContent);
            }
        }
    }
}

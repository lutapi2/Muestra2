<?php
use App\Helpers\IconHelper;
use App\Helpers\AuthHelper;
?>
<div style="max-width: 1000px; margin: 2rem auto; padding: 2rem; background: #ffffff; border: 1px solid var(--border-color); border-radius: 14px;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem; border-bottom: 2px solid #f1f5f9; padding-bottom: 1.5rem;">
        <div>
            <a href="<?= AuthHelper::estaAutenticado() ? BASE_URL . '/index.php?c=Dashboard&a=index' : BASE_URL . '/' ?>" style="color: #10b981; text-decoration: none; font-size: 0.88rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem; margin-bottom: 0.5rem;">
                <?= IconHelper::render('arrow-left', 14) ?>
                <span><?= AuthHelper::estaAutenticado() ? 'Volver al Dashboard' : 'Volver al Inicio' ?></span>
            </a>
            <h1 style="color: #0b6b44; font-size: 1.75rem; font-weight: 800; margin: 0;">
                Manual y Tutorial Institucional del Sistema de Selección y Contratación CECAR
            </h1>
            <p style="color: #64748b; font-size: 0.95rem; margin-top: 0.25rem;">
                Especificación Funcional y Técnica Versión 3.0 &bull; Corporación Universitaria del Caribe
            </p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <a href="<?= BASE_URL ?>/index.php?c=MesaAyuda&a=index" class="btn btn-secondary btn-sm" style="font-weight: 600;">
                <?= IconHelper::render('help-circle', 14) ?>
                <span style="margin-left: 0.35rem;">Mesa de Ayuda</span>
            </a>
        </div>
    </div>

    <!-- Etapas del Ciclo de Selección -->
    <div style="margin-bottom: 2.5rem;">
        <h2 style="color: #1e293b; font-size: 1.25rem; font-weight: 800; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <?= IconHelper::render('briefcase', 18) ?>
            <span>Ciclo Integral de Selección y Vinculación</span>
        </h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.25rem; border-top: 4px solid #10b981;">
                <h3 style="color: #0b6b44; font-size: 1.05rem; font-weight: 700; margin-bottom: 0.5rem;">1. Convocatoria y Postulación</h3>
                <p style="color: #475569; font-size: 0.88rem; line-height: 1.5;">
                    Publicación oficial de cargos con requisitos mínimos, experiencia exigida y periodo académico. El candidato crea su cuenta, acepta el aviso de privacidad de datos (Ley 1581) y registra su Hoja de Vida dividida por ítems individuales con su respectivo soporte probatorio en PDF.
                </p>
            </div>

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.25rem; border-top: 4px solid #f59e0b;">
                <h3 style="color: #b45309; font-size: 1.05rem; font-weight: 700; margin-bottom: 0.5rem;">2. Revisión Lado a Lado y Subsanación</h3>
                <p style="color: #475569; font-size: 0.88rem; line-height: 1.5;">
                    Los revisores verifican cada dato frente al documento adjunto. Si un soporte no cumple, se emite una solicitud formal con plazo en días. En la subsanación (RN-011), el aspirante solo puede editar los ítems observados, preservando la inmutabilidad del resto del expediente.
                </p>
            </div>

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.25rem; border-top: 4px solid #8b5cf6;">
                <h3 style="color: #6d28d9; font-size: 1.05rem; font-weight: 700; margin-bottom: 0.5rem;">3. Pruebas y Entrevistas de Comité</h3>
                <p style="color: #475569; font-size: 0.88rem; line-height: 1.5;">
                    Se aplican pruebas psicotécnicas y de aula docente. Las entrevistas se agendan mediante validación de disponibilidad temporal sin traslapes ni conflictos de agenda (RN-017). Los descartes requieren causal institucional obligatoria (RN-024).
                </p>
            </div>

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.25rem; border-top: 4px solid #059669;">
                <h3 style="color: #047857; font-size: 1.05rem; font-weight: 700; margin-bottom: 0.5rem;">4. Formalización y Vinculación</h3>
                <p style="color: #475569; font-size: 0.88rem; line-height: 1.5;">
                    Control estricto de las 8 categorías documentales (incluyendo cuenta Davivienda y concepto ocupacional de aptitud laboral). Una vez formalizado el contrato, se genera el código de colaborador institucional y se sincronizan los datos con la Hoja de Vida de CECAR.
                </p>
            </div>
        </div>
    </div>

    <!-- Reglas de Negocio Clave -->
    <div style="margin-bottom: 2rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 1.5rem;">
        <h3 style="color: #166534; font-size: 1.1rem; font-weight: 800; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
            <?= IconHelper::render('shield', 18) ?>
            <span>Seguridad Institucional y Reglas de Negocio</span>
        </h3>
        <ul style="color: #1e293b; font-size: 0.88rem; line-height: 1.7; margin-left: 1.5rem;">
            <li><strong>RN-004 / RF-015:</strong> Consentimiento previo y expreso para el tratamiento de datos personales registrado con marca de tiempo.</li>
            <li><strong>RN-011 / RF-049:</strong> Edición restringida durante la subsanación; únicamente se pueden modificar los soportes observados.</li>
            <li><strong>RN-017 / RF-064:</strong> Validación automática de no solapamiento de horarios entre entrevistadores y postulantes.</li>
            <li><strong>RN-024 / RF-093:</strong> Ningún aspirante puede ser descartado sin seleccionar una causal codificada del catálogo oficial (DES-01 a DES-08).</li>
            <li><strong>RN-027 / RNF-028:</strong> El concepto médico ocupacional solo expone la aptitud laboral (Apto / Restricciones / No apto), protegiendo la confidencialidad médica.</li>
        </ul>
    </div>
</div>

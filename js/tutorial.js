/**
 * Tutorial Contextual Permanente (RF-083)
 * Plataforma de Contratación CECAR
 */

function toggleTutorialModal() {
    let modal = document.getElementById('cecarTutorialModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'cecarTutorialModal';
        modal.style.cssText = `
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.7); display: flex; align-items: center; justify-content: center;
            z-index: 9999; backdrop-filter: blur(4px);
        `;
        modal.innerHTML = `
            <div style="background: #ffffff; border-radius: 14px; max-width: 600px; width: 90%; padding: 2rem; box-shadow: 0 20px 40px rgba(0,0,0,0.3); border-top: 5px solid #10b981; position: relative;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="background: #ecfdf5; color: #059669; padding: 0.4rem; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                        </span>
                        <h3 style="color: #0b6b44; font-size: 1.25rem; font-weight: 800; margin: 0;">Tutorial y Guía del Sistema CECAR</h3>
                    </div>
                    <button onclick="toggleTutorialModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #94a3b8;">&times;</button>
                </div>
                <div style="color: #475569; font-size: 0.92rem; line-height: 1.6;">
                    <p style="margin-bottom: 1rem;">Bienvenido a la <strong>Plataforma Institucional de Contratación y Selección de CECAR</strong>. Este sistema gestiona el ciclo completo desde la postulación hasta la vinculación formal.</p>
                    
                    <h4 style="color: #1e293b; font-size: 0.95rem; margin-bottom: 0.5rem;">Pasos Principales:</h4>
                    <ol style="margin-left: 1.25rem; margin-bottom: 1.25rem;">
                        <li style="margin-bottom: 0.4rem;"><strong>Convocatorias:</strong> Consulte cargos abiertos y requisitos exigidos.</li>
                        <li style="margin-bottom: 0.4rem;"><strong>Hoja de Vida por Ítems:</strong> Cada estudio, experiencia o antecedente se registra con su soporte individualizado.</li>
                        <li style="margin-bottom: 0.4rem;"><strong>Revisión y Subsanación:</strong> Talento Humano verifica soportes y puede solicitar subsanación con plazo limitado.</li>
                        <li style="margin-bottom: 0.4rem;"><strong>Pruebas y Entrevistas:</strong> Registro de pruebas psicotécnicas, de aula docente y comités evaluadores.</li>
                        <li style="margin-bottom: 0.4rem;"><strong>Contratación de Seleccionados:</strong> Gestión de documentos por categorías (Contrato, Formación, Davivienda, etc.) y confirmación de vinculación con transferencia a Hoja de Vida institucional.</li>
                    </ol>
                </div>
                <div style="text-align: right;">
                    <button onclick="toggleTutorialModal()" class="btn btn-primary" style="padding: 0.5rem 1.25rem; font-weight: 700;">Entendido</button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    } else {
        modal.style.display = (modal.style.display === 'none' || modal.style.display === '') ? 'flex' : 'none';
    }
}

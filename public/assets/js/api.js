/**
 * Cliente API Centralizado - Sistema de Selección CECAR v3.0 (2026)
 * Conecta los componentes de interfaz de usuario con los endpoints del Front Controller.
 * Satisface: RNF-024 (manejo seguro de errores HTTP).
 */

const ApiClient = (() => {
  /**
   * Determina la URL base de la aplicación de forma dinámica,
   * soportando servidor integrado (php -S) o subdirectorios de Apache (XAMPP).
   */
  const getBaseUrl = () => {
    const metaBase = document.querySelector('meta[name="base-url"]');
    if (metaBase && metaBase.getAttribute('content') !== null) {
      return metaBase.getAttribute('content');
    }
    const path = window.location.pathname;
    const publicIndex = path.indexOf('/public');
    if (publicIndex !== -1) {
      return path.substring(0, publicIndex + 7);
    }
    return '';
  };

  /**
   * Realiza una petición HTTP genérica en formato JSON.
   */
  const request = async (endpoint, options = {}) => {
    const url = `${getBaseUrl()}${endpoint.startsWith('/') ? '' : '/'}${endpoint}`;
    
    const defaultHeaders = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    };

    const token = localStorage.getItem('cecar_session_token');
    if (token) {
      defaultHeaders['Authorization'] = `Bearer ${token}`;
    }

    const config = {
      ...options,
      headers: {
        ...defaultHeaders,
        ...(options.headers || {})
      }
    };

    try {
      const response = await fetch(url, config);
      const data = await response.json().catch(() => null);

      if (!response.ok) {
        return {
          success: false,
          status: response.status,
          message: data?.message || 'Error en la comunicación con el servidor.',
          error: data?.error || null,
          data: null
        };
      }

      return data || { success: true, status: response.status, data: null };
    } catch (err) {
      console.error('[CECAR API Error]', err);
      return {
        success: false,
        status: 0,
        message: 'No fue posible conectar con el servidor. Verifique su conexión de red.',
        error: { network: err.message },
        data: null
      };
    }
  };

  return {
    get: (endpoint) => request(endpoint, { method: 'GET' }),
    post: (endpoint, body) => request(endpoint, { method: 'POST', body: JSON.stringify(body) }),
    put: (endpoint, body) => request(endpoint, { method: 'PUT', body: JSON.stringify(body) }),
    delete: (endpoint) => request(endpoint, { method: 'DELETE' }),
    getBaseUrl
  };
})();

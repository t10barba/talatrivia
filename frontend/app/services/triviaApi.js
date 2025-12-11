import axios from 'axios';

const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8080';

const triviaApi = axios.create({
  baseURL: `${API_BASE_URL}/api/v1/juego`,
  headers: {
    'Content-Type': 'application/json',
  },
});

/**
 * Obtiene la siguiente pregunta sin responder de la trivia
 * @param {string} slug - El slug de la trivia
 * @param {string} codigo - El código único del usuario
 * @returns {Promise<Object>} Datos de la pregunta o indicador de finalizado
 */
export const getPregunta = async (slug, codigo) => {
  try {
    const response = await triviaApi.get(`/${slug}/${codigo}/pregunta`);
    return { success: true, data: response.data };
  } catch (error) {
    return {
      success: false,
      error: error.response?.data || { message: 'Error al obtener la pregunta' },
    };
  }
};

/**
 * Envía la respuesta seleccionada por el usuario
 * @param {string} slug - El slug de la trivia
 * @param {string} codigo - El código único del usuario
 * @param {number} respuestaId - ID de la respuesta seleccionada
 * @returns {Promise<Object>} Resultado de la respuesta (correcto/incorrecto, puntaje, finalizado)
 */
export const enviarRespuesta = async (slug, codigo, respuestaId) => {
  try {
    const response = await triviaApi.post(`/${slug}/${codigo}/respuesta`, {
      respuestaId,
    });
    return { success: true, data: response.data };
  } catch (error) {
    return {
      success: false,
      error: error.response?.data || { message: 'Error al enviar la respuesta' },
    };
  }
};

/**
 * Obtiene el puntaje final de la trivia completada
 * @param {string} slug - El slug de la trivia
 * @param {string} codigo - El código único del usuario
 * @returns {Promise<Object>} Puntaje total, tiempo transcurrido y datos de la trivia
 */
export const getPuntaje = async (slug, codigo) => {
  try {
    const response = await triviaApi.get(`/${slug}/${codigo}/puntaje`);
    return { success: true, data: response.data };
  } catch (error) {
    return {
      success: false,
      error: error.response?.data || { message: 'Error al obtener el puntaje' },
    };
  }
};

/**
 * Obtiene el ranking de mejores puntajes de una trivia
 * @param {string} slug - El slug de la trivia
 * @param {number} limit - Número máximo de resultados (opcional, default: 10)
 * @returns {Promise<Object>} Nombre de la trivia y array con el ranking
 */
export const getRanking = async (slug, limit = 10) => {
  try {
    const response = await triviaApi.get(`/${slug}/ranking`, {
      params: { limit }
    });
    return { success: true, data: response.data };
  } catch (error) {
    return {
      success: false,
      error: error.response?.data || { message: 'Error al obtener el ranking' },
    };
  }
};

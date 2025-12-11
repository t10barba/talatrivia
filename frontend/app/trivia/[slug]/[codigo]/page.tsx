'use client';

import { useState, useEffect } from 'react';
import { useParams } from 'next/navigation';
import { getPregunta, enviarRespuesta, getPuntaje } from '@/app/services/triviaApi';
import QuestionCard from '@/app/components/QuestionCard';
import FeedbackScreen from '@/app/components/FeedbackScreen';
import ScoreDisplay from '@/app/components/ScoreDisplay';

const FEEDBACK_DELAY = parseInt(process.env.NEXT_PUBLIC_FEEDBACK_DELAY || '2000', 10);

export default function JuegoTrivia() {
  const params = useParams();
  const { slug, codigo } = params;

  // Estados del juego
  const [gameState, setGameState] = useState('loading'); // loading, playing, feedback, finished, error
  const [preguntaActual, setPreguntaActual] = useState(null);
  const [respuestaSeleccionada, setRespuestaSeleccionada] = useState(null);
  const [feedbackData, setFeedbackData] = useState(null);
  const [puntajeFinal, setPuntajeFinal] = useState(null);
  const [error, setError] = useState(null);

  // Cargar pregunta inicial
  useEffect(() => {
    cargarPregunta();
  }, [slug, codigo]);

  const cargarPregunta = async () => {
    setGameState('loading');
    const result = await getPregunta(slug, codigo);

    if (!result.success) {
      setError(result.error);
      setGameState('error');
      return;
    }

    if (result.data.finalizado) {
      // La trivia ya está finalizada, cargar puntaje
      cargarPuntajeFinal();
    } else {
      setPreguntaActual(result.data);
      setGameState('playing');
      setRespuestaSeleccionada(null);
    }
  };

  const cargarPuntajeFinal = async () => {
    setGameState('loading');
    const result = await getPuntaje(slug, codigo);

    if (!result.success) {
      setError(result.error);
      setGameState('error');
      return;
    }

    setPuntajeFinal(result.data);
    setGameState('finished');
  };

  const handleRespuestaClick = (respuestaId) => {
    // Solo marca la respuesta seleccionada, no envía
    setRespuestaSeleccionada(respuestaId);
  };

  const handleEnviarRespuesta = async () => {
    if (respuestaSeleccionada === null) return;

    setGameState('loading');

    const result = await enviarRespuesta(slug, codigo, respuestaSeleccionada);

    if (!result.success) {
      setError(result.error);
      setGameState('error');
      return;
    }

    // Mostrar feedback
    setFeedbackData(result.data);
    setGameState('feedback');

    // Después del delay, continuar
    setTimeout(() => {
      if (result.data.finalizado) {
        cargarPuntajeFinal();
      } else {
        cargarPregunta();
      }
    }, FEEDBACK_DELAY);
  };

  // Renderizado condicional según el estado
  if (gameState === 'loading') {
    return (
      <div className="min-h-screen flex items-center justify-center bg-linear-to-br from-blue-500 to-purple-600">
        <div className="text-white text-2xl font-bold animate-pulse">
          Cargando...
        </div>
      </div>
    );
  }

  if (gameState === 'error') {
    return (
      <div className="min-h-screen flex items-center justify-center bg-linear-to-br from-red-500 to-pink-600">
        <div className="bg-white rounded-lg shadow-2xl p-8 max-w-md text-center">
          <i className="fas fa-exclamation-triangle text-red-500 text-6xl mb-4"></i>
          <h1 className="text-2xl font-bold text-gray-800 mb-4">Error</h1>
          <p className="text-gray-600 mb-2">{error?.message || 'Ha ocurrido un error'}</p>
          {error?.code && (
            <p className="text-sm text-gray-500">Código: {error.code}</p>
          )}
        </div>
      </div>
    );
  }

  if (gameState === 'feedback') {
    return (
      <FeedbackScreen
        correcto={feedbackData.correcto}
        puntajeObtenido={feedbackData.puntajeObtenido}
      />
    );
  }

  if (gameState === 'finished') {
    return (
      <ScoreDisplay
        puntajeTotal={puntajeFinal.puntajeTotal}
        nombreUsuario={puntajeFinal.nombreUsuario}
        nombreTrivia={puntajeFinal.nombreTrivia}
        tiempoTranscurrido={puntajeFinal.tiempoTranscurrido}
      />
    );
  }

  if (gameState === 'playing' && preguntaActual) {
    return (
      <QuestionCard
        pregunta={preguntaActual}
        respuestaSeleccionada={respuestaSeleccionada}
        onRespuestaClick={handleRespuestaClick}
        onEnviarRespuesta={handleEnviarRespuesta}
      />
    );
  }

  return null;
}

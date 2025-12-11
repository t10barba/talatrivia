'use client';

import { useEffect, useState } from 'react';
import Confetti from 'react-confetti';

export default function ScoreDisplay({ puntajeTotal, nombreUsuario, nombreTrivia, tiempoTranscurrido }) {
  const [showConfetti, setShowConfetti] = useState(true);

  useEffect(() => {
    // Detener confetti después de 10 segundos
    const timer = setTimeout(() => {
      setShowConfetti(false);
    }, 10000);

    return () => clearTimeout(timer);
  }, []);

  return (
    <div className="min-h-screen bg-linear-to-br from-yellow-400 via-orange-500 to-pink-500 flex items-center justify-center p-4 relative overflow-hidden">
      {/* Confetti */}
      {showConfetti && (
        <Confetti
          width={1920}
          height={1080}
          numberOfPieces={500}
          recycle={false}
        />
      )}

      {/* Card principal */}
      <div className="bg-white rounded-3xl shadow-2xl p-12 max-w-2xl w-full text-center relative z-10">
        {/* Trofeo animado */}
        <div className="mb-8 animate-bounce-slow">
          <i className="fas fa-trophy text-yellow-500 text-8xl drop-shadow-xl"></i>
        </div>

        {/* Título */}
        <h1 className="text-5xl font-bold text-gray-800 mb-4">
          ¡Felicitaciones!
        </h1>

        {/* Nombre del usuario */}
        <p className="text-2xl text-gray-600 mb-8">
          {nombreUsuario}
        </p>

        {/* Puntaje principal */}
        <div className="bg-linear-to-r from-purple-500 to-pink-500 rounded-2xl p-8 mb-8">
          <p className="text-white text-xl mb-2 font-semibold">Puntaje Final</p>
          <p className="text-white text-7xl font-bold drop-shadow-lg">
            {puntajeTotal}
          </p>
          <p className="text-white text-lg mt-2 opacity-90">puntos</p>
        </div>

        {/* Información adicional */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
          {/* Trivia */}
          <div className="bg-gray-50 rounded-xl p-6">
            <i className="fas fa-gamepad text-purple-500 text-3xl mb-3"></i>
            <p className="text-gray-500 text-sm mb-1">Trivia</p>
            <p className="text-gray-800 font-semibold text-lg">{nombreTrivia}</p>
          </div>

          {/* Tiempo */}
          <div className="bg-gray-50 rounded-xl p-6">
            <i className="fas fa-clock text-blue-500 text-3xl mb-3"></i>
            <p className="text-gray-500 text-sm mb-1">Tiempo</p>
            <p className="text-gray-800 font-semibold text-lg">{tiempoTranscurrido}</p>
          </div>
        </div>

        {/* Mensaje final */}
        <div className="border-t pt-6">
          <p className="text-gray-600 text-lg">
            ¡Has completado la trivia con éxito! 🎉
          </p>
        </div>

      </div>
    </div>
  );
}

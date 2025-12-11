'use client';

import { useState, useEffect } from 'react';
import { useParams } from 'next/navigation';
import { getRanking } from '@/app/services/triviaApi';

export default function RankingPage() {
  const params = useParams();
  const { slug } = params;

  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [rankingData, setRankingData] = useState(null);

  useEffect(() => {
    cargarRanking();
  }, [slug]);

  const cargarRanking = async () => {
    setLoading(true);
    const result = await getRanking(slug, 50); // Top 50

    if (!result.success) {
      setError(result.error);
      setLoading(false);
      return;
    }

    setRankingData(result.data);
    setLoading(false);
  };

  // Estado de carga
  if (loading) {
    return (
      <div className="min-h-screen bg-linear-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
        <div className="text-white text-2xl font-bold animate-pulse">
          Cargando ranking...
        </div>
      </div>
    );
  }

  // Estado de error
  if (error) {
    return (
      <div className="min-h-screen bg-linear-to-br from-red-500 to-pink-600 flex items-center justify-center p-4">
        <div className="bg-white rounded-lg shadow-2xl p-8 max-w-md text-center">
          <i className="fas fa-exclamation-triangle text-red-500 text-6xl mb-4"></i>
          <h1 className="text-2xl font-bold text-gray-800 mb-4">Error</h1>
          <p className="text-gray-600 mb-2">{error?.message || 'Error al cargar el ranking'}</p>
          {error?.code && (
            <p className="text-sm text-gray-500">Código: {error.code}</p>
          )}
        </div>
      </div>
    );
  }

  // Tabla de ranking
  return (
    <div className="min-h-screen bg-linear-to-br from-purple-500 to-indigo-600 py-12 px-4">
      <div className="max-w-6xl mx-auto">
        {/* Header */}
        <div className="text-center mb-12">
          <div className="mb-4">
            <i className="fas fa-trophy text-yellow-400 text-6xl drop-shadow-lg"></i>
          </div>
          <h1 className="text-5xl font-bold text-white mb-3 drop-shadow-lg">
            Ranking
          </h1>
          <p className="text-2xl text-white opacity-90">
            {rankingData?.nombreTrivia}
          </p>
        </div>

        {/* Tabla de ranking */}
        {rankingData?.ranking && rankingData.ranking.length > 0 ? (
          <div className="bg-white rounded-2xl shadow-2xl overflow-hidden">
            {/* Header de tabla - Desktop */}
            <div className="hidden md:grid md:grid-cols-5 bg-linear-to-r from-purple-600 to-indigo-600 text-white font-bold py-4 px-6">
              <div className="text-center">Posición</div>
              <div className="col-span-2">Participante</div>
              <div className="text-center">Puntaje</div>
              <div className="text-center">Tiempo</div>
            </div>

            {/* Filas de ranking */}
            <div className="divide-y divide-gray-200">
              {rankingData.ranking.map((item, index) => {
                const isPodium = item.posicion <= 3;
                const medalColors = {
                  1: 'text-yellow-500',
                  2: 'text-gray-400',
                  3: 'text-orange-600'
                };

                return (
                  <div
                    key={index}
                    className={`
                      grid grid-cols-1 md:grid-cols-5 gap-4 py-4 px-6 hover:bg-gray-50 transition-colors
                      ${isPodium ? 'bg-gradient-to-r from-yellow-50 to-orange-50' : ''}
                    `}
                  >
                    {/* Posición */}
                    <div className="flex items-center justify-center md:justify-center">
                      {isPodium ? (
                        <div className="flex items-center gap-2">
                          <i className={`fas fa-medal text-3xl ${medalColors[item.posicion]}`}></i>
                          <span className="text-xl font-bold text-gray-700">#{item.posicion}</span>
                        </div>
                      ) : (
                        <span className="text-xl font-semibold text-gray-600">#{item.posicion}</span>
                      )}
                    </div>

                    {/* Nombre */}
                    <div className="col-span-1 md:col-span-2 flex items-center justify-center md:justify-start">
                      <div className="flex items-center gap-3">
                        <div className="w-10 h-10 rounded-full bg-linear-to-br from-purple-500 to-indigo-600 flex items-center justify-center text-white font-bold">
                          {item.nombreUsuario.charAt(0).toUpperCase()}
                        </div>
                        <span className="text-lg font-semibold text-gray-800">
                          {item.nombreUsuario}
                        </span>
                      </div>
                    </div>

                    {/* Puntaje */}
                    <div className="flex items-center justify-center">
                      <div className="text-center">
                        <div className="text-2xl font-bold text-purple-600">
                          {item.puntajeTotal}
                        </div>
                        <div className="text-xs text-gray-500">puntos</div>
                      </div>
                    </div>

                    {/* Tiempo */}
                    <div className="flex items-center justify-center">
                      <div className="text-center">
                        <div className="text-lg font-semibold text-gray-700">
                          <i className="fas fa-clock text-indigo-500 mr-1"></i>
                          {item.tiempoTranscurrido}
                        </div>
                      </div>
                    </div>
                  </div>
                );
              })}
            </div>
          </div>
        ) : (
          <div className="bg-white rounded-2xl shadow-2xl p-12 text-center">
            <i className="fas fa-users-slash text-gray-300 text-6xl mb-4"></i>
            <p className="text-xl text-gray-600">
              Aún no hay participantes en el ranking
            </p>
          </div>
        )}

        {/* Footer stats */}
        {rankingData?.ranking && rankingData.ranking.length > 0 && (
          <div className="mt-8 text-center text-white">
            <p className="text-lg opacity-90">
              <i className="fas fa-users mr-2"></i>
              Total de participantes: {rankingData.ranking.length}
            </p>
          </div>
        )}
      </div>
    </div>
  );
}

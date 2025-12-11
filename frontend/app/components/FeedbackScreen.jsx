export default function FeedbackScreen({ correcto, puntajeObtenido }) {
  return (
    <div className={`
      min-h-screen flex items-center justify-center
      ${correcto 
        ? 'bg-linear-to-br from-green-400 to-emerald-600' 
        : 'bg-linear-to-br from-red-400 to-pink-600'}
    `}>
      <div className={`text-center ${correcto ? 'animate-bounce-slow' : 'animate-shake-horizontal'}`}>
        {/* Icono grande */}
        <div className="mb-8">
          {correcto ? (
            <i className="fas fa-check-circle text-white text-9xl drop-shadow-2xl"></i>
          ) : (
            <i className="fas fa-times-circle text-white text-9xl drop-shadow-2xl"></i>
          )}
        </div>

        {/* Texto principal */}
        <h1 className="text-white text-6xl font-bold mb-4 drop-shadow-lg">
          {correcto ? '¡Correcto!' : '¡Incorrecto!'}
        </h1>

        {/* Puntaje obtenido */}
        {correcto && (
          <p className="text-white text-3xl font-semibold drop-shadow-md">
            +{puntajeObtenido} {puntajeObtenido === 1 ? 'punto' : 'puntos'}
          </p>
        )}

        {/* Mensaje motivacional */}
        <p className="text-white text-xl mt-6 opacity-90">
          {correcto 
            ? '¡Excelente trabajo!' 
            : 'No te preocupes, sigue adelante'}
        </p>
      </div>
    </div>
  );
}

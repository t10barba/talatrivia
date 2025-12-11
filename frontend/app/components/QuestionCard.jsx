import AnswerButton from './AnswerButton';

export default function QuestionCard({ pregunta, respuestaSeleccionada, onRespuestaClick, onEnviarRespuesta }) {
  return (
    <div className="min-h-screen bg-linear-to-br from-blue-500 to-purple-600 flex items-center justify-center p-4">
      <div className="bg-white rounded-2xl shadow-2xl p-8 max-w-3xl w-full">
        {/* Header con progreso */}
        <div className="mb-8">
          <div className="flex justify-between items-center mb-4">
            <span className="text-sm font-semibold text-gray-500">
              Pregunta {pregunta.numeroPregunta} de {pregunta.totalPreguntas}
            </span>
          </div>
          
          {/* Barra de progreso */}
          <div className="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
            <div
              className="bg-gradient-to-r from-blue-500 to-purple-600 h-full transition-all duration-500 ease-out"
              style={{ width: `${(pregunta.numeroPregunta / pregunta.totalPreguntas) * 100}%` }}
            ></div>
          </div>
        </div>

        {/* Texto de la pregunta */}
        <div className="mb-8">
          <h2 className="text-2xl md:text-3xl font-bold text-gray-800 leading-relaxed text-center">
            {pregunta.texto}
          </h2>
        </div>

        {/* Opciones de respuesta */}
        <div className="space-y-4">
          {pregunta.respuestas.map((respuesta, index) => (
            <AnswerButton
              key={respuesta.id}
              respuesta={respuesta}
              index={index}
              isSelected={respuestaSeleccionada === respuesta.id}
              onClick={() => onRespuestaClick(respuesta.id)}
            />
          ))}
        </div>

        {/* Botón enviar respuesta */}
        <div className="mt-8">
          <button
            onClick={onEnviarRespuesta}
            disabled={respuestaSeleccionada === null}
            className={`
              w-full py-4 rounded-xl font-bold text-lg transition-all duration-200
              ${
                respuestaSeleccionada === null
                  ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                  : 'bg-linear-to-r from-green-500 to-emerald-600 text-white hover:shadow-xl hover:scale-105 cursor-pointer'
              }
            `}
          >
            <i className="fas fa-paper-plane mr-2"></i>
            Enviar Respuesta
          </button>
        </div>
      </div>
    </div>
  );
}

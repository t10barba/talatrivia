export default function AnswerButton({ respuesta, index, isSelected, onClick }) {
  // Generar letra automáticamente según el índice (A, B, C, D, E, ...)
  const letter = String.fromCharCode(65 + index); // 65 = 'A' en ASCII
  
  const getButtonClasses = () => {
    let baseClasses = 'w-full p-5 rounded-xl text-left transition-all duration-200 flex items-center gap-4 font-medium text-lg';
    
    if (isSelected) {
      return `${baseClasses} bg-blue-500 text-white shadow-lg transform scale-105 cursor-pointer`;
    }
    
    return `${baseClasses} bg-white border-2 border-gray-200 text-gray-800 hover:border-purple-500 hover:shadow-lg hover:transform hover:scale-102 cursor-pointer`;
  };

  return (
    <button
      onClick={onClick}
      className={getButtonClasses()}
    >
      {/* Letra de la opción */}
      <span className={`
        w-12 h-12 rounded-full flex items-center justify-center font-bold text-xl flex-shrink-0
        ${isSelected ? 'bg-white text-blue-500' : 'bg-linear-to-br from-blue-500 to-purple-600 text-white'}
      `}>
        {letter}
      </span>
      
      {/* Texto de la respuesta */}
      <span className="flex-1">
        {respuesta.texto}
      </span>
      
      {/* Icono cuando está seleccionada */}
      {isSelected && (
        <i className="fas fa-check-circle text-2xl"></i>
      )}
    </button>
  );
}

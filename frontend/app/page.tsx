export default function Home() {
  return (
    <div className="container mx-auto px-4 py-12 md:py-20">
      <div className="max-w-4xl mx-auto">
        {/* Welcome Section */}
        <div className="text-center mb-12">
          <div className="mb-8 flex justify-center">
            <div className="bg-gradient-to-br from-[#27273f] to-[#3d3d5f] rounded-full p-8 shadow-lg">
              <i className="fas fa-star text-6xl text-white"></i>
            </div>
          </div>
          
          <h1 className="text-4xl md:text-5xl lg:text-6xl font-bold text-[#27273f] mb-6">
            ¡Bienvenidos a TalaTrivia!
          </h1>
          
          <p className="text-xl md:text-2xl text-gray-700 leading-relaxed max-w-3xl mx-auto">
            Reta tus conocimientos con nuestra trivia de Recursos Humanos
          </p>

        </div>

        {/* Features Section */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8 mt-16">

          <div className="text-center p-6 rounded-lg hover:shadow-lg transition-shadow bg-gray-50">
            <div className="mb-4 flex justify-center">
              <div className="bg-[#27273f] rounded-full p-4">
                <i className="fas fa-trophy text-3xl text-white"></i>
              </div>
            </div>
            <h3 className="text-lg font-semibold text-[#27273f] mb-2">Compite</h3>
            <p className="text-gray-600">Mide tus resultados y mejora tu comprensión del área</p>
          </div>

          <div className="text-center p-6 rounded-lg hover:shadow-lg transition-shadow bg-gray-50">
            <div className="mb-4 flex justify-center">
              <div className="bg-[#27273f] rounded-full p-4">
                <i className="fas fa-graduation-cap text-3xl text-white"></i>
              </div>
            </div>
            <h3 className="text-lg font-semibold text-[#27273f] mb-2">Aprende</h3>
            <p className="text-gray-600">Descubre y refuerza las buenas prácticas laborales</p>
          </div>

          <div className="text-center p-6 rounded-lg hover:shadow-lg transition-shadow bg-gray-50">
            <div className="mb-4 flex justify-center">
              <div className="bg-[#27273f] rounded-full p-4">
                <i className="fas fa-crown text-3xl text-white"></i>
              </div>
            </div>
            <h3 className="text-lg font-semibold text-[#27273f] mb-2">Ranking</h3>
            <p className="text-gray-600">Conoce a los jugadores y sus puntajes</p>
          </div>
    
        </div>

      </div>
    </div>
  );
}

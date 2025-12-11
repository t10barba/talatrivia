export default function Footer() {
  const currentYear = new Date().getFullYear();
  
  return (
    <footer className="bg-[#27273f] w-full mt-auto">
      <div className="container mx-auto px-4 py-6">
        <div className="flex flex-col md:flex-row items-center justify-between text-white text-sm">
          <div className="mb-4 md:mb-0">
            <p>&copy; {currentYear} Talana. Todos los derechos reservados.</p>
          </div>
          <div className="flex gap-6">
            <a href="#" className="hover:text-gray-300 transition-colors">
              <i className="fas fa-envelope"></i>
              <span className="ml-2">Contacto</span>
            </a>
            <a href="#" className="hover:text-gray-300 transition-colors">
              <i className="fas fa-info-circle"></i>
              <span className="ml-2">Acerca de</span>
            </a>
          </div>
        </div>
      </div>
    </footer>
  );
}

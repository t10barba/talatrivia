'use client';

import { useEffect, useState } from 'react';

export default function Footer() {
  const currentYear = new Date().getFullYear();
  const [version, setVersion] = useState<string>('');
  
  useEffect(() => {
    // Intentar obtener la versión desde el endpoint
    fetch('http://localhost:8080/api/version')
      .then(res => res.json())
      .then(data => setVersion(data.version))
      .catch(() => {
        // Fallback a variable de entorno si el endpoint falla
        setVersion(process.env.APP_VERSION || process.env.NEXT_PUBLIC_APP_VERSION || '1.0.0');
      });
  }, []);
  
  return (
    <footer className="bg-[#27273f] w-full mt-auto">
      <div className="container mx-auto px-4 py-6">
        <div className="flex flex-col md:flex-row items-center justify-between text-white text-sm">
          <div className="mb-4 md:mb-0 flex items-center gap-4">
            <p>&copy; {currentYear} Talana. Todos los derechos reservados.</p>
            {version && (
              <span className="text-xs bg-white/10 px-2 py-1 rounded">
                v{version}
              </span>
            )}
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

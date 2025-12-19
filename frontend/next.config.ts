import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  webpack: (config, { dev }) => {
    if (dev) {
      // Solo habilitar polling si se está ejecutando en Docker
      // En desarrollo local, Next.js usará el sistema de archivos nativo (más rápido)
      const usePolling = process.env.WATCHPACK_POLLING === "true";
      
      if (usePolling) {
        config.watchOptions = {
          poll: 1000,
          aggregateTimeout: 300,
        };
      }
    }
    return config;
  },
};

export default nextConfig;

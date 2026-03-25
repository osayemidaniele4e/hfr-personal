/** @type {import('next').NextConfig} */
const nextConfig = {
  images: {
    remotePatterns: [
      {
        protocol: "https",
        hostname: "daisyui.com",
        port: "",
      },
      {
        protocol: "https",
        hostname: "img.daisyui.com",
        port: "",
      },
      {
        protocol: "https",
        hostname: "storage.googleapis.com",
        port: "",
      },
      // {
      //   protocol: "https",
      //   hostname: "C:\fakepath\"",
      //   port: "",
      // },

      {
        protocol: "http",
        hostname: "127.0.0.1",
        port: "8001", // Allow images from your local backend
      },

      {
        protocol: "https",
        hostname: "your-production-domain.com", // Add your production domain
      },
    ],
  },
};

export default nextConfig;

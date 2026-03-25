/** @type {import('next').NextConfig} */
const nextConfig = {
  reactStrictMode: false, // ✅ Disable React Strict Mode (for development only)
  images: {
    remotePatterns: [
      {
        protocol: "https",
        hostname: "daisyui.com",
        pathname: "/**",
      },
      {
        protocol: "https",
        hostname: "img.daisyui.com",
        pathname: "/**",
      },
      {
        protocol: "https",
        hostname: "storage.googleapis.com",
        pathname: "/**",
      },
      {
        protocol: "http",
        hostname: "127.0.0.1",
        port: "8001",
        pathname: "/**", // Allow all images from your local API
      },
      {
        protocol: "https",
        hostname: process.env.NEXT_PUBLIC_SITE_URL || "your-production-domain.com",
        pathname: "/**",
      },
      {
        protocol: 'https',
        hostname: 'images.unsplash.com',
      }
    ],
  },
};

export default nextConfig;

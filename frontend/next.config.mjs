/** @type {import('next').NextConfig} */
function siteImageRemotePattern() {
  const raw = process.env.NEXT_PUBLIC_SITE_URL;
  if (!raw) {
    return { protocol: "https", hostname: "localhost", pathname: "/**" };
  }
  try {
    const u = new URL(raw.includes("://") ? raw : `https://${raw}`);
    const pattern = {
      protocol: u.protocol === "https:" ? "https" : "http",
      hostname: u.hostname,
      pathname: "/**",
    };
    if (u.port) {
      pattern.port = u.port;
    }
    return pattern;
  } catch {
    return { protocol: "https", hostname: "localhost", pathname: "/**" };
  }
}

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
      siteImageRemotePattern(),
      {
        protocol: 'https',
        hostname: 'images.unsplash.com',
      }
    ],
  },
};

export default nextConfig;

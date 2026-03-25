import type { Metadata } from "next";


/**
 * Helper to generate consistent SEO metadata for each page.
 *
 * @param title - The title of the page
 * @param description - A short description (max 160 chars)
 * @param keywords - Comma-separated keywords
 * @param path - The relative path of the page (e.g. '/contact')
 * @param image - Optional social preview image URL
 */
export function createPageMetadata(
  title: string,
  description: string,
  keywords: string,
  path: string = "/",
  image: string = `${process.env.NEXT_PUBLIC_SITE_URL}/nhfr-logo.svg`
): Metadata {
  const baseUrl = process.env.NEXT_PUBLIC_SITE_URL
  const fullUrl = `${baseUrl}${path}`;

  return {
    title: `NHFR | ${title}`,
    description,
    openGraph: {
      title: `NHFR | ${title}`,
      description,
      url: fullUrl,
      siteName: "National Health Facility Registry (NHFR)",
      images: [
        {
          url: image,
          width: 1200,
          height: 630,
          alt: `${title} - NHFR`,
        },
      ],
      locale: "en_NG",
      type: "website",
    },
    twitter: {
      card: "summary_large_image",
      title: `NHFR | ${title}`,
      description,
      images: [image],
    },
    other: {
      keywords,
      author: "National Health Facility Registry (NHFR)",
      robots: "index, follow",
      "theme-color": "#0A8754",
    },
  };
}

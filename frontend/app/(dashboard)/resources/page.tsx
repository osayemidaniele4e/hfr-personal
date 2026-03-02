import ResourceX from "@/components/sections/ResourceX";
import React from "react";
import { createPageMetadata } from "@/app/lib/metadata";


export const metadata = createPageMetadata(
  "Resources",
 "Access downloadable reports, guidelines, publications, and FAQs from the National Health Facility Registry (NHFR) to enhance transparency and support data-driven health planning in Nigeria.",
  "NHFR resources, health facility reports, healthcare data Nigeria, NHFR publications, open data, health guidelines, NHFR FAQs, healthcare transparency, Federal Ministry of Health",
  "/resources"
);

const Resources = () => {
  return (
    <div>
      <ResourceX />
    </div>
  );
};

export default Resources;

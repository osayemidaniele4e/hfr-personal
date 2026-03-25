import AboutHero from "@/components/sections/about/AboutHero";
import Origin from "@/components/sections/about/Origin";
import Process from "@/components/sections/about/Process";
import Speech from "@/components/sections/about/Speech";
import React from "react";
import { createPageMetadata } from "@/app/lib/metadata";



export const metadata = createPageMetadata(
  "About",
  "Learn more about the National Health Facility Registry and our mission to strengthen health data visibility.",
  "NHFR, about NHFR, Nigeria health facility registry, healthcare data, health infrastructure, Ministry of Health, facility information",
  "/about"
);

const About = () => {
  return (
    <div className="pt-[4.5rem] lg:pt-[6rem]">
      <AboutHero />
      <Origin />
      <Process />
      <Speech />
    </div>
  );
};

export default About;

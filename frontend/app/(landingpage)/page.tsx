//"use client";

import type { Metadata } from "next";

import {
  DataVisualization,
  InteractiveSearch,
  PublicResources,
} from "@/components/sections/Experience";
import HeroSlideShow from "@/components/sections/HeroSlideShow";
import Intro from "@/components/sections/Intro";
import Overlay from "@/components/sections/Overlay";
import { createPageMetadata } from "@/app/lib/metadata";

export const metadata = createPageMetadata(
  "Home",
  "Welcome to the National Health Facility Registry (NHFR) — Nigeria's official platform for verified health facility information. Explore accurate data across all states and LGAs to support evidence-based health planning and decision-making.",
  "NHFR, Nigeria health facilities, health data, healthcare registry, hospital database, health infrastructure, Federal Ministry of Health, NHFR Nigeria",
  "/"
);

const Home = () => {
  return (
    <div className=" flex flex-col gap-[5rem] md:gap-[5rem] ">
      <div style={{ position: "relative", zIndex: 1 }}>
        <HeroSlideShow />
      </div>
      <Overlay />
      <Intro />
      <InteractiveSearch />
      <DataVisualization />
      <PublicResources />
    </div>
  );
};
export default Home;

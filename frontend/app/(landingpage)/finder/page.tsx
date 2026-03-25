import Finder from "@/components/sections/Finder";
import React from "react";
import { createPageMetadata } from "@/app/lib/metadata";


export const metadata = createPageMetadata(
  "Facility Finder - deprecated",
  "Search and explore verified health facilities across Nigeria using the NHFR Facility Finder. Access information by state, LGA, ownership, or service type.",
  "NHFR, facility finder, health facilities Nigeria, hospital locator, clinic search, healthcare services, NHFR map",
  "/finder"
);

const FinderPage = () => {
  return (
    <div className="">
      <Finder />
    </div>
  );
};


export default FinderPage;

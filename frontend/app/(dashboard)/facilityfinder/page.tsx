import Facility from "@/components/sections/Facility";
import Finder from "@/components/sections/Finder";
import React from "react";

import { createPageMetadata } from "@/app/lib/metadata";


export const metadata = createPageMetadata(
  "Facility Finder",
  "Search and explore verified health facilities across Nigeria using the NHFR Facility Finder. Access information by state, LGA, ownership, or service type.",
  "NHFR, facility finder, health facilities Nigeria, hospital locator, clinic search, healthcare services, NHFR map",
  "/facilityfinder"
);

const FacilityFinder = () => {
  return (
    <div>
      <Facility />
    </div>
  );
};

export default FacilityFinder;

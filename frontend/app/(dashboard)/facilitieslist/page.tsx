import ListOfFacilities from "@/components/sections/ListOfFacilities";
import React from "react";
import { createPageMetadata } from "@/app/lib/metadata";


export const metadata = createPageMetadata(
  "Facility List",
  "Search and explore verified health facilities across Nigeria using the NHFR Facility Finder. Access information by state, LGA, ownership, or service type.",
  "NHFR, facility list, health facilities Nigeria, pharmaceuticals, laboratories, hospital locator, clinic search, healthcare services in Nigeria, NHFR map",
  "/facilitieslist"
);


const FacilitiesList = () => {
  return (
    <div className="">
      <ListOfFacilities />
    </div>
  );
};

export default FacilitiesList;

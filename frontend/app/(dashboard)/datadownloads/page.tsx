import DownloadedData from "@/components/sections/DownloadedData";
import React from "react";
import { createPageMetadata } from "@/app/lib/metadata";


export const metadata = createPageMetadata(
  "Data Download Request",
  "Request and download verified lists of health facilities across Nigeria. Access facility data disaggregated by state, LGA, and ward from the National Health Facility Registry (NHFR).",
  "NHFR, data download, facility data, health facilities Nigeria, hospitals, clinics, laboratories, healthcare database, open data, NHFR Nigeria",
  "/datadownloads"
);

const DataDownloads = () => {
  return (
    <div>
      <DownloadedData />
    </div>
  );
};

export default DataDownloads;

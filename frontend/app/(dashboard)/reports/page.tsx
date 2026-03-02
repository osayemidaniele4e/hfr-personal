import ReportX from "@/components/sections/ReportX";
import React from "react";
import { createPageMetadata } from "@/app/lib/metadata";


export const metadata = createPageMetadata(
  "Reports",
 "Explore detailed health facility reports from the National Health Facility Registry (NHFR), including data summaries and insights by state, LGA, and ward across Nigeria.",
  "NHFR reports, health facility data, healthcare statistics Nigeria, NHFR analytics, open health data, NHFR Nigeria, healthcare transparency, Federal Ministry of Health, NHFR dashboards",
  "/reports",
);


const Reports = () => {
  return (
    <div className="">
      <ReportX />
    </div>
  );
};

export default Reports;

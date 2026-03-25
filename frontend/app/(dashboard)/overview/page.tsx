import { createPageMetadata } from "@/app/lib/metadata";
import OverviewClient from "@/components/sections/OverviewClient";

export const metadata = createPageMetadata(
  "Overview",
  "Overview of Nigeria Health Facility Registry",
  "NHFR reports, health facility data, healthcare statistics Nigeria, NHFR analytics, open health data, NHFR Nigeria, healthcare transparency, Federal Ministry of Health, NHFR dashboards",
  "/overview"
);



export default function OverviewPage() {
  return <OverviewClient />;
  
}

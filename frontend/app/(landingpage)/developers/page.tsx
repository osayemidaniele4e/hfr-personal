import { createPageMetadata } from "@/app/lib/metadata";
import DeveloperDocs from "@/components/sections/developers/DeveloperDocs";

export const metadata = createPageMetadata(
  "API Documentation",
  "Access the HFR External API documentation. Integrate Nigeria's health facility data into your applications with our RESTful API.",
  "HFR API, health facility API, Nigeria health data API, developer documentation, REST API, facility registry API",
  "/developers"
);

const DevelopersPage = () => {
  return (
    <div className="pt-[4.5rem] lg:pt-[6rem]">
      <DeveloperDocs />
    </div>
  );
};

export default DevelopersPage;

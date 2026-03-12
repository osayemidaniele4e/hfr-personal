import Image from "next/image";
import SectionContainer from "../ui/SectionContainer";
import { Text } from "../ui/Typography";
import Link from "next/link";

export const InteractiveSearch = () => {
  return (
    <div className="flex flex-col  ">
      <div className="flex flex-col lg:flex-row md:justify-center items-center gap-[3rem]">
        <div className="flex flex-col gap-[1rem] p-4">
          <Text className="font-[600] md:text-center lg:text-start">
            Interactive{" "}
            <span className="text-[#5CB85C]">
              <Link href="/facilitieslist" passHref>
                Search & Filter
              </Link>
            </span>
          </Text>

          <Text className="font-[400] w-full md:text-center lg:text-start lg:w-[440px]">
            Easily locate health facilities by name, location, type, or services
            using dynamic filters for precise results.
          </Text>
        </div>
        <Image
          src={"/interactive-filter.svg"}
          width={472}
          height={360}
          alt="img"
        />
      </div>
    </div>
  );
};

export const DataVisualization = () => {
  return (
    <div>
      <div className="flex flex-col-reverse lg:flex-row md:justify-center items-center gap-[3rem]">
        <Image src={"/data-visual.svg"} width={472} height={360} alt="img" />
        <div className="flex flex-col gap-[1rem]">
          <Text className="font-[600] md:text-center lg:text-start p-4 md:p-0">
            Data <span className="text-[#5CB85C]"> <Link href="/overview" passHref>
                Visualizations
              </Link></span>
          </Text>
          <Text className="font-[400]  w-full  lg:w-[440px] md:text-center lg:text-start p-4 md:p-0">
            Charts, graphs, and maps to present key health facility data in an
            easily interpretable format.
          </Text>
        </div>
      </div>
    </div>
  );
};

export const PublicResources = () => {
  return (
    <div>
      <div className="flex flex-col lg:flex-row md:justify-center items-center gap-[3rem]">
        <div className="flex flex-col gap-[1rem]">
          <Text className="font-[600] md:text-center lg:text-start p-4 md:p-0">
            Public{" "}
            <span className="text-[#5CB85C]">
              <Link href="/resources" passHref>
                Resources
              </Link>
            </span>
          </Text>

          <Text className="font-[400]  w-full lg:w-[440px] md:text-center lg:text-start p-4 md:p-0">
            Access to downloadable reports, guidelines, and FAQs to improve
            transparency.
          </Text>
        </div>
        <Image
          src={"/public-resource.svg"}
          width={472}
          height={360}
          alt="img"
        />
      </div>
    </div>
  );
};

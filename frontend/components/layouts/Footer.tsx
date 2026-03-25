"use client";

import {
  FaArrowRightLong,
  FaCircleArrowUp,
  FaLocationDot,
} from "react-icons/fa6";
import { CiGlobe, CiMail } from "react-icons/ci";

import { Text } from "../ui/Typography";
import Image from "next/image";
import { useRouter } from "next/navigation";

const Socials = [
  { icon: "/nigeria-logo.svg", href: "" },
  { icon: "/usaid.svg", href: "" },
  { icon: "/measure.svg", href: "" },
  { icon: "/global-fund.svg", href: "" },
  { icon: "/E4E.svg", href: "" },
];

const Footer = () => {
  const { push } = useRouter();
  return (
    <div className="relative text-[#fff] bg-[#2D3E50] mt-[3rem] py-8">
      {/* Floating button */}
      <button
        className="absolute top-[-2rem] right-[2rem] bg-[#5CB85C] p-4 rounded-md shadow-lg hover:bg-[#4AAE4A] transition-all"
        onClick={() => push("/contact-us")}
      >
        Send us a feedback
        <FaArrowRightLong size={24} color="#fff" className="inline pl-1" />
      </button>

      <div className="flex flex-col lg:flex-row lg:justify-between lg:items-start lg:w-[1200px] mx-[1rem] lg:mx-auto mt-[2rem] gap-[2rem] lg:gap-[0]">
        <div>
          <Text className="font-[600]">Partners</Text>
          <div className="flex mt-[1rem] lg:mt-0 gap-2 flex-row">
            {Socials.map((i, index) => (
              <div
                className="flex justify-center items-center rounded-full"
                key={index}
              >
                <Image src={i.icon} width={40} height={40} alt="img" />
              </div>
            ))}
          </div>
        </div>

        <div className="flex flex-col gap-[1rem]">
          <Text className="font-[600]">Get in touch</Text>
          <div className="font-[400] flex gap-[.5rem]">
            <CiMail color="#fff" fontSize={24} />
            <Text>
              <a
                href="mailto:hfr@health.gov.ng"
                className="text-white hover:underline"
              >
                hfr@health.gov.ng
              </a>
            </Text>
          </div>
          <div className="font-[400] flex gap-[.5rem]">
            <CiGlobe color="#fff" fontSize={24} />
            <Text>
              <a
                href="https://hfr.health.gov.ng"
                target="_blank"
                rel="noopener noreferrer"
                className="text-white hover:underline"
              >
                https://hfr.health.gov.ng
              </a>
            </Text>
          </div>
          <div className="font-[400] flex gap-[.5rem]">
            <FaLocationDot color="#fff" fontSize={24} />
            <Text className="lg:w-[400px]">
              <a
                href="https://www.google.com/maps/search/?api=1&query=New+Federal+Secretariat+Complex,+Ahmadu+Bello+Way,+Central+Business+District"
                target="_blank"
                rel="noopener noreferrer"
                className="lg:w-[400px] cursor-pointer"
              >
                New Federal Secretariat Complex, Ahmadu Bello Way, Central
                Business District.
              </a>
            </Text>
          </div>
        </div>
        <div className="flex flex-col gap-[1rem]">
          <Text className="font-[600]">Useful links</Text>
          <Text className="font-[400]">
            <a
              href="http://health.gov.ng/"
              target="_blank"
              rel="noopener noreferrer"
              className="text-white hover:underline"
            >
              Federal Ministry of Health
            </a>
          </Text>
          <Text className="font-[400]">
            <a
              href="http://nphcda.gov.ng/"
              target="_blank"
              rel="noopener noreferrer"
              className="text-white hover:underline"
            >
              NPHCDA
            </a>
          </Text>
          <Text className="font-[400]">
            <a
              href="https://dhis2nigeria.org.ng/"
              target="_blank"
              rel="noopener noreferrer"
              className="text-white hover:underline"
            >
              Nigeria DHIS2
            </a>
          </Text>
        </div>
      </div>
    </div>
  );
};

export default Footer;

export const RightReserved = () => {
  return (
    <div className=" bg-[#5CB85C] p-4">
      <div className=" lg:px-[10rem] py-[1rem]  flex flex-col  lg:flex-row justify-between items-center">
        {" "}
        <p className="text-center mt-[1rem] text-xs  text-[#fff]">
          Copyright ©{new Date().getFullYear()} Federal Ministry of Health. All
          Rights Reserved
        </p>{" "}
        <div className="flex mt-[1rem] lg:mt-0 gap-4 md:gap-8 flex-row text-[#fff]">
          Version 2.0
        </div>
      </div>
    </div>
  );
};

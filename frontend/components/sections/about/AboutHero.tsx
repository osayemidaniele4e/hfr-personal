import { Text } from "@/components/ui/Typography";
import Image from "next/image";
import React from "react";

const AboutHero = () => {
  return (
    <div className="">
      <Text className="bg-[#E8F0E2] p-8 text-center font-semibold">
        About Nigeria Health Facility Registry
      </Text>
      <Image src={"/hero-img.svg"} width={1920} height={400} alt="hero" />
    </div>
  );
};

export default AboutHero;

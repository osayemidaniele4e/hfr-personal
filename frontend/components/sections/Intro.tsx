import React from "react";
import { Text } from "../ui/Typography";

const Intro = () => {
  return (
    <div className="flex flex-col justify-center items-center gap-[1rem]">
      <Text className="font-[600] p-2">
        Nigeria <span className="text-[#5CB85C] ">Health Facility</span>{" "}
        Registry (HFR)
      </Text>
      <Text className="w-full lg:w-[900px] text-center p-2">
      The Nigeria Health Facility Registry (HFR) is a comprehensive database designed to provide users with up-to-date information about healthcare facilities across Nigeria. Explore the registry to access vital information that can enhance healthcare delivery and promote informed decisions.
      </Text>
    </div>
  );
};

export default Intro;

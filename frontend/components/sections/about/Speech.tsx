import { Text } from "@/components/ui/Typography";
import React from "react";

const Speech = () => {
  return (
    <div className="bg-[#F2F2F2] rounded-lg w-full lg:w-[1200px] mx-auto mt-[3rem] p-8 flex flex-col gap-[1.5rem] items-center">
      <Text className="text-sm">
        The Honorable Minister of Health, Prof. Isaac F. Adewole at the
        presentation of the HFR to him stated that
      </Text>
      <Text className="italic">
        <span className="text-[#5CB85C] font-bold">“</span>
        Asking me to be a champion of the HFR is like preaching to the choir.
        Take it that I am already a champion.
        <span className="text-[#5CB85C]">”</span>
      </Text>
      <Text className="text-sm">
        The project was supported by the United States Agency for International
        Development.
      </Text>
    </div>
  );
};

export default Speech;

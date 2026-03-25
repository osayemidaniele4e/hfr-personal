import Image from "next/image";
import React from "react";

const Steps = ({ index: currentIndex }: { index: number }) => {
  return (
    <div>
      <div className="flex items-center space-x-2 mt-4">
        {[...Array(6)].map((_, index) => (
          <div
            key={index}
            className={`w-8 h-2 rounded-full ${
              index < currentIndex ? "bg-[#078586]" : "bg-gray-300"
            }`}
          ></div>
        ))}
        <span className="text-gray-500 text-sm ml-2">{currentIndex}/6</span>
        <Image src={"/nullIcon.svg"} width={18} height={18} alt="icon" />
      </div>
    </div>
  );
};

export default Steps;

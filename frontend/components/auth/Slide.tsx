import React, { useState, useEffect } from "react";
import Image from "next/image";

export interface SlideData {
  paragraph: string;
  image: string;
  name: string;
  username: string;
}

interface SliderProps {
  data: SlideData[];
}

const Slider: React.FC<SliderProps> = ({ data }) => {
  const [currentSlide, setCurrentSlide] = useState(0);
  const [value, setValue] = useState<number | null>(5);

  useEffect(() => {
    const interval = setInterval(() => {
      setCurrentSlide((prevSlide) =>
        prevSlide === data.length - 1 ? 0 : prevSlide + 1
      );
    }, 5000);

    return () => clearInterval(interval);
  }, [data]);

  return (
    <div className="relative overflow-hidden">
      <div
        className="flex transition-transform duration-200"
        style={{ transform: `translateX(-${currentSlide * 100}%)` }}
      >
        {data.map((item, index) => (
          <div
            key={index}
            className="flex flex-col justify-center items-center min-w-full flex-shrink-0"
          >
            <p className="text-[#1d1c20] mt-9 mx-16 text-lg font-medium lg:w-[600px]">
              "{item.paragraph}"
            </p>
            <div className="flex gap-[20rem] items-center mt-6 mx-16">
              <div className="flex items-center gap-6">
                <div>
                  <Image src={item.image} width={40} height={40} alt="img" />
                </div>
                <div>
                  <p className="font-semibold">{item.name}</p>
                  <p className="text-sm text-gray-500">{item.username}</p>
                </div>
              </div>
              <div>
                <Image src={"/Stars.svg"} width={80} height={80} alt="stars" />
              </div>
            </div>
          </div>
        ))}
      </div>
      <Indicators data={data} currentSlide={currentSlide} />
    </div>
  );
};

const Indicators: React.FC<{ data: SlideData[]; currentSlide: number }> = ({
  data,
  currentSlide,
}) => {
  return (
    <div className="absolute flex justify-center bottom-0 left-1/2 transform -translate-x-1/2">
      {data.map((_, index) => (
        <div
          key={index}
          className={`w-2 h-2 rounded-full mx-1 cursor-pointer ${
            currentSlide === index ? "bg-[#178d8d]" : "bg-gray-300"
          }`}
        ></div>
      ))}
    </div>
  );
};

export default Slider;

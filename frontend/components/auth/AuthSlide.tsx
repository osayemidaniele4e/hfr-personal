import React from "react";
import SectionContainer from "../ui/SectionContainer";
import Slider, { SlideData } from "./Slide";

const SwiperData = [
  {
    paragraph:
      "Discovering McAderson has been a game-changer for me. As someone juggling a busy schedule, I needed a platform that not only provided top-notch courses but also seamlessly integrated into my daily routine.",
    image: "/Photo.svg",

    name: "Jaleel_Lakin5",
    username: "@Maribeik",
  },
  {
    paragraph:
      "Discovering McAderson has been a game-changer for me. As someone juggling a busy schedule, I needed a platform that not only provided top-notch courses but also seamlessly integrated into my daily routine.",
    image: "/Photo.svg",
    name: "Jaleel_Lakin5",
    username: "@Maribeik",
  },
  {
    paragraph:
      "Discovering McAderson has been a game-changer for me. As someone juggling a busy schedule, I needed a platform that not only provided top-notch courses but also seamlessly integrated into my daily routine.",
    image: "/Photo.svg",
    name: "Jaleel_Lakin5",
    username: "@Maribeik",
  },
  {
    paragraph:
      "Discovering McAderson has been a game-changer for me. As someone juggling a busy schedule, I needed a platform that not only provided top-notch courses but also seamlessly integrated into my daily routine.",
    image: "/Photo.svg",
    name: "Jaleel_Lakin5",
    username: "@Maribeik",
  },
] as SlideData[];

const AuthSlide = () => {
  return (
    <div
      className="flex flex-col gap-3rem items-center "
      style={{
        backgroundImage: `
        url('/rside-image.svg')
      `,
        backgroundPosition: "right bottom",
        backgroundRepeat: "no-repeat",
        backgroundSize: "1000px 60%",
        minHeight: "100vh",
        overflowX: "hidden",
        overflowY: "hidden",
      }}
    >
      <SectionContainer>
        <Slider data={SwiperData} />
      </SectionContainer>
    </div>
  );
};

export default AuthSlide;

"use client";
import { FaArrowRight, FaLocationDot } from "react-icons/fa6";
import { useRouter, usePathname } from "next/navigation";
import AvatarGroup from "../ui/Avatar";
import Image from "next/image";
import Link from "next/link";

type BannerProps = {
  image_url: string;
  firstbtnLink?: string;
  secondbtnLink?: string;
  firstBtnTitle?: string;
  secondBtnTitle?: string;
  sub_title?: string;
  title?: string;
};
export type itemsProps = {
  items: BannerProps;
  index: number;
};

const MainPageBanner = ({ items, index }: itemsProps) => {
  const {
    title,
    sub_title,
    firstbtnLink,
    secondbtnLink,
    firstBtnTitle,
    secondBtnTitle,
    image_url,
  } = items;
  const { push } = useRouter();
  const pathname = usePathname();
  const isTalentPath = pathname === "/for-talents";

  const titleSplit = title?.split(" ");

  const part1 =
    titleSplit?.[0] && titleSplit?.[1]
      ? `${titleSplit[0]} ${titleSplit[1]}`
      : "";

  // const part1 =
  //   titleSplit?.[0] && titleSplit?.[1]
  //     ? `${titleSplit[0]} ${titleSplit[1]}`
  //     : "";

  const part2 = titleSplit?.[2] || "";
  const part3 = titleSplit?.[3] || "";

  const backImg = image_url ? image_url : "/bg-fac.png";

  console.log({ part1, part2, part3 });

  return (
    <div
      className={` relative ${
        index === 0 ? "mb-10" : "mb-20"
      } md:mb-0 py-2 sm:py-20 font-bold w-full px-4 mt-20 lg:mt-20 sm:px-10 `}
      style={{
        backgroundImage: `linear-gradient(
            rgba(0, 0, 0, 0.5), 
            rgba(0, 0, 0, 0.5)
          ), url(${backImg})`,
        backgroundPosition: "center top",
        backgroundSize: "cover",
        height: "100%",
        overflowX: "hidden",
        overflowY: "hidden",
      }}
    >
      <div className=" container lg:mx-auto">
        <div
          className={`flex flex-col-reverse lg:flex-row justify-center gap-10 sm:gap-10 md:pt-[3rem] items-center`}
          style={{ overflowX: "hidden" }}
        >
          <div className=" flex flex-col md:justify-left items-left gap-[1.5rem]  w-[100%] ">
            <h1
              className={`md:leading-[1.2] break-words leading-[1] font-[500] w-[100%] text-[#fff] text-left md:text-6xl text-4xl `}
            >
              {part1}
            </h1>

            <h1
              className={`md:leading-[1.2] break-words leading-[1] font-[500]  w-[100%] text-[#fff] text-left  md:text-6xl text-4xl mt-[1rem] lg:mt-[0]`}
            >
              {part2} <span className="text-[#5CB85C]">{part3}</span>
            </h1>

            <p className=" break-words  text-left  md:leading-[25px] text-[#fff] font-[400] w-[auto] lg:w-[650px]">
              {sub_title}
            </p>
            <div className="flex justify-center items-center gap-[.2rem] bg-[#5CB85C] w-[200px] rounded-lg p-3 cursor-pointer">
              <FaLocationDot color="#fff" fontSize={24} />
              <p
                className="text-[#fff] font-[400]"
                // onClick={() => push("/facilityfinder")}
              >
                <Link href="/facilityfinder" passHref>
                  Find Now
                </Link>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};
export default MainPageBanner;

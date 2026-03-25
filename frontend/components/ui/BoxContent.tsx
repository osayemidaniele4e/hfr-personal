import { UseDisclosureProps, useDisclosure } from "@chakra-ui/react";
import Image from "next/image";
import {
  Modal,
  ModalOverlay,
  ModalContent,
  ModalHeader,
  ModalFooter,
  ModalBody,
  ModalCloseButton,
} from "@chakra-ui/react";
import { FaArrowRight } from "react-icons/fa";
import Link from "next/link";
import { Heading } from "./Typography";
interface CorporatesDataProps {
  title: string;
  item?: string;
  list?: { item: string }[];
}

const BoxContent = ({
  data,
  width,
  link,
}: {
  data: CorporatesDataProps;
  width: string;
  link: string;
}) => {
  console.log(link);
  const { isOpen, onOpen, onClose } = useDisclosure();
  const { title, list, item } = data;
  return (
    <div
      onClick={onOpen}
      className={`flex flex-col w-full ${width} p-[4rem] px-20 h-[10rem] cursor-pointer hover:bg-[#CFE2F3]/50   hover:scale-105 rounded-xl bg-[#E8DFCA]  justify-center items-center gap-[1.5rem]`}
    >
      <Heading className=" leading-6 text-center text-[#172A54]  font-bold">
        {title}
      </Heading>
      <Modal
        isCentered
        isOpen={isOpen}
        size={{ base: "sm", xl: "sm" }}
        onClose={onClose}
      >
        <ModalOverlay />
        <ModalContent>
          <ModalCloseButton />
          <div className="flex flex-col  py-[4rem] px-10 cursor-pointer rounded-xl justify-center items-left gap-[1.5rem]">
            <Heading className=" text-left text-[#172A54]  leading-6 font-bold">
              {title}
            </Heading>
            <p className="font-bold text-[20px]">
              {title === "Other Bootcamps"
                ? null
                : title === "Data Analysis"
                ? "What will you get from this course?"
                : "Who is this course for?"}
            </p>
            {list && (
              <div className="flex flex-col gap-5">
                {list.map((i: any) => (
                  <div className="flex gap-2 items-start" key={i.item}>
                    {" "}
                    <Image
                      className="w-[20px] h-[20px]"
                      src="/checkbox-circle-fill.png"
                      width={20}
                      height={20}
                      alt={""}
                    />
                    <p className="text-[20px] leading-6 ">{i.item}</p>
                  </div>
                ))}
              </div>
            )}
            {item && (
              <div>
                {" "}
                <div className="flex gap-2 items-start">
                  {" "}
                  <Image
                    className="w-[20px] h-[20px]"
                    src="/checkbox-circle-fill.png"
                    width={20}
                    height={20}
                    alt={""}
                  />
                  <h1 className="text-sm md:text-md leading-6 font-bold ">
                    {item}
                  </h1>
                </div>
              </div>
            )}

            <div className="flex justify-start items-left">
              <Link href={link}>
                <button
                  className={`btn rounded-full text-[#fff] bg-[#80013F] hover:bg-[#2563EB] w-[100%]  md:w-[200px]  border-0`}
                >
                  Learn more
                  <FaArrowRight />
                </button>
              </Link>
            </div>
          </div>
        </ModalContent>
      </Modal>
    </div>
  );
};
export default BoxContent;

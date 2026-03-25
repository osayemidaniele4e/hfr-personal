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
import { useRouter } from "next/navigation";
import { FaArrowRight } from "react-icons/fa";
import BoxContent from "./BoxContent";
interface CorporatesDataProps {
  title: string;
  add?: string;
  item?: string;
  list?: { title: string }[];
}

export const NestedBoxContent = ({
  index,
  data,
  width,
}: {
  index: number;
  data: CorporatesDataProps;
  width: string;
}) => {
  const { isOpen, onOpen, onClose } = useDisclosure();
  const { title, list, item, add } = data;
  const router = useRouter();
  return (
    <div
      onClick={onOpen}
      key={index}
      className={`flex flex-col w-full lg:w-[${width}] h-[10rem] p-[2rem] px-20 cursor-pointer hover:bg-[#CFE2F3]/50   hover:scale-105 rounded-xl bg-[#CFE2F3]  justify-center items-center gap-[2rem]`}
    >
      <h1 className=" leading-6 text-center text-[#172A54]  font-bold  lg:text-md">
        {title}
      </h1>
      <Modal
        isCentered
        isOpen={isOpen}
        size={{ base: "md", xl: "2xl" }}
        onClose={onClose}
      >
        <ModalOverlay />
        <ModalContent>
          <ModalCloseButton />
          <div className="flex flex-col  py-[4rem] px-10 cursor-pointer rounded-xl justify-center items-center gap-[1.5rem]">
            {add && (
              <h1 className=" leading-6 text-center text-[#172A54]  font-bold  lg:text-md">
                {title} {add}
              </h1>
            )}
            {!add && (
              <h1 className=" leading-6 text-center text-[#172A54]  font-bold  lg:text-md">
                {title}
              </h1>
            )}
            {list && (
              <div className="grid md:grid-cols-3 text-center gap-5">
                {list.map((i: any) => (
                  <div key={index}>
                    {/* <p className="text-sm md:text-md leading-6 font-bold ">{i.item}</p> */}
                    <BoxContent link="/talent-register" data={i} width={""} />
                  </div>
                ))}
              </div>
            )}
            {item && (
              <div>
                {" "}
                <div key={index} className="flex gap-2 items-start">
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

            <div className="flex justify-center items-center">
              {/* <button onClick={()=>router.push(`/details/${index}`)} className=" flex  btn rounded-full text-[#fff] bg-[#2563EB] hover:bg-[#2563EB] w-[100%]  md:w-[200px]  border-0">
               More Details
           
                  <FaArrowRight />
                </button> */}
            </div>
          </div>
        </ModalContent>
      </Modal>
    </div>
  );
};

import { Box, IconButton, useDisclosure } from "@chakra-ui/react";
import { RiDeleteBin4Fill } from "react-icons/ri";
import Image from "next/image";
import ReusableModal from "../ui/ReusableModal";
import { DeleteUsers } from "@/app/lib/actions/admin-actions";
import { toast } from "react-toastify";

interface DeleteProps {
  title: string;
  paragraph: string;
  icon: string;
  userid?: string;
  roletype: string;
}

export const Delete = ({
  title,
  paragraph,
  icon,
  userid,
  roletype,
}: DeleteProps) => {
  const { isOpen, onOpen, onClose } = useDisclosure();

  const handleDelete = async (userid: any, roletype: string) => {
    try {
      const res = await DeleteUsers(userid, roletype);
      console.log(res);
      if (res?.statusCode === "00") {
        toast.success(res?.message);
        onClose();
      }
    } catch (error) {
      console.log(error);
    }
  };
  return (
    <Box>
      <IconButton
        aria-label="invoice"
        icon={<RiDeleteBin4Fill />}
        bg="none"
        _hover={{ color: "red" }}
        _active={{ color: "red" }}
        onClick={onOpen}
      />

      <ReusableModal
        isOpen={isOpen}
        onClose={onClose}
        size="md"
        closeOnOverlayClick={true}
        px="4"
        py="4"
      >
        <div className="flex flex-col justify-center items-center">
          <Image src={icon} width={70} height={70} alt="icon" />

          <p className="font-[700] mt-[1rem]">{title}</p>
          <p className="text-center text-sm mt-[1rem]">{paragraph}</p>

          <div className="mt-[1rem] flex flex-col justify-center items-center gap-[1rem]">
            <button
              onClick={() => handleDelete(userid, roletype)}
              className="bg-[#EF4444] w-full md:w-[330px] border-[none] rounded-full p-2 text-white"
            >
              Delete
            </button>
            <button
              className="bg-[#fff] w-full md:w-[330px] border border-[#6B6D70] rounded-full p-1 text-[#6B6D70]"
              onClick={() => onClose()}
            >
              Cancel
            </button>
          </div>
        </div>
      </ReusableModal>
    </Box>
  );
};

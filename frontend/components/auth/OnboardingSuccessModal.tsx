import React from "react";
import ReusableModal from "../ui/ReusableModal";
import Image from "next/image";
import { IoClose } from "react-icons/io5";
import { Text } from "../ui/Typography";

const OnboardingSuccessModal = ({
  isOpen,
  onClose,
}: {
  isOpen: boolean;
  onClose: () => void;
}) => {
  return (
    <div>
      <ReusableModal
        isOpen={isOpen}
        onClose={onClose}
        size="sm"
        header=""
        closeOnOverlayClick={true}
        px="4"
        py="4"
      >
        <div className="flex flex-col gap-[1rem]">
          <div className="flex justify-end">
            <IoClose
              onClick={() => onClose()}
              fontSize={24}
              className="cursor-pointer"
            />
          </div>

          <Image
            src={"/empty.svg"}
            width={84}
            height={84}
            alt="empty"
            className="block mx-auto "
          />

          <Text className="font-[600] text-center">You're all set!</Text>
          <Text className="text-center text-sm text-[#4F4D55]">
            Enjoy exploring McAderson and your personalized learning journey.
          </Text>
        </div>
      </ReusableModal>
    </div>
  );
};

export default OnboardingSuccessModal;

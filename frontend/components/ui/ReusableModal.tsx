import {
  Modal,
  ModalOverlay,
  ModalContent,
  ModalHeader,
  ModalFooter,
  ModalBody,
  ModalCloseButton,
} from "@chakra-ui/react";
import React from "react";

const ReusableModal = ({
  isOpen,
  onClose,
  children,
  closeOnOverlayClick,
  showheader,
  header,
  size,
  px,
  maxH,
  py,
}: {
  children: React.ReactNode;
  isOpen?: any;
  maxH?: string;
  onClose?: any;
  size: string;
  header?: string;
  showheader?: boolean;
  closeOnOverlayClick: boolean;
  px?: string;
  py?: string;
}) => {
  return (
    <>
      <Modal
        closeOnOverlayClick={closeOnOverlayClick}
        size={size}
        isCentered
        isOpen={isOpen}
        onClose={onClose}
        colorScheme="gray"
      >
        <ModalOverlay />
        <ModalContent
          rounded={"md"}
          maxH={maxH}
          overflow={"hidden"}
          // my={{ base: "2rem", xl: "20rem" }}
          mx={{ base: "2rem", xl: "0rem" }}
          px={px}
          py={py}
        >
          {showheader && (
            <>
              {" "}
              <ModalHeader>{header}</ModalHeader>
              <ModalCloseButton />
            </>
          )}
          <ModalBody overflowY={"auto"}>{children}</ModalBody>
        </ModalContent>
      </Modal>
    </>
  );
};
export default ReusableModal;

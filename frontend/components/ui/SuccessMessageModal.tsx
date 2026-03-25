import ReusableModal from "./ReusableModal";
import Image from "next/image";

const SuccessMessageModal = ({
  handleViewDashboard,
  isOpen,
  onClose,
}: {
  handleViewDashboard: () => void;
  isOpen: boolean;
  onClose: () => void;
}) => {
  return (
    <div>
      <ReusableModal
        onClose={onClose}
        size="sm"
        // showheader={true}
        header=""
        closeOnOverlayClick={true}
        px="4"
        py="4"
        isOpen={isOpen}
      >
        <div className="flex flex-col justify-center items-center gap-4">
          <Image
            src="/successCheck.svg"
            width={120}
            height={120}
            alt="Success"
          />
          <h2>Payment Successful</h2>
          <p>Your payment was successful!</p>
          <p className="text-lg font-bold">Thank you for buying this course</p>
          <button
            className="bg-[#078586] text-white w-full md:w-[450px] h-auto md:h-[44px]"
            onClick={handleViewDashboard}
          >
            Go home
          </button>
        </div>
      </ReusableModal>
    </div>
  );
};

export default SuccessMessageModal;

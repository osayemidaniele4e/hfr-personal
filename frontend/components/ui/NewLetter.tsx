import { IoPaperPlaneOutline } from "react-icons/io5";
import Input from "./Input";

const NewLetter = () => {
  return (
    <div className="flex flex-col w-[100%] gap-10 p-5  bg-[#E9E9F2] rounded-2xl">
      <div className="flex flex-col gap-3">
        {" "}
        <div className="text-[#2563EB] flex justify-center items-center  bg-[#C4D5E9] h-[40px] w-[40px] rounded-full">
          <IoPaperPlaneOutline fontSize={"1.2rem"} />
        </div>
        <h1 className="text-[#2563EB]">Weekly newsletter</h1>
        <p className="text-sm">
          We’ll keep you updated when the best new remote jobs pop up on
          TechyTeams
        </p>
        <Input placeholder="Enter your email" />
      </div>

      <button className="btn text-[#fff] bg-[#2563EB] hover:bg-[#2563EB] rounded-full w-[100%] border-0">
        Subscribe
      </button>
    </div>
  );
};
export default NewLetter;

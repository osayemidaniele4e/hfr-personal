import Image from "next/image";
const AvatarGroup = () => {
  return (
    <div className="avatar-group -space-x-4 rtl:space-x-reverse  z-[400px] ">
      <div className="avatar">
        <div className="w-10">
          <Image
            alt=""
            width={500}
            height={500}
            src="https://daisyui.com/images/stock/photo-1534528741775-53994a69daeb.jpg"
          />
        </div>
      </div>
      <div className="avatar">
        <div className="w-10">
          <Image
            alt=""
            width={500}
            height={500}
            src="https://daisyui.com/images/stock/photo-1534528741775-53994a69daeb.jpg"
          />
        </div>
      </div>
      <div className="avatar">
        <div className="w-10">
          <Image
            alt=""
            width={500}
            height={500}
            src="https://daisyui.com/images/stock/photo-1534528741775-53994a69daeb.jpg"
          />
        </div>
      </div>
      <div className="avatar placeholder">
        <div className="w-10 bg-[#4F5259] text-[12px] text-[#fff]">
          <span>12k+</span>
        </div>
      </div>
    </div>
  );
};
export default AvatarGroup;

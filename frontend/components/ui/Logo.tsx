import Image from "next/image";
const Logo = ({ onClick }: { onClick: () => void }) => {
  return (
    <Image
      alt=""
      width={500}
      height={500}
      src="/nhfr-logo.svg"
      className=" w-[250px] lg:w-[500px]"
      onClick={onClick}
    />
  );
};

const LogoWhite = () => {
  return (
    <Image
      alt=""
      width={500}
      height={500}
      src="/whitelogo.png"
      className=" w-[150px] lg:w-[150px]"
    />
  );
};

export { Logo, LogoWhite };

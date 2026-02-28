import { StaticImport } from "next/dist/shared/lib/get-img-props";
import Image from "next/image";

type childrenProps = {
  children: React.ReactNode;
  className?: string;
  disabled?: boolean;
  source?: string | StaticImport | React.ReactNode;
  onClick?: () => void;
};
const Heading = ({ children, className }: childrenProps) => {
  return (
    <h1
      className={`leading-10 text-left   xl:text-3xl text-2xl font-bold ${className}`}
    >
      {children}
    </h1>
  );
};

const Text = ({ children, className, onClick }: childrenProps) => {
  return (
    <p className={`text-left leading-6 ${className}`} onClick={onClick}>
      {children}
    </p>
  );
};
const GreenButton = ({ children, className, onClick, disabled }: childrenProps) => {
  return (
    <button
      className={`text-left leading-6 ${className} p-2 bg-[#326F32] rounded-lg text-white text-center`}
      onClick={disabled ? undefined : onClick}
      disabled={disabled}
    >
      {children}
    </button>
  );
};
const GreenButtonWithIcon = ({
  children,
  className,
  onClick,
  source,
}: childrenProps) => {
  return (
    <div
      className={`flex gap-[.5rem] bg-[#078586] rounded-lg w-full  p-2 justify-center ${className} text-white`}
      onClick={onClick}
    >
      {source && <Image src="" width={20} height={20} alt="att" />}
      {children}
    </div>
  );
};
const WhiteButtonWithIcon = ({
  children,
  className,
  onClick,
  source,
}: childrenProps) => {
  return (
    <div
      className={`flex gap-[.5rem] border rounded-lg w-full p-2 justify-center items-center ${className}`}
      onClick={onClick}
    >
      {children}
      {source && <Image src="" width={20} height={20} alt="att" />}
    </div>
  );
};
const RedButton = ({ children, className, onClick }: childrenProps) => {
  return (
    <button
      className={`text-left leading-6 ${className} p-2 bg-[#E12121] rounded-lg text-white text-center`}
      onClick={onClick}
    >
      {children}
    </button>
  );
};
const WhiteButton = ({ children, className, onClick }: childrenProps) => {
  return (
    <button
      className={`text-left leading-6 ${className} p-2 border rounded-lg text-center`}
      onClick={onClick}
    >
      {children}
    </button>
  );
};
export {
  Heading,
  Text,
  GreenButton,
  WhiteButton,
  RedButton,
  GreenButtonWithIcon,
  WhiteButtonWithIcon,
};

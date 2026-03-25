import { Heading, Text } from "@/components/ui/Typography";
import Image from "next/image";
import Link from "next/link";

interface EmailConfirmationMessageProps {
  message: string;
  success: boolean;
}

const EmailConfirmationMessage: React.FC<EmailConfirmationMessageProps> = ({
  message,
  success,
}) => (
  <>
    <div className="px-[2rem] md:px-[0] flex flex-col gap-[1rem] justify-center items-center">
      <>
        <Image src={"/authlogo.svg"} width={87} height={40} alt="logo" />
        <Heading>Thank you for signing up</Heading>
        <div className="flex flex-row gap-[.5rem]  p-2 rounded-lg w-full md:w-[400px] justify-center items-center">
          <Image
            src={"/email-sent-41JPeM0nc3.png"}
            width={200}
            height={200}
            alt="logo"
          />
        </div>
        <div className="flex flex-row justify-center items-center w-[100%] md:w-[400px]">
          {/* <div className="h-[1px] bg-[#DCDDE5] w-[100%] "></div>
          <p className="text-sm mx-[10px]">or</p>
          <div className="h-[1px] bg-[#DCDDE5] w-[100%]"></div> */}
        </div>

        <div className="flex flex-col justify-center items-start w-[100%] md:w-[400px] gap-[1rem]">
          <p className="text-lg mx-[10px] justify-center font-bold">
            {message}
          </p>
        </div>
        <div className="flex flex-row justify-center items-center w-[100%] md:w-[400px]">
          <Link
            className="text-white bg-[#078586] p-3 rounded-lg w-full md:w-[400px] "
            href={"/login"}
          >
            Login
          </Link>
        </div>
      </>
    </div>
  </>
);

export { EmailConfirmationMessage };

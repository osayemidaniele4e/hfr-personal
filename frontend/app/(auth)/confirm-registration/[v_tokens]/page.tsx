"use client";

import { EmailConfirmationMessage } from "@/components/auth/EmailConfirmationMessage";
import { useEmailConfirmationQuery } from "@/redux/services/authService";
import { Heading } from "@chakra-ui/react";
import Image from "next/image";

interface ConfirmRegistrationProps {
  params: {
    v_tokens: string;
  };
}
const ConfirmRegistration: React.FC<ConfirmRegistrationProps> = ({
  params,
}) => {
  const { v_tokens } = params;

  // Use optional chaining for safer access
  const { data, error, isLoading } = useEmailConfirmationQuery(v_tokens);

  if (isLoading) return <div>Loading...</div>;

  // Handle error and render data accordingly
  if (error)
    return (
      <div className="px-[2rem] md:px-[0] flex flex-col gap-[1rem] justify-center items-center">
        <Image src={"/authlogo.svg"} width={87} height={40} alt="logo" />
        <Heading>Error, Token is Invalid</Heading>

        <div className="px-[2rem] md:px-[0] flex flex-col gap-[1rem] justify-center items-center"></div>
        <div className="flex flex-col justify-center items-start w-[100%] md:w-[400px] gap-[1rem]">
          <p className="text-sm mx-[10px] justify-start font-bold"></p>
        </div>
      </div>
    );

  return (
    <div>
      <EmailConfirmationMessage
        success={!error}
        message={data?.message || "No message available."}
      />
    </div>
  );
};

export default ConfirmRegistration;

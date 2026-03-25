"use client";

import Input from "@/components/ui/Input";
import {
  useEmailConfirmationQuery,
  usePasswordChangeMutation,
} from "@/redux/services/authService";
import { PasswordChange } from "@/types/authType";
import * as Yup from "yup";
import { Heading, Text } from "@/components/ui/Typography";
import { Field, FieldProps, Form, Formik, FormikHelpers } from "formik";
import Image from "next/image";
import Link from "next/link";
import { MdError } from "react-icons/md";
import { toast } from "react-toastify";
import SuccessMessageModal from "@/components/ui/SuccessMessageModal";
import { useDisclosure } from "@chakra-ui/react";
import ReusableModal from "@/components/ui/ReusableModal";
import { useState } from "react";

interface ConfirmRegistrationProps {
  params: {
    v_tokens: string; // Define the type for v_tokens
  };
}
const ConfirmRegistration: React.FC<ConfirmRegistrationProps> = ({
  params,
}) => {
  const { v_tokens } = params;

  const [openModal, setOpenModal] = useState<boolean>(false);

  const [loginTalent, { isLoading }] = usePasswordChangeMutation();

  const initialValues: PasswordChange = {
    token: v_tokens,
    password: "",
    confirmPassword: "",
  };

  const validationSchema = Yup.object({
    password: Yup.string()
      .min(8, "Password must be at least 8 characters")
      .required("Password is required"),
    confirmPassword: Yup.string()
      .required("Confirm Password is required")
      .oneOf([Yup.ref("password")], "Passwords must match"),
  });

  const onSubmit = async (
    values: PasswordChange,
    { setSubmitting }: FormikHelpers<PasswordChange>
  ) => {
    const newValues = { ...values, token: v_tokens };
    try {
      await loginTalent(newValues).unwrap();

      setOpenModal(true);
    } catch (err: any) {
      toast.error(err!.data?.message);
      console.error("Error status:", err!.status);
      console.error("Error message:", err!.data?.message);
    }
  };

  // Use optional chaining for safer access
  //   const { data, error, isLoading } = useEmailConfirmationQuery(v_tokens);
  //   console.log(data, error, isLoading);

  //   if (isLoading) return <div>Loading...</div>;

  // Handle error and render data accordingly
  //   if (error)
  //     return (
  //       <div className="px-[2rem] md:px-[0] flex flex-col gap-[1rem] justify-center items-center">
  //         <Image src={"/authlogo.svg"} width={87} height={40} alt="logo" />
  //         <Heading>Error, Token is Invalid</Heading>

  //         <div className="px-[2rem] md:px-[0] flex flex-col gap-[1rem] justify-center items-center"></div>
  //         <div className="flex flex-col justify-center items-start w-[100%] md:w-[400px] gap-[1rem]">
  //           <p className="text-sm mx-[10px] justify-start font-bold"></p>
  //         </div>
  //       </div>
  //     );

  return (
    <div className="px-[2rem] md:px-[0] flex flex-col gap-[1rem] justify-center items-center">
      <ReusableModal
        isOpen={openModal}
        size="md"
        closeOnOverlayClick={true}
        px={"0.75rem"}
        py={"2rem"}
      >
        <div className="flex flex-col justify-center items-center gap-4">
          <Image
            src="/successCheck.svg"
            width={120}
            height={120}
            alt="Success"
          />
          <h2>Change Successful</h2>
          <p>Your Password Change is a success</p>
          <div className="w-full justify-center items-center flex">
            <Link
              className="bg-[#078586] rounded-xl text-white w-full md:w-[450px] h-auto md:h-[44px] items-center p-2 text-center"
              href="/login"
            >
              Login
            </Link>
          </div>
        </div>
      </ReusableModal>

      <Image src={"/authlogo.svg"} width={87} height={40} alt="logo" />
      <Heading>Password Change</Heading>
      <Text>Please kindly change your password</Text>
      <Formik
        initialValues={initialValues}
        validationSchema={validationSchema}
        onSubmit={onSubmit}
      >
        {({ errors, touched, isSubmitting }) => (
          <Form>
            <div className="flex flex-row justify-center items-center w-[100%] md:w-[400px]"></div>
            <div className="flex flex-col justify-center items-center w-[100%] md:w-[400px] gap-[1rem]">
              <>
                <Field name="password">
                  {({ field }: FieldProps) => (
                    <Input
                      type="password"
                      placeholder="Enter Password"
                      {...field}
                    />
                  )}
                </Field>
                {errors.password && touched.password && (
                  <div className="text-error flex gap-1 items-center">
                    <MdError fontSize={"1rem"} />
                    <p className="text-sm text-error">{errors.password}</p>
                  </div>
                )}
              </>
              <>
                <Field name="confirmPassword">
                  {({ field }: FieldProps) => (
                    <>
                      <Input
                        placeholder="Confirm Password"
                        type="password"
                        {...field}
                      />
                    </>
                  )}
                </Field>
                {errors.confirmPassword && touched.confirmPassword && (
                  <div className="text-error flex gap-1 items-center">
                    <MdError fontSize={"1rem"} />
                    <p className="text-sm text-error">
                      {errors.confirmPassword}
                    </p>
                  </div>
                )}
              </>

              <button
                disabled={isSubmitting}
                type="submit"
                className="text-white bg-[#078586] p-2 rounded-lg w-full md:w-[400px]"
              >
                {isSubmitting ? " Submitting ..." : "Submit"}
              </button>

              <Text className="inline">
                Recall Password?{" "}
                <Link
                  href="/createaccount"
                  className="text-[#078586] cursor-pointer"
                >
                  Back to Log in
                </Link>
                {/* <span></span> */}
              </Text>
            </div>
          </Form>
        )}
      </Formik>
    </div>
  );
};

export default ConfirmRegistration;

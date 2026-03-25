"use client";

import Input from "@/components/ui/Input";

import { Heading, Text } from "@/components/ui/Typography";
import { RegistrationRequestType } from "@/types/authType";
import * as Yup from "yup";
import Image from "next/image";
import React, { useState } from "react";
import { Field, FieldProps, Form, Formik, FormikHelpers } from "formik";
import { toast } from "react-toastify";
import { useTalentRegisterMutation } from "@/redux/services/authService";
import { MdError } from "react-icons/md";
import { RegistrationMessage } from "@/components/auth/RegistrationMessage";
import Link from "next/link";
import { signIn } from "@/app/lib/auth";
import { SignIn } from "@/app/lib/actions/company-actions";

const Signup = () => {
  const [registerTalent, { isLoading }] = useTalentRegisterMutation();
  const [registrationSuccessful, setRegistrationSuccessful] =
    useState<boolean>(false);
  const [userEmail, setUserEmail] = useState<string>("");
  const initialValues: RegistrationRequestType = {
    email: "",
    username: "",
    firstName: "",
    lastName: "",
    password: "",
    confirmPassword: "",
    role: "talent",
  };

  const validationSchema = Yup.object({
    firstName: Yup.string().required("First Name is required"),
    lastName: Yup.string().required("Last Name is required"),
    username: Yup.string().required("User Name is required"),
    email: Yup.string().email().required("Email Address is required"),

    password: Yup.string()
      .min(8, "Password must be at least 8 characters")
      .required("Password is required"),
    confirmPassword: Yup.string()
      .required("Confirm Password is required")
      .oneOf([Yup.ref("password")], "Passwords must match"),
  });

  const onSubmit = async (
    values: RegistrationRequestType,
    { setSubmitting }: FormikHelpers<RegistrationRequestType>
  ) => {
    const newValue = { ...values, role: "talent" };
    try {
      const response = await registerTalent(newValue).unwrap();
      if (response) {
        setRegistrationSuccessful(true);
        setUserEmail(values.email!);
      }
    } catch (err: any) {
      toast.error(err!.data?.message);
      console.error("Error status:", err!.status);
      console.error("Error message:", err!.data?.message);
    }
  };

  const handleEmailChange = () => {
    setUserEmail("");
    setRegistrationSuccessful(false);
  };

  return (
    <div className="px-[2rem] md:px-[0] flex flex-col gap-[1rem] justify-center items-center">
      {registrationSuccessful ? (
        <RegistrationMessage
          email={userEmail}
          onChangeEmail={handleEmailChange}
        />
      ) : (
        <>
          <Image src={"/authlogo.svg"} width={87} height={40} alt="logo" />
          <Heading>Create your account</Heading>
          <Text>Enter the fields below to get started</Text>

          <div className="flex flex-row gap-[.5rem] border p-2 rounded-lg w-full md:w-[400px] justify-center items-center">
            <Image src={"/google2.svg"} width={24} height={24} alt="logo" />
            <button onClick={() => SignIn()}>Sign up with Google</button>
          </div>
          <div className="flex flex-row justify-center items-center w-[100%] md:w-[400px]">
            <div className="h-[1px] bg-[#DCDDE5] w-[100%] "></div>
            <p className="text-sm mx-[10px]">or</p>
            <div className="h-[1px] bg-[#DCDDE5] w-[100%]"></div>
          </div>

          <div className="flex flex-col justify-center items-center w-[100%] md:w-[400px] gap-[1rem]">
            <Formik
              initialValues={initialValues}
              validationSchema={validationSchema}
              onSubmit={onSubmit}
            >
              {({ errors, touched, isSubmitting }) => (
                <Form>
                  <>
                    <Field name="firstName">
                      {({ field }: FieldProps) => (
                        <>
                          <Input placeholder="First Name" {...field} />
                        </>
                      )}
                    </Field>
                    {errors.firstName && touched.firstName && (
                      <div className="text-error flex gap-1 items-center">
                        <MdError fontSize={"1rem"} />
                        <p className="text-sm text-error">{errors.firstName}</p>
                      </div>
                    )}
                  </>
                  <>
                    <Field name="lastName">
                      {({ field }: FieldProps) => (
                        <>
                          <Input placeholder="Last Name" {...field} />
                        </>
                      )}
                    </Field>
                    {errors.lastName && touched.lastName && (
                      <div className="text-error flex gap-1 items-center">
                        <MdError fontSize={"1rem"} />
                        <p className="text-sm text-error">{errors.lastName}</p>
                      </div>
                    )}
                  </>
                  <>
                    <Field name="username">
                      {({ field }: FieldProps) => (
                        <>
                          <Input placeholder="User Name" {...field} />
                        </>
                      )}
                    </Field>
                    {errors.username && touched.username && (
                      <div className="text-error flex gap-1 items-center">
                        <MdError fontSize={"1rem"} />
                        <p className="text-sm text-error">{errors.username}</p>
                      </div>
                    )}
                  </>
                  <>
                    <Field name="email">
                      {({ field }: FieldProps) => (
                        <>
                          <Input placeholder="Email Address" {...field} />
                        </>
                      )}
                    </Field>
                    {errors.email && touched.email && (
                      <div className="text-error flex gap-1 items-center">
                        <MdError fontSize={"1rem"} />
                        <p className="text-sm text-error">{errors.email}</p>
                      </div>
                    )}
                  </>
                  <>
                    <Field name="password">
                      {({ field }: FieldProps) => (
                        <>
                          <Input
                            placeholder="Password"
                            type="password"
                            {...field}
                          />
                        </>
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
                  <br />

                  <button
                    disabled={isSubmitting}
                    type="submit"
                    className="text-white bg-[#078586] p-3 rounded-lg w-full md:w-[400px] mb-6"
                  >
                    {isSubmitting ? " Submit ..." : "Create account"}
                  </button>

                  <Text className="inline">
                    Already have an account?{" "}
                    <Link
                      href="/login"
                      className="text-[#078586] cursor-pointer"
                    >
                      Login
                    </Link>
                  </Text>
                </Form>
              )}
            </Formik>
          </div>
        </>
      )}
    </div>
  );
};

export default Signup;

"use client";

import { Text } from "@/components/ui/Typography";
import Image from "next/image";
import { useRouter } from "next/navigation";
import React from "react";

const Login: React.FC = () => {
  const { push } = useRouter();
  return (
    <div className="flex items-center justify-center lg:h-[100vh]">
      <div className="flex flex-col lg:flex-row justify-center lg:gap-[8rem] items-center w-full">
        <div className="hidden lg:block bg-[#F5F7FA] lg:h-[100vh]">
          {/* <img
            src="/SideBar.svg"
            alt="Reset Password Illustration"
            className="w-full h-auto"
          /> */}
          <Image
            src="/SideBar.svg"
            className="w-full h-auto"
            alt="Reset Password"
            priority
          />
        </div>

        <div className="w-full p-8 lg:p-32 ">
          <div className="text-center mb-6">
            {/* <img src="/new-logo.svg" alt="Logo" className="mx-auto h-16" /> */}
            <Image
              src="/new-logo.svg"
              className="mx-auto h-16"
              alt="Logo"
              priority
            />
          </div>

          <h1 className="text-xl font-semibold text-gray-800 text-center mb-2">
            NIGERIA Health Facility Registry (HFR)
          </h1>
          <p className="text-gray-600 text-center mb-6">
            Login to Access your Dashboard
          </p>

          {/* Form */}
          <form className="space-y-4 w-full lg:w-[600px] mx-auto">
            <div>
              <label
                htmlFor="email"
                className="block text-sm font-medium text-gray-700"
              >
                Email Address
              </label>
              <input
                type="email"
                id="email"
                placeholder="Enter Email Address"
                className="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500"
              />
            </div>

            <div>
              <label
                htmlFor="new-password"
                className="block text-sm font-medium text-gray-700"
              >
                Password
              </label>
              <input
                type="password"
                id="new-password"
                placeholder="Enter New Password"
                className="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500"
              />
            </div>
            <Text
              className="text-right cursor-pointer"
              onClick={() => push("/forgotpassword")}
            >
              Forgot Password
            </Text>

            <div className="flex flex-col items-center">
              <button
                type="submit"
                className="w-full lg:w-[180px] mx-auto py-2 px-4 bg-green-600 text-white font-medium rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
              >
                Login
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  );
};

export default Login;

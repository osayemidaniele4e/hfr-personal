"use client";

import React, { useState } from "react";

function Forgotpassword() {
  const [email, setEmail] = useState("");
  const [isLoading, setIsLoading] = useState(false);

  const handleSubmit = async (e: React.FormEvent) => {};

  return (
    <div
      className="min-h-screen flex items-center justify-center p-4"
      style={{
        backgroundImage: `url('/bg-two.svg'), url('/bg-one.svg')`,
        backgroundPosition: ` left, right bottom`,
        backgroundSize: `300px`,
        backgroundRepeat: `no-repeat, no-repeat`,
      }}
    >
      <div className=" w-full max-w-md">
        <div className="p-8">
          <h2 className="text-2xl font-bold text-center text-gray-900 mb-2">
            Forgot Password?
          </h2>
          <p className="text-center text-gray-600 mb-8">
            Enter your registered email address to reset your password.
          </p>

          <form onSubmit={handleSubmit}>
            <div className="mb-6">
              <label
                htmlFor="email"
                className="block text-sm font-medium text-gray-700 mb-2"
              >
                Email Address
              </label>
              <input
                id="email"
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                className="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all"
                placeholder="Enter Email Address"
                disabled={isLoading}
              />
            </div>

            <button
              type="submit"
              disabled={isLoading}
              className={`w-full py-3 px-4 rounded-lg text-white font-medium lg:mt-[4rem]
                ${
                  isLoading
                    ? "bg-green-400 cursor-not-allowed"
                    : "bg-green-600 hover:bg-green-700"
                } transition-colors duration-200`}
            >
              {isLoading ? "Sending Reset Link..." : "Request Password Reset"}
            </button>
          </form>

          <div className="text-center mt-6">
            <a
              href="/login"
              className="text-sm text-green-600 hover:text-green-700"
            >
              Back to Login
            </a>
          </div>
        </div>
      </div>
    </div>
  );
}

export default Forgotpassword;

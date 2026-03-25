"use client";
import React from "react";
import "./globals.css";
import ReduxProvider from "@/redux/Provider";
import { GoogleOAuthProvider } from "@react-oauth/google";
import { ToastContainer } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
import { ChakraProviders } from "@/components/layouts/ChakraProvider";

const MainLayout = ({ children }: { children: React.ReactNode }) => {
  return (
    <html lang="en">
      <head>
        <link rel="icon" href="/favicon.png" type="image/png" />
      </head>
      <body className="font-body">
        <ToastContainer />
        <ChakraProviders>
          <ReduxProvider>
            {" "}
            <GoogleOAuthProvider clientId="456103874366-tr5slm3dfbl18afk7j2nrgu99ocdmm2j.apps.googleusercontent.com">
              {children}
            </GoogleOAuthProvider>
          </ReduxProvider>
        </ChakraProviders>
      </body>
    </html>
  );
};

export default MainLayout;

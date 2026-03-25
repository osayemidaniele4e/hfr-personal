"use client";

import AuthSlide from "@/components/auth/AuthSlide";
import Header from "@/components/Header/Header";
import AuthHeader from "@/components/layouts/AuthHeader";

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <>
      <div className="flex flex-col bg-white md:flex-row items-start relative">
        <div
          className="w-full z-10"
          
        >
          {/* <Header /> */}
          {children}
        </div>
      </div>
    </>
  );
}

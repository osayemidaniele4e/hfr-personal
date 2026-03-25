"use client";
import Footer, { RightReserved } from "@/components/layouts/Footer";
import Header from "@/components/Header/Header";
import { useEffect, useState } from "react";
import Chatbot from "@/components/chatbot/Chatbot";

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  const [scrollPosition, setScrollPosition] = useState(0);
  useEffect(() => {
    const handleScroll = () => {
      setScrollPosition(window.scrollY);
    };
    window.addEventListener("scroll", handleScroll);
    return () => {
      window.removeEventListener("scroll", handleScroll);
    };
  }, []);

  return (
    <div>
      <Header />

      {children}
      <Footer />
      <Chatbot />
      <RightReserved />
    </div>
  );
}

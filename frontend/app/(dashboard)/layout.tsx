"use client";

import React, { useState, useEffect } from "react";
import {
  Drawer,
  DrawerBody,
  DrawerHeader,
  DrawerOverlay,
  DrawerContent,
  DrawerCloseButton,
  useDisclosure,
  Button,
} from "@chakra-ui/react";
import Header from "@/components/Header/Header";
import { Text } from "@/components/ui/Typography";
import { LayoutDashboard } from "lucide-react";
import { usePathname } from "next/navigation";
import { PiDiamondsFour } from "react-icons/pi";
import { LuBuilding2, LuArrowDownToLine } from "react-icons/lu";
import { RiBuilding2Line } from "react-icons/ri";
import { GoFileDirectory } from "react-icons/go";
import { FiBook } from "react-icons/fi";
import { MdOutlineDoubleArrow } from "react-icons/md";

const topNavData = [
  {
    icon: PiDiamondsFour,
    text: "Overview",
    navitem: "Overview",
    link: "/overview",
  },
  {
    icon: LuBuilding2,
    text: "Facility Finder",
    navitem: "facilityfinder",
    link: "/facilityfinder",
  },
  {
    icon: RiBuilding2Line,
    text: "Facility List",
    navitem: "facilitieslist",
    link: "/facilitieslist",
  },
];
const bottomNavData = [
  {
    icon: LuArrowDownToLine,
    text: "Data Request",
    navitem: "Data Request",
    link: "/datadownloads",
  },
  {
    icon: GoFileDirectory,
    text: "Resources",
    navitem: "resources",
    link: "/resources",
  },
  {
    icon: FiBook,
    text: "Reports",
    navitem: "reports",
    link: "/reports",
  },
];

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  const { isOpen, onOpen, onClose } = useDisclosure();
  const pathname = usePathname();
  const [isDesktop, setIsDesktop] = useState(false);

  useEffect(() => {
    const checkWidth = () => setIsDesktop(window.innerWidth >= 1024);
    checkWidth(); // run on mount
    window.addEventListener("resize", checkWidth);
    return () => window.removeEventListener("resize", checkWidth);
  }, []);

  const renderNavLinks = (navData: typeof topNavData) => (
    <nav>
      {navData.map((item, index) => (
        <a
          key={index}
          href={item.link}
          className={`flex items-center py-3 text-gray-700 transition-colors ${
            pathname === item.link
              ? "bg-[#5BBA62] px-2 text-white mx-4 rounded-lg"
              : "transparent px-6"
          }`}
        >
          <item.icon className="h-5 w-5 mr-3" />
          {item.text}
        </a>
      ))}
    </nav>
  );

  return (
    <div>
      <Header />

      {/* Mobile menu toggle - shows below header on small screens */}
      {!isDesktop && (
        <div style={{ marginTop: "80px" }}>
          <MdOutlineDoubleArrow fontSize={44} onClick={onOpen} />
        </div>
      )}

      {/* Sidebar Drawer for Mobile */}
      <Drawer isOpen={isOpen} placement="left" onClose={onClose}>
        <DrawerOverlay />
        <DrawerContent>
          <DrawerCloseButton />
          <DrawerHeader>
            <h1 className="text-2xl font-bold text-gray-800 flex items-center gap-2">
              <LayoutDashboard className="h-6 w-6 text-emerald-600" />
              Dashboard
            </h1>
          </DrawerHeader>
          <DrawerBody>
            <Text className="text-[#AEAEAE] px-6 py-1">Overview</Text>
            {renderNavLinks(topNavData)}
            <Text className="text-[#AEAEAE] px-6 py-1 mt-6">Resources</Text>
            {renderNavLinks(bottomNavData)}
          </DrawerBody>
        </DrawerContent>
      </Drawer>

      {/* Desktop sidebar - rendered when screen >= 1024px */}
      {isDesktop && (
        <aside
          style={{
            position: "fixed",
            top: 0,
            left: 0,
            width: "256px",
            height: "100vh",
            paddingTop: "96px",
            borderRight: "1px solid #e5e7eb",
            background: "#fff",
            zIndex: 40,
            overflowY: "auto",
          }}
        >
          <nav className="mt-6">
            <Text className="text-[#AEAEAE] px-6 py-1">Overview</Text>
            {renderNavLinks(topNavData)}
          </nav>
          <nav className="mt-6">
            <Text className="text-[#AEAEAE] px-6 py-1">Resources</Text>
            {renderNavLinks(bottomNavData)}
          </nav>
        </aside>
      )}

      {/* Main content area */}
      <main
        style={{
          paddingTop: "96px",
          marginLeft: isDesktop ? "256px" : "0",
          minHeight: "100vh",
          background: "#fff",
        }}
      >
        {children}
      </main>
    </div>
  );
}

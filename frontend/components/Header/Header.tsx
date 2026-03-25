"use client";
import React, { useState } from "react";
import {
  Box,
  Flex,
  HStack,
  useDisclosure,
  IconButton,
  Slide,
  Stack,
  Collapse,
  Container,
  MenuButton,
  Icon,
  Menu,
  MenuItem,
  MenuList,
  Button,
} from "@chakra-ui/react";
import Link from "next/link";
import { Logo, LogoWhite } from "../ui/Logo";
import DeskTopNav from "./DeskTopNav";

import { usePathname, useRouter } from "next/navigation";
import { AiOutlineClose } from "react-icons/ai";
import { IoIosMenu } from "react-icons/io";
import { LoginData, NavLinks } from "@/data/NavLinks";
import Dropdown from "../ui/Dropdown";
import { MobileNav } from "./MobileNav";
import Image from "next/image";

const Header = () => {
  const pathname = usePathname();
  const isTalentPath = pathname === "/";
  const slug = pathname.includes("/details/");
  const isWhyPath = pathname === "/contact";
  const { isOpen, onToggle, onClose } = useDisclosure();
  const { push } = useRouter();
  const router = useRouter();

  const handleLogoClick = () => {
    window.location.href = "/";
  };

  return (
    <Box pos={"fixed"} zIndex={100} w={"full"}>
      <Flex
        direction={"row"}
        alignItems="center"
        h={{ base: "70px", xl: "80px" }}
        justifyContent={"space-between"}
        px={{ base: "2rem", xl: "4%" }}
        background={"#fff"}
        backdropFilter="blur(5px)"
        boxShadow=" 0px 4px 9px 0px rgba(0, 0, 0, 0.10)"
      >
        <Link href="/">
          {/* <Logo onClick={() => push("/")} /> */}
          <Logo onClick={handleLogoClick} />
        </Link>

        <Flex
          flexDirection={"row"}
          alignItems={"center"}
          justifyContent={{ base: "center", xl: "space-between" }}
          py={{ base: "1rem", xl: "1rem" }}
          gap={{ base: "10rem", xl: "1rem" }}
        >
          <Box
            display={{ base: "none", xl: "flex" }}
            color={isTalentPath || slug || isWhyPath ? "#6B6D70" : "#fff"}
          >
            <DeskTopNav />
          </Box>
          <Flex
            mr="1rem"
            display={{ base: "none", xl: "flex" }}
            gap={".5rem"}
            justifyContent={"center"}
            alignItems={"center"}
          >
            <Image
              src="/Profile-icon.svg"
              width={30}
              height={30}
              alt="cart"
              className="cursor-pointer"
            />
            <Link
              href={
                process.env.NEXT_PUBLIC_BACKEND_URL
                  ? `${process.env.NEXT_PUBLIC_BACKEND_URL}/login`
                  : "/login"
              }
            >
              <button className="hover:text-[#202020] text-[#4F4D55] bg-[transparent]">
                Login
              </button>
            </Link>
          </Flex>

          <Flex display={{ base: "flex", xl: "none" }} flex={"start"}>
            <IconButton
              onClick={onToggle}
              _hover={{ bg: "transparent" }}
              color={"#6B6D70"}
              icon={
                isOpen ? (
                  <AiOutlineClose size="1.5rem" onClick={onClose} />
                ) : (
                  <IoIosMenu fontSize="2rem" />
                )
              }
              variant={"ghost"}
              aria-label={"Toggle Navigation"}
            />
          </Flex>
        </Flex>
      </Flex>
      <Collapse in={isOpen} style={{ zIndex: 5 }}>
        <Stack
          mt={"0rem"}
          color="white"
          bg="#fff"
          display={{ xl: "none" }}
          shadow="md"
          h={"100vh"}
          onClick={onClose}
        >
          <MobileNav />
        </Stack>
      </Collapse>
    </Box>
  );
};

export default Header;

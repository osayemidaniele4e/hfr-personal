"use client";
import React, { useState } from "react";
import {
  Box,
  Flex,
  HStack,
  Link,
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
import { Logo, LogoWhite } from "../ui/Logo";
import DeskTopNav from "./DeskTopNavSticky";

import { AiOutlineClose } from "react-icons/ai";

import MobileNav from "./MobileNavSticky";

import { usePathname } from "next/navigation";
import { IoIosMenu } from "react-icons/io";
import {  FaChevronDown } from "react-icons/fa";
import { LoginData, NavLinks } from "@/data/NavLinks";

import SkickyDesktopNav from "./DeskTopNavSticky";
import MobileNavSticky from "./MobileNavSticky";
import StickyDropdown from "../ui/StickyDropdown";

const StickyHeader = () => {
  const pathname = usePathname();

  const { isOpen, onToggle, onClose } = useDisclosure();
  return (
    <Box    pos={"fixed"} top={"0"} zIndex={100} w={"full"} >
      <Flex
        direction={"row"}
        alignItems="center"
        h="100px"
        justifyContent={"space-between"}
        px={{ base: "2rem", xl: "4%" }}
        background="#0B1C4C"
        // boxShadow=" 0px 4px 9px 0px rgba(0, 0, 0, 0.10)"
      
      >

          <Link href="/" cursor={"pointer"}>
          < LogoWhite />
         
          </Link>
     
        <Box display={{ base: "none", xl: "flex" }} color= "#fff">
          <SkickyDesktopNav />
        </Box>

        <Flex
          flexDirection={"row"}
          alignItems={"center"}
          justifyContent={{ base: "center", xl: "space-between" }}
          py={{ base: "1rem", xl: "1rem" }}
          gap={{ base: "10rem", xl: "0rem" }}
        >
          <Flex mr="1rem" display={{ base: "none", xl: "flex" }} gap={"1rem"}   justifyContent={"center"} alignItems={"center"}>
            <button className=" btn btn-md rounded-full  hover:text-[#fff] hover:bg-primary hover:border-[#fff] btn-outline text-white bg-primary border-2 border-primary w-[180px]">
              Hire Talents
            </button>
            <div className="flex gap-[2rem] font-bold justify-center items-center">
            {" "}
            {LoginData.map((items,index) => (
              <StickyDropdown key={index} {...items} />
            ))}
          </div>
          </Flex>

          <Flex display={{ base: "flex", xl: "none" }} flex={"start"}>
            <IconButton
              onClick={onToggle}
              _hover={{ bg: "transparent" }}
              icon={
                isOpen ? (
                  <AiOutlineClose
                    size="1.5rem"
                    color="#fff"
                    onClick={onClose}
                  />
                ) : (
                  <IoIosMenu fontSize="2rem"  color="#fff"/>
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
          color="#fff"
          bg="#0B1C4C"
          display={{ xl: "none" }}
          shadow="md"
          onClick={onClose}
        >
          <MobileNavSticky />
        </Stack>
      </Collapse>
    </Box>
  );
};

export default StickyHeader;

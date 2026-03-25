import React from "react";
import { Flex, Text, Link } from "@chakra-ui/react";
import { NavLinks } from "@/data/NavLinks";

const SkickyDesktopNav = () => {
  return (
    <Flex
      flexDirection={"row"}
      alignItems={"center"}
      justifyContent={"center"}
      // color="#2A2A2A"
      gap={"2rem"}
      fontWeight={"500"}
    >
      {NavLinks.map((item, index) => (
        <Text
          as={Link}
          href={item.link}
          key={index}
          fontSize={"md"}
          fontWeight={"bold"}
          _hover={{
            cursor: "pointer",
            color: "",
            textDecor: "none",
          }}
          lineHeight={"32px"}
        >
          {item.navitem}
        </Text>
      ))}
    </Flex>
  );
};

export default SkickyDesktopNav;

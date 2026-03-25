import React from "react";
import { Flex, Text, Link } from "@chakra-ui/react";
import { NavLinks } from "@/data/NavLinks";
import { usePathname } from "next/navigation";

const DeskTopNav = () => {
  const pathname = usePathname();
  return (
    <Flex
      flexDirection={"row"}
      alignItems={"center"}
      justifyContent={"center"}
      gap={"1rem"}
      fontWeight={"700"}
    >
      {NavLinks.map((item, index) => (
        <Text
          as={Link}
          href={item.link}
          key={index}
          fontSize={{ base: "md", "2xl": "lg" }}
          fontWeight={"400"}
          color={pathname === item.link ? "#5BBA62" : "#202020"}
          padding={".5rem"}
          borderRadius={".5rem"}
          _hover={{
            cursor: "pointer",
            color: "#0B1C4C",
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

export default DeskTopNav;

import {
  Stack,
  useDisclosure,
  Flex,
  Icon,
  Box,
  Text,
  Link,
  Button,
} from "@chakra-ui/react";
import { usePathname } from "next/navigation";
import React from "react";
import { LoginData, NavLinks } from "@/data/NavLinks";
import StickyDropdown from "../ui/StickyDropdown";
const MobileNavSticky = () => {
  const pathname = usePathname();


  return (
    <Stack bg={"#0B1C4C"} p={10} display={{ xl: "none" }}>
      {NavLinks.map((navItem) => (
        <MobileNavItem key={navItem.navitem} {...navItem} />
      ))}
      <Box pt="1rem"   py={4}    borderBottom={"0.5px solid rgba(175, 175, 175, 1) "}>
      <Text fontSize={"sm"} fontWeight={600} color={"#fff"}>
              Hire Talents
            </Text>
      </Box>
      <div className="flex gap-[2rem] color-[#4F4F4F] font-bold justify-center items-center">
            {" "}
            {LoginData.map((items,index) => (
              <StickyDropdown  key={index}  {...items} />
            ))}
          </div>
    </Stack>
  );
};

export default MobileNavSticky;

const MobileNavItem = ({ navitem, children, link }: { navitem: any; children?: any; link?: any; }) => {
  const { onClose, isOpen, onToggle } = useDisclosure();

  return (
    <Stack  spacing={4} onClick={children && onClose}>
      <Flex
        py={4}
        as={Link}
        href={link ?? "#"}
        justify={"space-between"}
        align={"center"}
        _hover={{
          textDecoration: "none",
        }}
        borderBottom={"0.5px solid rgba(175, 175, 175, 1) "}
      >
        <Text fontSize={"sm"} fontWeight={600} color={"#fff"}>
          {navitem}
        </Text>
      </Flex>
    </Stack>
  );
};

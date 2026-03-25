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
import { AdminNavLinks, LoginData, NavLinks } from "@/data/NavLinks";
import Dropdown from "../ui/Dropdown";
export const MobileNav = () => {
  const pathname = usePathname();

  return (
    <Stack bg={"#fff"} p={10} display={{ xl: "none" }}>
      {NavLinks.map((navItem) => (
        <MobileNavItem key={navItem.navitem} {...navItem} />
      ))}

      <Flex
        flexDirection="column"
        gap={"1rem"}
        marginTop={"1rem"}
        alignItems={"left"}
      >
        <Link
          href={
            process.env.NEXT_PUBLIC_BACKEND_URL
              ? `${process.env.NEXT_PUBLIC_BACKEND_URL}/login`
              : "/login"
          }
        >
          <Button
            className="btn btn-md rounded-md hover:text-[#fff] hover:bg-[#078586] hover:border-[#fff] btn-outline text-[#4F4D55] bg-[transparent] border-2 border-[#E6E6E6] w-[180px]"
            variant="outline"
          >
            Login
          </Button>
        </Link>
      </Flex>
    </Stack>
  );
};
export const MobileNavAdmin = () => {
  const pathname = usePathname();

  return (
    <Stack bg={"#fff"} p={10} display={{ xl: "none" }}>
      {AdminNavLinks.map((navItem) => (
        <MobileNavAdminItem key={navItem.navitem} {...navItem} />
      ))}
      <Box
        pt="1rem"
        py={4}
        borderBottom={"0.5px solid rgba(175, 175, 175, 1) "}
      >
        <Text fontSize={"sm"} fontWeight={600} color={"#4F4F4F"}>
          Hire Talents
        </Text>
      </Box>
      <div className="flex gap-[2rem] color-[#4F4F4F] font-bold justify-center items-center">
        {" "}
        {LoginData.map((items, index) => (
          <Dropdown key={index} {...items} />
        ))}
      </div>
    </Stack>
  );
};

const MobileNavItem = ({
  navitem,
  children,
  link,
}: {
  navitem: any;
  children?: any;
  link?: any;
}) => {
  const { onClose, isOpen, onToggle } = useDisclosure();

  return (
    <Stack spacing={4} onClick={children && onClose}>
      <Flex
        py={4}
        as={Link}
        href={link ?? "#"}
        justify={"left"}
        align={"left"}
        _hover={{
          textDecoration: "none",
        }}
        // borderBottom={"0.5px solid rgba(175, 175, 175, 1) "}
      >
        <Text fontSize={"sm"} fontWeight={600} color={"#4F4F4F"}>
          {navitem}
        </Text>
      </Flex>
    </Stack>
  );
};
const MobileNavAdminItem = ({
  navitem,
  children,
  link,
}: {
  navitem: any;
  children?: any;
  link?: any;
}) => {
  const { onClose, isOpen, onToggle } = useDisclosure();

  return (
    <Stack spacing={4} onClick={children && onClose}>
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
        <Text fontSize={"sm"} fontWeight={600} color={"#4F4F4F"}>
          {navitem}
        </Text>
      </Flex>
    </Stack>
  );
};

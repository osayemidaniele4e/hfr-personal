"use client";
import React, { useState } from "react";
import {
  Table,
  Thead,
  Tbody,
  Tr,
  Th,
  Td,
  TableContainer,
  IconButton,
  Button,
  useDisclosure,
} from "@chakra-ui/react";
import { FiMoreVertical } from "react-icons/fi";
import { useBreakpointValue } from "@chakra-ui/react";
import {
  IoMdArrowBack,
  IoMdArrowDown,
  IoMdArrowDropdown,
} from "react-icons/io";
import { IoArrowForward } from "react-icons/io5";
import { Text } from "@/components/ui/Typography";
import { HiMiniBars4 } from "react-icons/hi2";

const users = [
  {
    SN: 1,
    State: "Nigeria",
    Primary: 4132,
    Secondary: 4132,
    Tertiary: 4132,
    Total: 4132,
  },
  {
    SN: 2,
    State: "Abia",
    Primary: 783,
    Secondary: 783,
    Tertiary: 783,
    Total: 783,
  },
  {
    SN: 3,
    State: "Adamawa",
    Primary: 410,
    Secondary: 410,
    Tertiary: 410,
    Total: 410,
  },
  {
    SN: 4,
    State: "Akwa Ibom",
    Primary: 555,
    Secondary: 555,
    Tertiary: 555,
    Total: 555,
  },
  {
    SN: 5,
    State: "Anambra",
    Primary: 277,
    Secondary: 277,
    Tertiary: 277,
    Total: 277,
  },
  {
    SN: 6,
    State: "Bauchi",
    Primary: 907,
    Secondary: 907,
    Tertiary: 907,
    Total: 907,
  },
  {
    SN: 7,
    State: "Bayelsa",
    Primary: 689,
    Secondary: 689,
    Tertiary: 689,
    Total: 689,
  },
  {
    SN: 8,
    State: "Benue",
    Primary: 128,
    Secondary: 128,
    Tertiary: 128,
    Total: 128,
  },
  {
    SN: 9,
    State: "Borno",
    Primary: 342,
    Secondary: 342,
    Tertiary: 342,
    Total: 342,
  },
  {
    SN: 10,
    State: "Cross River",
    Primary: 723,
    Secondary: 723,
    Tertiary: 723,
    Total: 723,
  },
  {
    SN: 11,
    State: "Delta",
    Primary: 136,
    Secondary: 136,
    Tertiary: 136,
    Total: 136,
  },
  {
    SN: 12,
    State: "Ebonyi",
    Primary: 621,
    Secondary: 621,
    Tertiary: 621,
    Total: 621,
  },
  {
    SN: 13,
    State: "Edo",
    Primary: 588,
    Secondary: 588,
    Tertiary: 588,
    Total: 588,
  },
  {
    SN: 14,
    State: "Ekiti",
    Primary: 512,
    Secondary: 512,
    Tertiary: 512,
    Total: 512,
  },
];
const TableOwnership = () => {
  const isMobile = useBreakpointValue({ base: true, md: false });
  const [currentPage, setCurrentPage] = useState(1);

  const itemsPerPage = 10;

  const paginatedUsers = users.slice(
    (currentPage - 1) * itemsPerPage,
    currentPage * itemsPerPage
  );

  const totalPages = Math.ceil(users.length / itemsPerPage);

  return (
    <div className="p-1">
      <div className="flex flex-col gap-1 items-center mb-4">
        <TableContainer overflowX="auto" maxW="full">
          <div className="flex justify-between items-start bg-[#E8F0E2] p-4 rounded-t-md">
            <h2 className="text-sm mb-4 text-wrap lg:text-nowrap">
              Percentage of Hospitals and Clinics by Level of Care
            </h2>
            <HiMiniBars4 fontSize={24} />
          </div>
          <Table
            variant="striped"
            size={isMobile ? "sm" : "md"}
            className="compact-table"
          >
            <Thead>
              <Tr>
                <Th
                  fontSize={16}
                  p={2.5}
                  textTransform="none"
                  fontWeight={"medium"}
                  className="text-[#667085]"
                >
                  SN
                </Th>
                <Th
                  fontSize={16}
                  p={2.5}
                  textTransform="none"
                  fontWeight={"medium"}
                  className="text-[#667085]"
                >
                  State <IoMdArrowDown className="inline ml-1" />
                </Th>
                <Th
                  fontSize={16}
                  p={2.5}
                  textTransform="none"
                  fontWeight={"medium"}
                  className="text-[#667085]"
                >
                  Primary <IoMdArrowDown className="inline ml-1" />
                </Th>
                <Th
                  fontSize={16}
                  p={2.5}
                  textTransform="none"
                  fontWeight={"medium"}
                  className="text-[#667085]"
                >
                  Secondary <IoMdArrowDown className="inline ml-1" />
                </Th>
                <Th
                  fontSize={16}
                  p={2.5}
                  textTransform="none"
                  fontWeight={"medium"}
                  className="text-[#667085]"
                >
                  Tertiary <IoMdArrowDown className="inline ml-1" />
                </Th>
                <Th
                  fontSize={16}
                  p={2.5}
                  textTransform="none"
                  fontWeight={"medium"}
                  className="text-[#667085]"
                >
                  Total <IoMdArrowDown className="inline ml-1" />
                </Th>
              </Tr>
            </Thead>
            <Tbody>
              {paginatedUsers.map((user, index) => (
                <Tr key={index}>
                  <Td p={3} className="text-gray-500 text-sm">
                    {user.SN}
                  </Td>
                  <Td p={3} className=" text-sm">
                    {user.State}
                  </Td>
                  <Td p={3} className="text-gray-500 text-sm">
                    {user.Primary}
                  </Td>
                  <Td p={3} className="text-gray-500 text-sm">
                    {user.Secondary}
                  </Td>
                  <Td p={3} className="text-gray-500 text-sm">
                    {user.Tertiary}
                  </Td>
                  <Td p={3} className="text-gray-500 text-sm">
                    {user.Total}
                  </Td>
                </Tr>
              ))}
            </Tbody>
          </Table>
        </TableContainer>

        {totalPages > 1 && (
          <div className="flex justify-center items-center mt-4 gap-[1rem] mt-[2rem] overflow-hidden">
            <Button
              isDisabled={currentPage === 1}
              onClick={() => setCurrentPage((prev) => prev - 1)}
            >
              <IoMdArrowBack />
            </Button>
            <Text>{`${currentPage} of ${totalPages}`}</Text>
            <Button
              isDisabled={currentPage === totalPages}
              onClick={() => setCurrentPage((prev) => prev + 1)}
            >
              <IoArrowForward />
            </Button>
          </div>
        )}
      </div>
    </div>
  );
};

export default TableOwnership;

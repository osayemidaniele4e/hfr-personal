import React from "react";
import { chakra, Table, Thead, Tr, Th, Tbody } from "@chakra-ui/react";

const ReusableTable = ({
  tableHeader,
  variant,
  bgColor,
  children,
}: {
  tableHeader: string[];
  children: React.ReactElement | any;
  variant?: string;
  bgColor?: string;
}) => {
  return (
    <chakra.div
      overflowX="auto"
      borderColor="#E3E7EB"
      rounded="lg"
      mt="2"
      fontSize="sm"
    >
      <Table variant={variant} fontWeight="semibold">
        <Thead fontFamily={"Comfortaa"} border="none" bg={bgColor} >
          <Tr>
            {tableHeader.map((column) => (
              <Th
                border="none"
                whiteSpace="nowrap"
                py="5"
                textTransform="none"
                fontSize="sm"
                key={column}
              >
                {column}
              </Th>
            ))}
          </Tr>
        </Thead>
        <Tbody>{children}</Tbody>
      </Table>
    </chakra.div>
  );
};

export default ReusableTable;

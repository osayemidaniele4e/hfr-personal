"use client";
import React, { useEffect, useState } from "react";
import {
  Table,
  Thead,
  Tbody,
  Tr,
  Th,
  Td,
  TableContainer,
  Button,
} from "@chakra-ui/react";
import { useBreakpointValue } from "@chakra-ui/react";
import { IoMdArrowBack, IoMdArrowDown } from "react-icons/io";
import { IoArrowForward } from "react-icons/io5";
import { Text } from "@/components/ui/Typography";

import dynamic from "next/dynamic";
import DetailsModal from "./DetailsModal";
const DataTable = dynamic(() => import("react-data-table-component"), {
  ssr: false,
});
const DataTableExtensions = dynamic(
  () => import("react-data-table-component-extensions"),
  { ssr: false }
);

interface ReportsTableProps {
  data: any[]; // Replace 'any' with the actual type if possible
  report: any; // Replace 'any' with the actual type if possible
}

// const ReportsTable = ({ data, report }) => {

const ReportsTable: React.FC<ReportsTableProps> = ({ data, report }) => {
  const [loading, setLoading] = useState(true);

  const [showModal, setShowModal] = useState(false);
  const [selectedRow, setSelectedRow] = useState(null);

  const openModal = (row: any) => {
    setSelectedRow(row);
    setShowModal(true);
  };

  const closeModal = () => {
    setShowModal(false);
    setSelectedRow(null);
  };

  useEffect(() => {
    console.log("ReportsTable received data:", data);
  }, [data]);

  const columns = [
    {
      name: "State",
      selector: (row: any) => row.state_name,
      sortable: true,
    },
    {
      name: "LGA",
      selector: (row: any) => row.lga_name,
      sortable: true,
    },
    {
      name: "Ward",
      selector: (row: any) => row.ward_name ?? "N/A",
      sortable: true,
    },
    {
      name: "Facility Code",
      selector: (row: any) => row.facility_name,
      sortable: true,
      wrap: true, // ✅ Ensures wrapping
      grow: 2, // ✅ Increases width to take more space
    },
    {
      name: "Facility Name",
      selector: (row: any) => row.facility_name,
      sortable: true,
      wrap: true, // ✅ Ensures wrapping
      grow: 2, // ✅ Increases width to take more space
    },
    {
      name: "Facility Level",
      selector: (row: any) => row.facility_level_name,
      sortable: true,
      wrap: true, // ✅ Ensures wrapping
      grow: 2, // ✅ Increases width to take more space
      cell: (row: any) => (
        <span
          className={`px-2 py-1 rounded-sm text-white text-sm font- ${
            row.facility_level_name === "Primary"
              ? "bg-green-500"
              : row.facility_level_name === "Secondary"
              ? "bg-blue-500"
              : row.facility_level_name === "Tertiary"
              ? "bg-gray-500"
              : "bg-red-500"
          }`}
        >
          {row.facility_level_name}
        </span>
      ),
    },
    {
      name: "Ownership",
      selector: (row: any) => row.ownership_name,
      sortable: true,
      cell: (row: any) => (
        <span
          className={`px-2 py-1 rounded-sm text-white text-sm font- ${
            row.ownership_name === "Public"
              ? "bg-indigo-600"
              : row.ownership_name === "Private"
              ? "bg-cyan-500"
              : "bg-red-500"
          }`}
        >
          {row.ownership_name}
        </span>
      ),
    },

    {
      name: "Details",
      cell: (row: any) => (
        <span
          onClick={() => openModal(row)}
          className="text-green-600 underline cursor-pointer"
        >
          Details
        </span>
      ),
    },
  ];

  const customStyles = {
    headRow: {
      style: {
        backgroundColor: "#E8F0E2", // Header background color
        color: "#333", // Text color
        fontSize: "14px", // Header font size
        fontWeight: "bold", // Bold header text
      },
    },
    headCells: {
      style: {
        fontSize: "14px", // Column header font size
        fontWeight: "bold",
      },
    },
    rows: {
      style: {
        fontSize: "14px", // Row data font size
      },
    },
    cells: {
      style: {
        fontSize: "14px",
        whiteSpace: "normal", // ✅ Allow text to wrap
        overflow: "visible", // ✅ Prevent truncation
      },
    },
  };

  const tableData = {
    columns,
    data,
  };

  return (
    <div className="w-full overflow-x-auto">
      <div className="min-w-full">
        <DataTable
          highlightOnHover
          columns={columns}
          customStyles={customStyles}
          striped
          data={data}
          pagination
          // paginationServer
          paginationPerPage={15}
          paginationComponentOptions={{
            noRowsPerPage: true,
          }}
          responsive
          noDataComponent={
            <div className="text-gray-500 text-center py-6">
              Please select a report to view data.
            </div>
          }
          persistTableHead
        />

        {showModal && selectedRow && (
          <DetailsModal row={selectedRow} onClose={closeModal} />
        )}
      </div>
    </div>
  );
};

export default ReportsTable;

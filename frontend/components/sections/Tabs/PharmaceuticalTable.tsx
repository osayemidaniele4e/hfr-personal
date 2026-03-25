import { useEffect, useState } from "react";
// import { IoMdArrowDown, IoMdArrowBack, IoArrowForward } from "react-icons/io";
import { IoMdArrowDown, IoMdArrowBack, IoMdArrowForward } from "react-icons/io";

import dynamic from "next/dynamic";
import DetailsModal2 from "./DetailsModal2";
import { useSearchParams } from "next/navigation";
const DataTable = dynamic(() => import("react-data-table-component"), {
  ssr: false,
});

const DataTableExtensions = dynamic(
  () => Promise.resolve(require("react-data-table-component-extensions")),
  { ssr: false }
);

const PharmaceuticalTable: React.FC<{
  data: any[];
  currentPage: number;
  setCurrentPage: (page: number) => void;
  totalPages: number;
  fetchFacilities: () => void;
}> = ({ data, currentPage, setCurrentPage, totalPages, fetchFacilities }) => {
  const [showModal, setShowModal] = useState(false);
  const [selectedRow, setSelectedRow] = useState(null);

  const [loading, setLoading] = useState(true);

  const searchParams = useSearchParams();
  const [isVerified, setIsVerified] = useState(false);

  const openModal = (row: any) => {
    setSelectedRow(row);
    setShowModal(true);
  };

  const closeModal = () => {
    setShowModal(false);
    setSelectedRow(null);
  };

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
      name: "Facility Name",
      selector: (row: any) => row.facility_name,
      sortable: true,
    },

    {
      name: "Ownership",
      selector: (row: any) => row.ownership_name,
      sortable: true,
      cell: (row: any) => (
        <span
          className={`px-2 py-1 rounded-md text-white text-sm font-semibold ${
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
        fontSize: "18px", // Header font size
        fontWeight: "bold", // Bold header text
      },
    },
    headCells: {
      style: {
        fontSize: "18px", // Column header font size
        fontWeight: "bold",
      },
    },
    rows: {
      style: {
        fontSize: "16px", // Row data font size
      },
    },
    cells: {
      style: {
        fontSize: "16px", // Individual cell font size
      },
    },
  };

  const tableData = {
    columns,
    data,
  };

  const exportToCSV = (data: any[], filename: string) => {
    if (!data.length) return;

    const csvRows = [];

    // Extract headers
    const headers = Object.keys(data[0]);
    csvRows.push(headers.join(","));

    // Extract rows
    for (const row of data) {
      const values = headers.map((header) => JSON.stringify(row[header] ?? ""));
      csvRows.push(values.join(","));
    }

    const csvData = new Blob([csvRows.join("\n")], { type: "text/csv" });
    const url = window.URL.createObjectURL(csvData);

    const link = document.createElement("a");
    link.href = url;
    link.download = `${filename}.csv`;
    link.click();

    window.URL.revokeObjectURL(url);
  };

  useEffect(() => {
    const verified = searchParams.get("verified");

    if (verified === "true") {
      const now = new Date().toISOString();
      localStorage.setItem("verified", "true");
      localStorage.setItem("verified_at", now);
      setIsVerified(true);

      // Remove query param
      window.history.replaceState({}, document.title, window.location.pathname);
    } else {
      const verifiedFlag = localStorage.getItem("verified");
      const verifiedAt = localStorage.getItem("verified_at");

      if (verifiedFlag === "true" && verifiedAt) {
        const now = new Date();
        const savedTime = new Date(verifiedAt);
        const diffInMs = now.getTime() - savedTime.getTime();
        const diffInHours = diffInMs / (1000 * 60 * 60);
        // const diffInHours = diffInMs / (1000 * 60);

        if (diffInHours < 24) {
          setIsVerified(true);
        } else {
          // Expired: clear it
          localStorage.removeItem("verified");
          localStorage.removeItem("verified_at");
        }
      }
    }
  }, [searchParams]);

  return (
    <>
      <div className="mt-6">
        {/* <DataTableExtensions
        {...tableData}
        export={false}
        print={false}
        filter={true}
        filterPlaceholder="Search Facilities name"
      > */}
        <DataTable
          highlightOnHover
          columns={columns}
          customStyles={customStyles}
          striped
          data={data}
          pagination
          paginationServer
          paginationTotalRows={totalPages * 15} // Laravel sends per_page: 10
          paginationPerPage={15}
          // paginationPerPage={10}
          paginationComponentOptions={{
            noRowsPerPage: true,
          }}
          onChangePage={(page) => setCurrentPage(page)}
        />
        {/* </DataTableExtensions> */}

        {showModal && selectedRow && (
          <DetailsModal2 row={selectedRow} onClose={closeModal} />
        )}
      </div>
      <hr className="border-[#f1f1f1] mt-5" />

      {isVerified && (
        <div className="flex justify-center pt-5 mb-4 text-center">
          <button
            onClick={() => exportToCSV(data, "hospital_report")}
            className="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
            disabled={!isVerified}
          >
            Download CSV
          </button>
        </div>
      )}
    </>
  );
};

export default PharmaceuticalTable;

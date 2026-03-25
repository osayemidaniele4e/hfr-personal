import React from "react";

// const DetailsModal = ({ row, onClose }) => {

const DetailsModal1: React.FC<{ row: any; onClose: () => void }> = ({
  row,
  onClose,
}) => {
  if (!row) return null; // Prevent rendering if no data

  return (
    <div className="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
      <div className="bg-white rounded-lg shadow-lg w-[90%] max-w-4xl p-6">
        {/* Modal Header */}
        <div className="flex justify-between items-center border-b pb-3">
          <h3 className="text-2xl font-bold">
            Laboratories Details
            {/*  */}
          </h3>
          <button
            onClick={onClose}
            className="text-gray-500 hover:text-gray-800 text-3xl"
          >
            ✕
          </button>
        </div>

        {/* Modal Body */}
        <div className="grid grid-cols-4 gap-x-8 gap-y-6">
          <div className="p-">
            <p className="font-bold text-gray-700 pb-3">Facility Name:</p>
            <p className="text-lg">{row.facility_name}</p>
          </div>

          <div>
            <p className="font-semibold text-gray-700 pb-3">Facility Code:</p>
            <p className="text-lg">{row.unique_id}</p>
          </div>

          <div className="p-2">
            <p className="font-semibold text-gray-700 pb-3">Registration No:</p>
            <p className="text-lg">{row.registration_no ?? "N/A"}</p>
          </div>

          <div className="p-2">
            <p className="font-semibold text-gray-700 pb-3">State:</p>
            <p className="text-lg">{row.state_name}</p>
          </div>

          <div className="p-2">
            <p className="font-semibold text-gray-700 pb-3">LGA:</p>
            <p className="text-lg">{row.lga_name}</p>
          </div>

          <div className="p-2">
            <p className="font-semibold text-gray-700 pb-3">Ward:</p>
            <p className="text-lg">{row.ward_name ?? "N/A"}</p>
          </div>

          <div className="p-2">
            <p className="font-semibold text-gray-700 pb-3">Facility Level:</p>
            <span
              className={`px-2 py- rounded-sm text-white font-semibold text-sm inline-block ${
                row.facility_level_name === "Primary"
                  ? "bg-green-500"
                  : row.facility_level_name === "Secondary"
                  ? "bg-blue-500"
                  : row.facility_level_name === "Tertiary"
                  ? "bg-red-500"
                  : "bg-gray-500"
              }`}
            >
              {row.facility_level_name}
            </span>
          </div>

          <div className="p-2">
            <p className="font-semibold text-gray-700">Ownership:</p>
            <span
              className={`px-2 py- rounded-sm text-white font-semibold text-sm inline-block ${
                row.ownership_name === "Public"
                  ? "bg-green-600"
                  : row.ownership_name === "Private"
                  ? "bg-red-500"
                  : "bg-gray-500"
              }`}
            >
              {row.ownership_name}
            </span>
          </div>

          {/* Add more fields here if needed */}
        </div>
        <br />
        {/* Modal Footer */}
        <div className="flex justify-end border-t pt-4">
          <button
            onClick={onClose}
            className="px-6 py-3 bg-red-500 text-white rounded-lg text-sm"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  );
};

export default DetailsModal1;

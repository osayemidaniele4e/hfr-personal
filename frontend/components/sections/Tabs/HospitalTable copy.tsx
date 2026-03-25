"use client";
import React, { useState } from "react";
import { IoMdArrowBack, IoMdArrowDown } from "react-icons/io";
import { IoArrowForward } from "react-icons/io5";

const data = [
  {
    State: "Lagos",
    LGA: "Ikeja",
    Ward: "GRA",
    "Facility Code": "02/03/4/5/6/0002",
    "Facility Name": "Mercy Clinic",
    "Facility Level": "Primary",
    Ownership: "Public",
    Details: "View details",
  },
  {
    State: "Kano",
    LGA: "Nassarawa",
    Ward: "Sabon Gari",
    "Facility Code": "03/05/2/1/7/0003",
    "Facility Name": "Alheri Hospital",
    "Facility Level": "Secondary",
    Ownership: "Private",
    Details: "View details",
  },
  {
    State: "Rivers",
    LGA: "Obio-Akpor",
    Ward: "Rumuokoro",
    "Facility Code": "04/07/3/2/8/0004",
    "Facility Name": "Hope Medical Center",
    "Facility Level": "Tertiary",
    Ownership: "Public",
    Details: "View details",
  },
];

const HospitalTable = () => {
  const [currentPage, setCurrentPage] = useState(1);
  const itemsPerPage = 10;
  const paginatedUsers = data.slice(
    (currentPage - 1) * itemsPerPage,
    currentPage * itemsPerPage
  );
  const totalPages = Math.ceil(data.length / itemsPerPage);

  return (
    <div className="w-full overflow-x-auto p-2">
      <div className="mb-4">
        <div className="overflow-x-auto w-full">
          <table className="table w-full min-w-[800px] border border-gray-200">
            <thead className="bg-gray-100">
              <tr>
                <th className="p-3 text-gray-600">State</th>
                <th className="p-3 text-gray-600">
                  LGA <IoMdArrowDown className="inline ml-1" />
                </th>
                <th className="p-3 text-gray-600">
                  Ward <IoMdArrowDown className="inline ml-1" />
                </th>
                <th className="p-3 text-gray-600">
                  Facility Code <IoMdArrowDown className="inline ml-1" />
                </th>
                <th className="p-3 text-gray-600">
                  Facility Name <IoMdArrowDown className="inline ml-1" />
                </th>
                <th className="p-3 text-gray-600">
                  Facility Level <IoMdArrowDown className="inline ml-1" />
                </th>
                <th className="p-3 text-gray-600">
                  Ownership <IoMdArrowDown className="inline ml-1" />
                </th>
                <th className="p-3 text-gray-600">Details</th>
              </tr>
            </thead>
            <tbody>
              {paginatedUsers.map((user, index) => (
                <tr key={index} className="border-b">
                  <td className="p-3 text-gray-500 text-sm">{user.State}</td>
                  <td className="p-3 text-sm">{user.LGA}</td>
                  <td className="p-3 text-gray-500 text-sm">{user.Ward}</td>
                  <td className="p-3 text-gray-500 text-sm">
                    {user["Facility Code"]}
                  </td>
                  <td className="p-3 text-gray-500 text-sm">
                    {user["Facility Name"]}
                  </td>
                  <td className="p-3 text-gray-500 text-sm">
                    {user["Facility Level"]}
                  </td>
                  <td className="p-3 text-gray-500 text-sm">
                    {user.Ownership}
                  </td>
                  <td className="p-3 text-[#5BBA62] text-sm underline cursor-pointer">
                    View Details
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>

        {totalPages > 1 && (
          <div className="flex justify-center items-center gap-4 mt-4">
            <button
              disabled={currentPage === 1}
              onClick={() => setCurrentPage((prev) => prev - 1)}
              className="p-2 bg-gray-300 rounded disabled:opacity-50"
            >
              <IoMdArrowBack />
            </button>
            <span className="text-gray-600">{`${currentPage} of ${totalPages}`}</span>
            <button
              disabled={currentPage === totalPages}
              onClick={() => setCurrentPage((prev) => prev + 1)}
              className="p-2 bg-gray-300 rounded disabled:opacity-50"
            >
              <IoArrowForward />
            </button>
          </div>
        )}
      </div>
    </div>
  );
};

export default HospitalTable;

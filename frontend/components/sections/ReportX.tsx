"use client";

import React, { useState } from "react";
import axios from "axios";
import SelectComponent from "../ui/SelectComponent";
import { GrUploadOption } from "react-icons/gr";
import ReportsTable from "./Tabs/ReportsTable";

import { ToastContainer, toast } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";

const options = [
  { value: "1", name: "New Facilities Created This Month" },
  { value: "2", name: "New Facilities Created Last Month" },
  { value: "3", name: "New Facilities Created Last 3 Months" },
  { value: "4", name: "Facilities Updated This Month" },
  { value: "5", name: "Facilities Updated Last Month" },
  { value: "6", name: "Facilities Updated Last 3 Months" },
];

const ReportX = () => {
  const [selectedReport, setSelectedReport] = useState("");
  const [report, setReport] = useState("");

  // const [reportData, setReportData] = useState(null);
  const [reportData, setReportData] = useState<any[]>([]);

  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");

  const fetchReportData = async () => {
    if (!selectedReport) {
      toast.warning("Please select a report.");
      return;
    }

    setLoading(true);
    setError("");

    try {
      const response = await axios.get(
        `${process.env.NEXT_PUBLIC_BACKEND_API}/facilities-latest-updates?report=${selectedReport}`
      );

      setReportData(response.data?.data?.facilities);
      setReport(response.data?.data?.report);
    } catch (err) {
      setError("Error fetching report data.");
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="w-full p-4 sm:p-6">
      <div className="flex flex-wrap lg:flex-nowrap items-center justify-between gap-4 max-w-7xl mx-auto w-full mb-2">
        {/* Select & Search Button */}
        <div className="flex flex-col md:flex-row items-center gap-2 w-full md:w-1/2 lg:w-auto">
          <SelectComponent
            options={options}
            className="w-full sm:w-[300px] lg:w-[600px]"
            placeholder="Select Report"
            onChange={(e) => setSelectedReport(e.target.value)}
          />
          <button
            className="bg-green-500 hover:bg-green-600 text-white rounded-lg p-3 w-full sm:w-[120px] lg:w-[150px]"
            onClick={fetchReportData}
          >
            {loading ? "Loading..." : "Show"}
          </button>
        </div>

        {/* Upload Button */}
        {/* <button className="w-full sm:w-[200px] border shadow-sm p-2 sm:p-3 rounded-lg flex items-center justify-center gap-2">
          <GrUploadOption size={16} />
          <span>Upload data</span>
        </button> */}
      </div>

      <hr className="border-[#f1f1f1] mt-5" />

      <div className="flex flex-col gap-4">
        {/* Error Message */}
        {error && (
          <div className="bg-red-500 text-white p-3 rounded-lg text-center">
            {error}
          </div>
        )}

        {/* Report Message */}
        {report && (
          <div className="bg-[#E8F0E2] p-3 mt-4 rounded-lg text-cente">
            <p>{report}.</p>
          </div>
        )}

        {/* Table Wrapper for Responsiveness */}
        <div className="grid overflow-x-auto">
          <div className="min-w-full min-w-[500px] sm:min-w-[700px] md:min-w-[1200] lg:min-w-900px]">
            <ReportsTable data={reportData || []} report={report} />
          </div>
        </div>
      </div>
    </div>
  );
};

export default ReportX;

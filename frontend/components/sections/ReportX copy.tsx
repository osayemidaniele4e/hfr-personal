"use client";

import React, { useState } from "react";
import axios from "axios";
import SelectComponent from "../ui/SelectComponent";
import { GrUploadOption } from "react-icons/gr";
import ReportsTable from "./Tabs/ReportsTable";

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

  const [reportData, setReportData] = useState(null);

  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");

  const fetchReportData = async () => {
    if (!selectedReport) {
      alert("Please select a report.");
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

  // console.log("reportData", reportData);

  return (
    <div className="w-full p-4 sm:p-6">
      <div className="flex flex-col sm:flex-row items-center justify-between mb-4 gap-4 sm:gap-0">
        <div className="flex flex-col sm:flex-row items-center w-full sm:w-auto space-y-2 sm:space-y-0 sm:space-x-2">
          <SelectComponent
            // options={options}
            className="w-full sm:w-[300px] lg:w-[400px]"
            placeholder="Select Report"
            onChange={(e) => setSelectedReport(e.target.value)}
          />
          <button
            className="bg-green-500 hover:bg-green-600 text-white rounded-lg p-2 w-full sm:w-auto"
            onClick={fetchReportData}
          >
            {loading ? "Loading..." : "Show"}
          </button>
        </div>
        <button className="w-full sm:w-[200px] border shadow-sm p-2 sm:p-3 rounded-lg flex items-center justify-center gap-2">
          <GrUploadOption size={16} />
          <span>Upload data</span>
        </button>
      </div>
      <hr className="border-[#f1f1f1]" />

      <div className="flex flex-col gap-4">
        {error && (
          <div className="bg-red-500 text-white p-3 rounded-lg">{error}</div>
        )}

        {report && (
          <div className="bg-[#E8F0E2] p-4 mt-4 rounded-lg">
            <p>{report}</p>
          </div>
        )}

        <ReportsTable data={reportData ? reportData : []} report={report} />
      </div>
    </div>
  );
};

export default ReportX;

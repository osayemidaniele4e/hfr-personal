"use client";

import { Text } from "@/components/ui/Typography";
import React, { useState } from "react";
import TableCare from "./TableCare";
import TableOwnership from "./TableOwnership";

const Table = () => {
  const [analysis, setAnalysis] = useState<boolean>(true);
  const [comparison, setComparison] = useState<boolean>(false);
  return (
    <div>
      <div className="w-full flex lg:justify-center items-start gap-[1rem] mt-6 p-4 border rounded-lg border-[#D0D0D0]">
        <div
          className={`cursor-pointer ${
            analysis ? "border-b-4 border-b-[#5BBA62] pb-3" : ""
          }`}
          onClick={() => {
            setAnalysis(!analysis);
            setComparison(false);
          }}
        >
          Analysis
        </div>
        <div
          className={`cursor-pointer ${
            comparison ? "border-b-4 border-b-[#5BBA62] pb-3" : ""
          }`}
          onClick={() => {
            setComparison(!comparison);
            setAnalysis(false);
          }}
        >
          Comparison
        </div>
      </div>

      {analysis && (
        <div>
          <div className="flex flex-col lg:flex-row gap-[1rem] lg:gap-[3rem] justify-center mt-[2rem]">
            <TableCare />
            <TableOwnership />
          </div>
        </div>
      )}
      {comparison && (
        <div>
          <div className="flex flex-col lg:flex-row gap-[1rem] lg:gap-[3rem] justify-center mt-[2rem]">
            <TableCare />
            <TableOwnership />
          </div>
        </div>
      )}
    </div>
  );
};

export default Table;

"use client";

import React from "react";
import {
  Chart as ChartJS,
  ArcElement,
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend,
} from "chart.js";
import { Pie, Bar } from "react-chartjs-2";
import SectionContainer from "@/components/ui/SectionContainer";
import { HiMiniBars4 } from "react-icons/hi2";

ChartJS.register(
  ArcElement,
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend
);

const Charts = () => {
  const minutesMentoredData = {
    labels: [
      "Aug 01",
      "Aug 05",
      "Aug 10",
      "Aug 15",
      "Aug 20",
      "Aug 25",
      "Aug 31",
    ],
    datasets: [
      {
        label: "Minutes Mentored",
        data: [100000, 75000, 77456, 90000, 80000, 87000, 92000],
        backgroundColor: [
          "#FF6384",
          "#36A2EB",
          "#FFCE56",
          "#4BC0C0",
          "#9966FF",
          "#FF9F40",
          "#FF6384",
        ],
        borderWidth: 1,
      },
    ],
  };

  const options = {
    responsive: true,
    plugins: {
      legend: {
        position: "top" as const,
        align: "center" as const,
      },
      tooltip: {
        mode: "index" as const,
        intersect: false,
      },
    },
  };

  return (
    <div className="w-full">
      <SectionContainer>
        <div className="pt-6 bg-white">
          <div className="flex  flex-col md:flex-row gap-3">
            <div className="mb-10 w-full md:w-[600px] h-auto md:h-[450px] border shadow-lg rounded-lg">
              <div className="flex justify-between items-start bg-[#E8F0E2] p-4">
                <h2 className="text-sm font-semibold mb-4 text-wrap lg:text-nowrap">
                  Percentage of Hospitals and Clinics by Level of Care
                </h2>
                <HiMiniBars4 fontSize={24} />
              </div>
              <div className="flex justify-center items-center mt-8">
                <div style={{ width: "300px", height: "300px" }}>
                  <Pie data={minutesMentoredData} options={options} />
                </div>
              </div>
            </div>

            <div className="mb-10 w-full md:w-[600px] h-auto md:h-[450px] border shadow-lg rounded-lg">
              <div className="flex justify-between items-start bg-[#E8F0E2] p-4">
                <h2 className="text-sm font-semibold mb-4 text-wrap lg:text-nowrap">
                  Percentage of Hospitals and Clinics by Level of Care
                </h2>
                <HiMiniBars4 fontSize={24} />
              </div>
              <div className="flex justify-center items-center mt-8">
                <div style={{ width: "300px", height: "300px" }}>
                  <Pie data={minutesMentoredData} options={options} />
                </div>
              </div>
            </div>
          </div>

          <div className="mb-10 w-full md:w-[1200px] h-auto md:h-[450px] border shadow-lg rounded-lg">
            <div className="flex justify-between items-start bg-[#E8F0E2] p-4">
              <h2 className="text-sm font-semibold mb-4 text-wrap lg:text-nowrap">
                Percentage of Hospitals and Clinics by Level of Care
              </h2>
              <HiMiniBars4 fontSize={24} />
            </div>
            <div className="flex justify-center items-center mt-8">
              <div style={{ width: "500px", height: "500px" }}>
                <Bar data={minutesMentoredData} options={options} />
              </div>
            </div>
          </div>
        </div>
      </SectionContainer>
    </div>
  );
};

export default Charts;

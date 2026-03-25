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
  const data = {
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
    maintainAspectRatio: false,
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
    <div className="w-full px-4  outline outline-1 outline-red-500">
      {/* No padding here, handle in SectionContainer only */}
      <SectionContainer>
      <div className="flex flex-wrap justify-center gap-4  outline outline-1 outline-red-500">
        {[1, 2].map((_, i) => (
          <div
            key={i}
            className="
            w-full
            sm:w-[90%]
            md:w-[47%]
            max-w-full
            border shadow-lg rounded-lg flex flex-col
            aspect-[4/3] sm:aspect-[16/9]
             outline outline-1 outline-red-500
          "
          >
            <div className="flex justify-between items-center bg-[#E8F0E2] p-3 rounded-t-lg  outline outline-1 outline-red-500">
              <h2 className="text-xs sm:text-sm font-semibold">
                Percentage of Hospitals and Clinics by Level of Care
              </h2>
              <HiMiniBars4 fontSize={20} />
            </div>
            <div className="relative flex-grow p-3 w-full h-full overflow-hidden  outline outline-1 outline-red-500">
              <Pie
                data={data}
                options={{ ...options, maintainAspectRatio: false }}
              />
            </div>
          </div>
        ))}
      </div>
      
        <div className="w-full max-w-[900px] border shadow-lg rounded-lg flex flex-col mt-4 aspect-[16/9] mx-auto px-4">
          <div className="flex justify-between items-center bg-[#E8F0E2] p-3 rounded-t-lg">
            <h2 className="text-xs sm:text-sm font-semibold">
              Percentage of Hospitals and Clinics by Level of Care
            </h2>
            <HiMiniBars4 fontSize={20} />
          </div>
          <div className="relative flex-grow p-3 w-full h-full">
            <Bar
              data={data}
              options={{ ...options, maintainAspectRatio: false }}
            />
          </div>
        </div>
      </SectionContainer>
    </div>
  );
};

export default Charts;

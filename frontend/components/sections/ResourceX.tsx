"use client";

import { FileText } from "lucide-react";
import { GreenButton } from "../ui/Typography";
import { useCallback, useEffect, useState } from "react";
import axios from "axios";

const resources = [
  "M&E Framework for MFL and HFR in Nigeria",
  "M&E Framework for MFL and HFR in Nigeria",
  "M&E Framework for MFL and HFR in Nigeria",
  "M&E Framework for MFL and HFR in Nigeria",
];

type Resource = {
  description: string;
  filename: string;
};

export default function ResourceX() {
  // const [resources, setResources] = useState<string[]>([]);
  const [resources, setResources] = useState<Resource[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [error, setError] = useState<string | null>(null);
  const [selectedResource, setSelectedResource] = useState("");

  const fetchResource = useCallback(async () => {
    try {
      setResources([]); // Reset LGAs
      setSelectedResource(""); // Reset selected LGA

      const response = await axios.get(
        `${process.env.NEXT_PUBLIC_BACKEND_API}/resources`
      );

      const data = response?.data?.data;

      // console.log("data", data);

      if (data && Array.isArray(data)) {
        setResources(data);
      }
      setLoading(false);
    } catch (error) {
      console.error("Error fetching LGAs:", error);
      setLoading(false);
      // setFetchError("Failed to fetch LGAs.");
    }
  }, []);

  useEffect(() => {
    fetchResource();
  }, [fetchResource]);

  const handleDownload = (url: string) => {
    window.open(url, "_blank");
  };

  return (
    <div className="max-w-5xl mx-auto p-6">
      <h2 className="text-2xl font-bold">Resources</h2>
      <p className="text-gray-600 mt-2">
        Access to downloadable reports, guidelines, and FAQs to improve
        transparency.
      </p>

      <div className="bg-green-100 text-green-800 p-3 rounded-md mt-4 font-medium">
        Public Resources
      </div>

      {loading ? (
        // <p className="text-center mt-4">Loading resources...</p>
        <div className="flex justify-center mt-4">
          <div className="w-8 h-8 border-4 border-green-500 border-t-transparent rounded-full animate-spin"></div>
          &nbsp; Loading resources
        </div>
      ) : error ? (
        <p className="text-red-500 mt-4">{error}</p>
      ) : (
        <div className="mt-4 space-y-4">
          {resources.map((resource, index) => (
            <div
              key={index}
              className="flex items-center justify-between p-4 border rounded-lg shadow-sm"
            >
              <div className="flex items-center space-x-3 p-0">
                <FileText className="text-blue-500" size={24} />
                <span className="text-medium font-400">
                  {resource?.description}
                </span>
              </div>
              <GreenButton
                className="bg-green-500 hover:bg-green-600 text-white"
                onClick={() => handleDownload(resource?.filename)}
              >
                Download file
              </GreenButton>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}

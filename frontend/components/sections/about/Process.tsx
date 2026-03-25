"use client";

import { Text } from "@/components/ui/Typography";
import axios from "axios";
import React, { useEffect, useState } from "react";

const Process = () => {
  // State to store the fetched data and loading/error states
  const [processData, setProcessData] = useState<{
    title: string;
    content: string;
  } | null>(null);

  const [processItemData, setProcessItemData] = useState<any>(null);

  const [loading, setLoading] = useState<boolean>(true);
  const [error, setError] = useState<string>("");

  useEffect(() => {
    // Function to fetch data from the backend
    const fetchProcessData = async () => {
      try {
        const response = await axios.get(
          `${process.env.NEXT_PUBLIC_BACKEND_API}/process`
        );

        // console.log("Adams", response.data.data);

        // Assuming the API returns the title and content in the response data
        setProcessData(response.data.data);
      } catch (err) {
        setError("Failed to fetch data.");
      } finally {
        setLoading(false);
      }
    };

    fetchProcessData();
  }, []);

  useEffect(() => {
    // Function to fetch data from the backend
    const fetchProcessItemData = async () => {
      try {
        const response = await axios.get(
          `${process.env.NEXT_PUBLIC_BACKEND_API}/process-item`
        );

        console.log("Adams", response.data.data);

        // Assuming the API returns the title and content in the response data
        setProcessItemData(response.data.data);
      } catch (err) {
        setError("Failed to fetch data.");
      } finally {
        setLoading(false);
      }
    };

    fetchProcessItemData();
  }, []);

  // if (loading) return <Text>Loading...</Text>;
  // if (error) return <Text>{error}</Text>;

  // console.log("Adams 2", processData?.title);

  return (
    <div className="flex flex-col gap-[1.5rem] flex flex-col mt-[2rem] w-full lg:w-[1200px] px-8 lg:px-0 lg:mx-auto">
      <Text className="text-center font-semibold">{processData?.title}</Text>
      {/* <Text className="w-full">
        The development of the HFR followed a consultative process among the
        different stakeholders working within the Federal Ministry of Health,
        its agencies and development partners.
      </Text>
      <Text className="w-full">
        The steps of the process followed are listed below:
      </Text> */}

      <div
        className="content w-full"
        dangerouslySetInnerHTML={{ __html: processData?.content || "" }}
      />

      <ul className="list-disc flex flex-col gap-[1rem] w-full  lg:mx-[0]">
        {/* {processItemData?.map((item: string, index: number) => (
          <li key={index} className="w-full">
            {item?.title || ""}
          </li>
        ))} */}

        {processItemData?.map((item: { title: string }, index: number) => (
          <li key={index} className="w-full">
            {item.title}
          </li>
        ))}
      </ul>
    </div>
  );
};

export default Process;

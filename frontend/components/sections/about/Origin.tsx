"use client";

import { Text } from "@/components/ui/Typography";
import axios from "axios";
import React, { useEffect, useState } from "react";

const Origin = () => {
  // State to store the fetched data and loading/error states
  const [originData, setOriginData] = useState<{
    title: string;
    content: string;
  } | null>(null);
  const [loading, setLoading] = useState<boolean>(true);
  const [error, setError] = useState<string>("");

  useEffect(() => {
    // Function to fetch data from the backend
    const fetchOriginData = async () => {
      try {
        const response = await axios.get(
          `${process.env.NEXT_PUBLIC_BACKEND_API}/origin`
        );

        // Assuming the API returns the title and content in the response data
        setOriginData(response.data.data);
      } catch (err) {
        setError("Failed to fetch data.");
      } finally {
        setLoading(false);
      }
    };

    fetchOriginData();
  }, []);

  if (loading) return <Text>Loading...</Text>;
  if (error) return <Text>{error}</Text>;

  return (
    <div className="bg-[#F2F2F2] p-8 w-full ">
      <div className="lg:w-[1200px] mx-auto flex flex-col gap-[1.5rem]">
        {originData ? (
          <>
            <Text className="font-semibold text-center">
              {originData.title}
            </Text>
            {/* <Text dangerouslySetInnerHTML={{ __html: originData.content }} /> */}
            <div
              className="content"
              dangerouslySetInnerHTML={{ __html: originData.content }}
            />
          </>
        ) : (
          <Text>No data available.</Text>
        )}

        {/* <Text className="font-semibold text-center">The Origin</Text>
        <Text className="">
          The Nigeria Health Facility Registry (HFR) was developed in 2017 as
          part of effort to dynamically manage the Master Health Facility List
          (MFL) in the country. The MFL "is a complete listing of health
          facilities in a country (both public and private) and is comprised of
          a set of identification items for each facility (signature domain) and
          basic information on the service capacity of each facility (service
          domain)".
        </Text>
        <Text>
          The Federal Ministry of Health had previously identified the need for
          an information system to manage the MFL in light of different
          shortcomings encountered in maintaining an up-to-date paper based MFL.
          The benefits of the HFR are numerous including serving as the hub for
          connecting different information systems thereby enabling integration
          and interoperability, eliminating duplication of health facility lists
          and for planning the establishment of new health facilities.
          Elaboration on the use cases of importance for which the HFR should
          address was subsequently made.
        </Text> */}
      </div>
    </div>
  );
};

export default Origin;

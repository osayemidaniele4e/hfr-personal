"use client";

import React, { useState } from "react";
import {
  Tabs,
  TabList,
  TabPanels,
  Tab,
  TabPanel,
} from "@chakra-ui/react";
import HospitalTab from "./Tabs/HospitalTab";
import Pharmaceutical from "./Tabs/PharmaceuticalTab";
import LaboratoryTab from "./Tabs/LaboratoryTab";
import RadiologyTab from "./Tabs/RadiologyTab";

const ListOfFacilities = () => {
  return (
    <div>
      <UserTabs />
    </div>
  );
};

export default ListOfFacilities;

const TabsData = [
  "Hospitals and Clinics",
  "Pharmaceuticals",
  "Laboratories",
  "Radiology/Imaging",
];

const UserTabs = () => {
  const [value, setValue] = useState(0);
  const handleChange = (index: number) => setValue(index);

  return (
    <div className="w-full overflow-x-hidden">
      <Tabs
        variant="unstyled"
        index={value}
        onChange={handleChange}
        overflowX={"auto"}
      >
        <TabList
          display="flex"
          flexWrap="wrap"
          borderBottom="1px solid #D0D0D0"
          overflowX="auto"
          whiteSpace="nowrap"
          sx={{
            scrollbarWidth: "none",
            "-ms-overflow-style": "none",
            "&::-webkit-scrollbar": {
              display: "none",
            },
          }}
        >
          {TabsData.map((tab, index) => (
            <Tab
              key={index}
              _selected={{
                borderBottom: "2px solid #078586",
              }}
              color="gray.400"
              px={4}
              py={2}
              textAlign="center"
            >
              {tab}
            </Tab>
          ))}
        </TabList>

        <TabPanels className="w-full overflow-x-hidden max-w-[100vw] md:overflow-x-auto">
          <TabPanel>
            <div className="w-full overflow-x-auto">
              <HospitalTab />
            </div>
          </TabPanel>

          <TabPanel>
            <div className="w-full overflow-x-auto">
              <Pharmaceutical />
            </div>
          </TabPanel>

          <TabPanel>
            <div className="w-full overflow-x-auto">
              <LaboratoryTab />
            </div>
          </TabPanel>

          <TabPanel>
            <div className="w-full overflow-x-auto">
              <RadiologyTab />
            </div>
          </TabPanel>
        </TabPanels>
      </Tabs>
    </div>
  );
};

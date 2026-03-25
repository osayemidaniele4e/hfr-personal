import React, { useState } from "react";

interface Item {
  title: string;
  content: string;
}

type AccordionData = Item[];

const ReusableAccordion = ({
  items = [],
  bgColor = "bg-[#E8DFCA]",
  titleClassName = "text-xl font-medium",
  defaultOpenIndex = 0,
}: {
  items: AccordionData;
  bgColor?: string;
  titleClassName?: string;
  defaultOpenIndex?: number;
}) => {
  const [openIndex, setOpenIndex] = useState(defaultOpenIndex);

  return (
    <div className="space-y-2">
      {items.map((item, index) => (
        <div key={index} className={`collapse ${bgColor}`}>
          <input
            type="radio"
            name="accordion-group"
            checked={openIndex === index}
            onChange={() => setOpenIndex(index)}
          />
          <div
            className={`collapse-title ${titleClassName} border-b border-b-[#000] pb-2`}
          >
            {item?.title}
          </div>
          <div className="collapse-content mt-2">{item?.content}</div>
        </div>
      ))}
    </div>
  );
};

export default ReusableAccordion;

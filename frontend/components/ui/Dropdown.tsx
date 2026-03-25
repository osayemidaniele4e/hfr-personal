import Link from "next/link";
import React, { useState } from "react";
import { NavLinksType } from "@/data/NavLinks";
import { FaChevronDown, FaChevronUp } from "react-icons/fa";
import { usePathname } from "next/navigation";
const Dropdown = (items: NavLinksType) => {
  const pathname = usePathname();
  const isTalentPath = pathname === "/";
  const isWhyPath = pathname === "/contact";
  const [mobiledrop, setDrop] = useState(false);
  const handleDropdown = () => {
    setDrop(true);
  };
  return (
    <div className="w-full">
      <ul className="flex flex-col w-full 2xl:hidden">
        <Link href={items.link}>
          <div
            onMouseEnter={handleDropdown}
            onMouseLeave={() => setDrop(false)}
            className="py-4 lowercase w-full flex text-[#4F4F4F]   items-center justify-between"
          >
            <li className="text-[#4F4F4F]">{items.navitem}</li>
            <div>
              {!mobiledrop ? (
                <FaChevronDown fontSize={"1rem"} />
              ) : (
                <FaChevronUp fontSize={"1rem"} />
              )}
            </div>
          </div>
        </Link>
        {/*if items.submenus exist*/}
        {items.subMenus && (
          <div
            onMouseEnter={handleDropdown}
            onMouseLeave={() => setDrop(false)}
            className={
              !mobiledrop
                ? "hidden"
                : " flex transition flex-col gap-4 p-4 py-8 w-full bg-white text-[#4F4F4F] rounded-t-none rounded-xl"
            }
          >
            {items.subMenus?.map((i, index) => (
              <Link href={i.link} key={index}>
                {" "}
                <li className="hover:bg- text-sm ">{i.menu}</li>
              </Link>
            ))}
          </div>
        )}
      </ul>
      <ul className="hidden 2xl:flex">
        <Link href={items.link}>
          <li
            className={`py-3 lowercase flex gap-2 text-[#4F4F4F] rounded-full items-center px-4 justify-center ${
              isTalentPath || isWhyPath ? "bg-transparent" : "bg-[#fff]"
            }`}
            onMouseEnter={handleDropdown}
            onMouseLeave={() => setDrop(false)}
          >
            {items.navitem}{" "}
            {!mobiledrop ? (
              <FaChevronDown fontSize={"1rem"} />
            ) : (
              <FaChevronUp fontSize={"1rem"} />
            )}
          </li>
        </Link>
        {/*if items.submenus exist*/}
        {items.subMenus && (
          <div
            onMouseEnter={handleDropdown}
            onMouseLeave={() => setDrop(false)}
            className={
              !mobiledrop
                ? "hidden"
                : " ml-2  flex transition mt-10 flex-col w-[150px] gap-4 absolute p-4 py-6 shadow-xl bg-white rounded-t-none rounded-xl"
            }
          >
            {items.subMenus?.map((i) => (
              <Link href={i.link} key={i.menu}>
                {" "}
                <li className="hover:bg- text-sm ">{i.menu}</li>
              </Link>
            ))}
          </div>
        )}
      </ul>
    </div>
  );
};
export default Dropdown;

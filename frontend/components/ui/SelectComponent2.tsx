import { MdError } from "react-icons/md";
import React from "react";

const SelectComponent2 = ({
  label,
  options,
  type,
  value,
  onChange,
  icon,
  onClick,
  name,
  error,
  touched,
  fieldProps,
  defaultValue,
  placeholder,
  isMulti,
  className,
  created_at,
  updated_at,
}: {
  label?: string;
  defaultValue?: string;
  type?: string;
  options?: string[];
  value?: string | [];
  onChange?: (event: React.ChangeEvent<HTMLSelectElement>) => void;
  icon?: React.ReactNode;
  onClick?: () => void;
  name?: string;
  error?: string;
  touched?: boolean;
  fieldProps?: any;
  placeholder?: string;
  isMulti?: boolean;
  className?: string;
  created_at?: Date;
  updated_at?: Date;
}) => {
  const inputBorderColor = touched && error ? "input-error" : "";

  return (
    <div className={`flex w-[100%] flex-col gap-2  ${className}`}>
      {label && <p className="text-sm">{label}</p>}
      <label>
        <select
          className={`select select-bordered w-full rounded-[4px]  focus:outline-none ${inputBorderColor}`}
          name={name}
          defaultValue={defaultValue}
          type={type}
          onChange={onChange}
          value={value}
          {...fieldProps}
          multiple={isMulti}
          placeholder={placeholder}
        >
          <option value="">{placeholder || "Facility Level"}</option>
          {options?.map((option, index) => (
            <option key={index} value={option}>
              {/* {option.name} */}
            </option>
          ))}
        </select>
        {icon && <div onClick={onClick}>{icon}</div>}
      </label>
      {touched && error && (
        <div className="text-error flex gap-1 justify-left items-center">
          <MdError fontSize={"1rem"} />
          <p className="text-sm text-error">{error}</p>
        </div>
      )}
    </div>
  );
};

export default SelectComponent2;

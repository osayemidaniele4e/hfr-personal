import React from "react";
import { MdError } from "react-icons/md";

interface SelectComponentWithIconsProps {
  label?: string;
  defaultValue?: string;
  type?: string;
  options?: string[];
  value?: string | [];
  onChange?: (event: React.ChangeEvent<HTMLSelectElement>) => void;
  leftIcon?: React.ReactNode;
  onClick?: () => void;
  name?: string;
  error?: string;
  touched?: boolean;
  fieldProps?: any;
  placeholder?: string;
  className?: string;
  isMulti?: boolean;
}

const SelectComponentWithIcons: React.FC<SelectComponentWithIconsProps> = ({
  label,
  options,
  type,
  value,
  onChange,
  leftIcon,
  onClick,
  name,
  error,
  touched,
  fieldProps,
  defaultValue,
  placeholder,
  isMulti,
  className,
}) => {
  const inputBorderColor = touched && error ? "input-error" : "";

  return (
    <div className={`flex w-full flex-col gap-2 ${className}`}>
      {label && <p className="text-sm">{label}</p>}
      <label className="relative flex items-center">
        {leftIcon && (
          <span className="absolute left-2 z-10 text-gray-500">{leftIcon}</span>
        )}
        <select
          className={`select select-bordered w-full pl-10 rounded-[4px] focus:outline-none ${inputBorderColor}`}
          name={name}
          defaultValue={defaultValue}
          type={type}
          onChange={onChange}
          value={value}
          {...fieldProps}
          multiple={isMulti}
          placeholder={placeholder}
        >
          <option value="">{placeholder || "Select an option"}</option>
          {options?.map((option, index) => (
            <option key={index} value={option}>
              {option}
            </option>
          ))}
        </select>
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

export default SelectComponentWithIcons;

"use client";

import { MdError } from "react-icons/md";
import React from "react";

const TextArea = ({
  label,
  type,
  value,
  onChange,
  icon,
  onClick,
  name,
  error,
  touched,
  fieldProps,
  placeholder,
  defaultValue,
}: {
  label?: string;
  defaultValue?: string;
  type?: string;
  value?: string | [];
  onChange?: (event: React.ChangeEvent<HTMLSelectElement>) => void;
  icon?: React.ReactNode;
  onClick?: () => void;
  name?: string;
  error?: string;
  touched?: boolean;
  fieldProps?: any;
  placeholder?: string;
}) => {
  const inputBorderColor = touched && error ? "input-error" : "";

  return (
    <div className="flex w-full flex-col gap-2">
      {label && <p className="font-bold text-sm">{label}</p>}
      <label>
        <textarea
          rows={6}
          defaultValue={defaultValue}
          className={`textarea textarea-bordered leading-5 textarea-md w-full ${inputBorderColor}`}
          name={name}
          type={type}
          onChange={onChange}
          value={value}
          {...fieldProps}
          placeholder={placeholder}
        ></textarea>

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

export default TextArea;

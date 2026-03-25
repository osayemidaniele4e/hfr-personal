import { MdError } from "react-icons/md";
import React from "react";

const InputWithIcons = ({
  label,
  type,
  value,
  onChange,
  onChangeInput,
  leftIcon,
  rightIcon,
  onClickRightIcon,
  name,
  error,
  touched,
  fieldProps,
  placeholder,
  accept,
  disabled,
  defaultValue,
  className,
}: {
  name?: string;
  label?: string;
  type?: string;
  value?: string;
  onChange?: (event: React.ChangeEvent<HTMLSelectElement>) => void;
  onChangeInput?: (event: React.ChangeEvent<HTMLInputElement>) => void;
  leftIcon?: React.ReactNode;
  rightIcon?: React.ReactNode;
  onClickRightIcon?: () => void;
  error?: string;
  touched?: boolean;
  fieldProps?: any;
  placeholder?: string;
  accept?: string;
  disabled?: boolean;
  defaultValue?: string;
  className?: string;
}) => {
  const inputBorderColor = touched && error ? "input-error" : "";

  return (
    <div className={`flex  flex-col gap-2 ${className}`}>
      {label && <label className="font-bold text-sm">{label}</label>}
      <div
        className={`input input-md input-bordered rounded-[4px] flex items-center gap-2 ${inputBorderColor} ${className}`}
      >
        {leftIcon && <div className="flex items-center">{leftIcon}</div>}

        <input
          disabled={disabled}
          name={name}
          onChange={type === "text" ? onChangeInput : onChange}
          type={type}
          value={value}
          defaultValue={defaultValue}
          accept={accept}
          className="grow text-sm border-none focus:ring-0"
          {...fieldProps}
          placeholder={placeholder}
        />

        {rightIcon && (
          <div className="flex items-center" onClick={onClickRightIcon}>
            {rightIcon}
          </div>
        )}
      </div>

      {touched && error && (
        <div className="text-error flex gap-1 items-center">
          <MdError fontSize={"1rem"} />
          <p className="text-sm text-error">{error}</p>
        </div>
      )}
    </div>
  );
};

export default InputWithIcons;

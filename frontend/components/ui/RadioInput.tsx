import { MdError } from "react-icons/md";

const RadioInput = ({
  label,
  name,
  value,
  onChange,
  checked,
  error,
  touched,
  fieldProps,
  disabled,
  className,
}: {
  name: string;
  label: string;
  value: string;
  onChange: (event: React.ChangeEvent<HTMLInputElement>) => void;
  checked?: boolean;
  error?: string;
  touched?: boolean;
  fieldProps?: any;
  disabled?: boolean;
  className?: string;
}) => {
  const inputBorderColor =
    touched && error ? "border-error" : "border-gray-300";

  const inputId = `${name}-${value}`;

  return (
    <div
      className={`flex items-center gap-3 w-full p-4 border rounded-lg ${inputBorderColor} ${className}`}
    >
      <input
        type="radio"
        id={inputId}
        name={name}
        value={value}
        checked={checked}
        onChange={onChange}
        disabled={disabled}
        className="form-radio "
        {...fieldProps}
      />

      <label htmlFor={inputId} className="text-sm text-gray-700">
        {label}
      </label>

      {touched && error && (
        <div className="text-error flex gap-1 items-center mt-2">
          <MdError fontSize={"1rem"} />
          <p className="text-sm">{error}</p>
        </div>
      )}
    </div>
  );
};

export default RadioInput;

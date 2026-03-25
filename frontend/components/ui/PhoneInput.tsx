import React, { useState, useEffect } from "react";
import { MdError } from "react-icons/md";
import "react-phone-number-input/style.css";
import PhoneInput from "react-phone-number-input";
import flags from "react-phone-number-input/flags";

interface PhoneInputProps {
  label?: string;
  field: any;
  form: any;
  placeholder?: string;
  disabled?: boolean;
  touched?: boolean;
  error?: string;
}

const PhoneInputComponent: React.FC<PhoneInputProps> = ({
  label,
  field,
  form,
  placeholder = "Enter phone number",
  disabled = false,
  touched,
  error,
}) => {
  const [inputValue, setInputValue] = useState<string | undefined>(
    field.value || ""
  );

  const handleChange = (value?: string) => {
    setInputValue(value);
    form.setFieldValue(field.name, value || "");
  };

  useEffect(() => {
    setInputValue(field.value);
  }, [field.value]);

  return (
    <div className="flex flex-col w-full gap-2">
      {label && <label className="text-sm font-medium">{label}</label>}
      <div className="relative ">
        <PhoneInput
          className=" h-10 py-2 px-3 placeholder:text-muted-foreground bg-background rounded border focus-visible:shadow-[#E06E274D] w-full md:w-[500px] h-[50px] input input-bordered rounded-md p-2 text-sm "
          placeholder="Enter phone number"
          value={inputValue}
          onChange={handleChange}
          defaultCountry="NG"
          flags={flags}
          international
        />
      </div>
      {touched && error && (
        <div className="flex items-center text-red-500 text-sm mt-1">
          <MdError className="mr-1" />
          {error}
        </div>
      )}
    </div>
  );
};

export default PhoneInputComponent;

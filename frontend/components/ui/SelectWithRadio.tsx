import Select from "react-select";

export interface Option {
  label: string;
  value: string;
  description?: string;
}

const SelectWithRadio = ({
  options,
  placeholder,
  onChange,
  value,
}: {
  options: Option[];
  placeholder?: string;
  onChange: (selectedOption: Option | null) => void;
  value?: Option;
}) => {
  return (
    <Select
      options={options}
      placeholder={placeholder}
      onChange={onChange}
      value={value}
      formatOptionLabel={(option) => (
        <div>
          <span>{option.label}</span>
          {option.description && (
            <small style={{ color: "#888", display: "block" }}>
              {option.description}
            </small>
          )}
        </div>
      )}
    />
  );
};

export default SelectWithRadio;

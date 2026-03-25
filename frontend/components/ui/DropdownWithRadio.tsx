import { useState } from "react";

function DropdownWithRadioOptions({
  options,
  placeholder,
}: {
  options: string[];
  placeholder: string;
}) {
  const [isOpen, setIsOpen] = useState(false);
  const [selectedOption, setSelectedOption] = useState("");

  return (
    <div className="relative w-full">
      <div
        className="flex items-center justify-between p-4 border rounded-md cursor-pointer"
        onClick={() => setIsOpen(!isOpen)}
      >
        <span className="text-gray-600">{selectedOption || placeholder}</span>
        <svg
          className={`w-4 h-4 transition-transform ${
            isOpen ? "rotate-180" : ""
          }`}
          fill="currentColor"
          viewBox="0 0 20 20"
        >
          <path
            fillRule="evenodd"
            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
            clipRule="evenodd"
          />
        </svg>
      </div>

      {isOpen && (
        <div className="absolute z-10 w-full p-4 mt-2 bg-white border border-gray-300 rounded-md shadow-lg max-h-64 overflow-y-auto">
          {options.map((option, index) => (
            <label
              key={index}
              className="flex items-center mb-2 cursor-pointer"
            >
              <input
                type="radio"
                name="option"
                value={option}
                checked={selectedOption === option}
                onChange={(e) => {
                  setSelectedOption(e.target.value);
                  setIsOpen(false);
                }}
                className="form-radio text-blue-500 h-4 w-4"
              />
              <span className="ml-2 text-gray-700">{option}</span>
            </label>
          ))}
        </div>
      )}
    </div>
  );
}
export default DropdownWithRadioOptions;

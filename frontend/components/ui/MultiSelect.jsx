import { useEffect, useRef, useState } from "react";
import styles from "./select.module.css";

export function Select({ multiple, value, onChange, options }) {
  const [isOpen, setIsOpen] = useState(false);
  const [highlightedIndex, setHighlightedIndex] = useState(0);
  const containerRef = useRef(null);

  function clearOptions() {
    multiple ? onChange([]) : onChange(undefined);
  }

  // function selectOption(option) {
  //   if (multiple) {
  //     if (value.includes(option)) {
  //       onChange(value.filter((o) => o !== option));
  //     } else {
  //       onChange([...value, option]);
  //     }
  //   } else {
  //     if (option !== value) onChange(option);
  //   }
  // }


  const selectOption = useCallback(
    (option) => {
      if (multiple) {
        if (value.includes(option)) {
          onChange(value.filter((o) => o !== option));
        } else {
          onChange([...value, option]);
        }
      } else {
        if (option !== value) onChange(option);
      }
    },
    [multiple, value, onChange] // Dependencies that affect selectOption
  );

  function isOptionSelected(option) {
    return multiple ? value.includes(option) : option === value;
  }

  useEffect(() => {
    if (isOpen) setHighlightedIndex(0);
  }, [isOpen]);

  // useEffect(() => {
  //   const handler = (e) => {
  //     if (e.target != containerRef.current) return;
  //     switch (e.code) {
  //       case "Enter":
  //       case "Space":
  //         setIsOpen((prev) => !prev);
  //         if (isOpen) selectOption(options[highlightedIndex]);
  //         break;
  //       case "ArrowUp":
  //       case "ArrowDown": {
  //         if (!isOpen) {
  //           setIsOpen(true);
  //           break;
  //         }

  //         const newValue = highlightedIndex + (e.code === "ArrowDown" ? 1 : -1);
  //         if (newValue >= 0 && newValue < options.length) {
  //           setHighlightedIndex(newValue);
  //         }
  //         break;
  //       }
  //       case "Escape":
  //         setIsOpen(false);
  //         break;
  //     }
  //   };
  //   containerRef.current?.addEventListener("keydown", handler);

  //   return () => {
  //     containerRef.current?.removeEventListener("keydown", handler);
  //   };
  // }, [isOpen, highlightedIndex, options]);


  useEffect(() => {
    const container = containerRef.current; // Store ref value in a variable
  
    const handler = (e) => {
      if (e.target !== container) return;
      switch (e.code) {
        case "Enter":
        case "Space":
          setIsOpen((prev) => !prev);
          if (isOpen) selectOption(options[highlightedIndex]);
          break;
        case "ArrowUp":
        case "ArrowDown": {
          if (!isOpen) {
            setIsOpen(true);
            break;
          }
  
          const newValue = highlightedIndex + (e.code === "ArrowDown" ? 1 : -1);
          if (newValue >= 0 && newValue < options.length) {
            setHighlightedIndex(newValue);
          }
          break;
        }
        case "Escape":
          setIsOpen(false);
          break;
      }
    };
  
    container?.addEventListener("keydown", handler);
  
    return () => {
      container?.removeEventListener("keydown", handler);
    };
  }, [isOpen, highlightedIndex, options, selectOption]); // Added selectOption to dependencies

  
  return (
    <div
      ref={containerRef}
      onBlur={() => setIsOpen(false)}
      onClick={() => setIsOpen((prev) => !prev)}
      tabIndex={0}
      className={styles.container}
    >
      <span className={styles.value}>
        {multiple
          ? value.map((v) => (
              <button
                key={v.value}
                onClick={(e) => {
                  e.stopPropagation();
                  selectOption(v);
                }}
                className={styles["option-badge"]}
              >
                {v}
                <span className={styles["remove-btn"]}>&times;</span>
              </button>
            ))
          : value}
      </span>
      <button
        onClick={(e) => {
          e.stopPropagation();
          clearOptions();
        }}
        className={styles["clear-btn"]}
      >
        &times;
      </button>
      <div className={styles.divider}></div>
      <div className={styles.caret}></div>
      <ul className={`${styles.options} ${isOpen ? styles.show : ""}`}>
        {options.map((option, index) => (
          <li
            onClick={(e) => {
              e.stopPropagation();
              selectOption(option);
              setIsOpen(false);
            }}
            onMouseEnter={() => setHighlightedIndex(index)}
            key={option.value}
            className={`${styles.option} ${
              isOptionSelected(option) ? styles.selected : ""
            } ${index === highlightedIndex ? styles.highlighted : ""}`}
          >
            {!isOptionSelected(option) ? option : "No options"}
          </li>
        ))}
      </ul>
    </div>
  );
}

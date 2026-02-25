// ...existing code...
import React, { useState, useEffect, useRef, useMemo } from "react";

export default function AutocompleteInput({
  value,
  onChange,
  suggestions = [],
  placeholder = "",
  allowCustom = true,
  minLengthToShow = 1,
  displayKey = null, // Propiedad del objeto a mostrar (null si suggestions es array de strings)
  valueKey = null,   // Propiedad del objeto a devolver como valor (null usa displayKey o el string completo)
}) {
  const [inputValue, setInputValue] = useState(value ?? "");
  const [filtered, setFiltered] = useState([]);
  const [isOpen, setIsOpen] = useState(false);
  const [highlighted, setHighlighted] = useState(-1);
  const containerRef = useRef(null);
  const listIdRef = useRef(`ac-list-${Math.random().toString(36).slice(2, 9)}`);

  useEffect(() => setInputValue(value ?? ""), [value]);

  // Función helper para obtener el texto a mostrar de un item
  const getDisplayText = (item) => {
    if (!item) return "";
    return displayKey ? (item[displayKey] ?? "") : String(item);
  };

  // Función helper para obtener el valor de un item
  const getItemValue = (item) => {
    if (!item) return "";
    if (valueKey) return item[valueKey] ?? "";
    return getDisplayText(item);
  };

  // close on outside click
  useEffect(() => {
    function handleClickOutside(e) {
      if (containerRef.current && !containerRef.current.contains(e.target)) {
        setIsOpen(false);
        setHighlighted(-1);
      }
    }
    document.addEventListener("mousedown", handleClickOutside);
    return () => document.removeEventListener("mousedown", handleClickOutside);
  }, []);

  // compute filtered suggestions when suggestions or input change
  useEffect(() => {
    const text = inputValue ?? "";
    if (text.length >= minLengthToShow) {
      const lower = text.toLowerCase();
      const result = suggestions.filter((s) => {
        const displayText = getDisplayText(s);
        return displayText.toLowerCase().includes(lower);
      });
      setFiltered(result);
      setIsOpen(result.length > 0);
      setHighlighted(result.length > 0 ? 0 : -1);
    } else {
      setFiltered([]);
      setIsOpen(false);
      setHighlighted(-1);
    }
  }, [inputValue, suggestions, minLengthToShow]);

  function handleInputChange(e) {
    const text = e.target.value;
    setInputValue(text);
    onChange?.(text);
  }

  function selectValue(item) {
    const displayText = getDisplayText(item);
    const itemValue = getItemValue(item);
    setInputValue(displayText);
    onChange?.(itemValue);
    setIsOpen(false);
    setHighlighted(-1);
  }

  function handleKeyDown(e) {
    if (!isOpen && (e.key === "ArrowDown" || e.key === "ArrowUp")) {
      if (filtered.length) {
        setIsOpen(true);
        setHighlighted(0);
      }
      return;
    }

    if (e.key === "ArrowDown") {
      e.preventDefault();
      setHighlighted((h) => Math.min((filtered.length - 1), Math.max(0, h + 1)));
    } else if (e.key === "ArrowUp") {
      e.preventDefault();
      setHighlighted((h) => Math.min((filtered.length - 1), Math.max(0, h - 1)));
    } else if (e.key === "Enter") {
      if (isOpen && highlighted >= 0 && highlighted < filtered.length) {
        selectValue(filtered[highlighted]);
      } else if (allowCustom) {
        // accept current text as custom value
        setIsOpen(false);
        setHighlighted(-1);
        onChange?.(inputValue);
      }
    } else if (e.key === "Escape") {
      setIsOpen(false);
      setHighlighted(-1);
    }
  }

  return (
    <div
      ref={containerRef}
      style={{ position: "relative", display: "inline-block", width: "100%" }}
    >
      <input
        type="text"
        role="combobox"
        aria-expanded={isOpen}
        aria-controls={listIdRef.current}
        aria-autocomplete="list"
        aria-activedescendant={
          highlighted >= 0 ? `${listIdRef.current}-item-${highlighted}` : undefined
        }
        value={inputValue}
        onChange={handleInputChange}
        onFocus={() => { if (filtered.length) setIsOpen(true); }}
        onKeyDown={handleKeyDown}
        placeholder={placeholder}
        autoComplete="off"
        style={{ width: "100%", boxSizing: "border-box" }}
      />

      {isOpen && (
        <ul
          id={listIdRef.current}
          role="listbox"
          style={{
            position: "absolute",
            zIndex: 10,
            background: "white",
            border: "1px solid #ccc",
            borderRadius: 4,
            marginTop: 2,
            padding: 0,
            listStyle: "none",
            width: "100%",
            maxHeight: 200,
            overflowY: "auto",
          }}
        >
          {filtered.map((item, idx) => {
            const isHighlighted = idx === highlighted;
            const displayText = getDisplayText(item);
            return (
              <li
                id={`${listIdRef.current}-item-${idx}`}
                key={displayText + "|" + idx}
                role="option"
                aria-selected={isHighlighted}
                onClick={() => selectValue(item)}
                onMouseDown={(e) => e.preventDefault()} // avoid input blur before click
                onMouseEnter={() => setHighlighted(idx)}
                style={{
                  padding: "6px 8px",
                  cursor: "pointer",
                  background: isHighlighted ? "#eee" : "transparent",
                  borderBottom: "1px solid #eee",
                }}
              >
                {displayText}
              </li>
            );
          })}
        </ul>
      )}
    </div>
  );
}
// ...existing code...

import { createRef, useState, useEffect, useCallback } from "react";
import "./css/selectInput.css";

//Components
import CustomReactSelect from "./customReactSelect";

//Export
const SelectInput = (props) => {
  //Reference forwarding
  const customReactSelectRef = createRef();
  //State
  const [validation, setValidation] = useState("");
  const [style, setStyle] = useState({
    inputFocused: false,
    labelColour: "gainsboro",
  });

  //Hooks...
  //useCallback ensures the function is not redeclared every render (which would alter its reference).
  const validationCheck = useCallback(() => {
    if (props.required && !Object.keys(props.value).length) setValidation("Please fill out this field.");
  }, [props.value]);

  //Executes when validationCheck updates.
  useEffect(() => {
    document.getElementById([props.form]).addEventListener("submit", validationCheck);

    //Clears event listener before creating an updated one.
    return () => {
      if (document.getElementById([props.form])) {
        document.getElementById([props.form]).removeEventListener("submit", validationCheck);
      }
    };
  }, [validationCheck]);

  //Visually communicate disabled input.
  useEffect(() => {
    if (props.disabled) {
      setStyle((prevState) => {
        return { ...prevState, ...{ labelColour: "dimgrey" } };
      });
    } else {
      setStyle((prevState) => {
        return { ...prevState, ...{ labelColour: "gainsboro" } };
      });
    }
  }, [props.disabled]);

  //Form Events...
  const handleFocus = () => {
    setStyle((prevState) => {
      return { ...prevState, ...{ inputFocused: true } };
    });
  };

  const handleBlur = () => {
    setStyle((prevState) => {
      return { ...prevState, ...{ inputFocused: false } };
    });
  };

  const handleChange = (e) => {
    if (props.required) setValidation("");

    props.onChange("SelectInput", null, e);
  };

  //Render...
  return (
    <>
      <div className="inputBox">
        <div className="selectInputBox">
          <CustomReactSelect
            ref={customReactSelectRef}
            name={props.name}
            form={props.form}
            options={props.options}
            defaultOption={null}
            placeholder={"Search..."}
            isMulti={props.multiSelect}
            isSearchable={true}
            isClearable={!props.required}
            isLoading={false}
            isInvalid={validation ? true : false}
            isDisabled={props.disabled}
            isVisible={true}
            value={props.value}
            onFocus={handleFocus}
            onBlur={handleBlur}
            onChange={handleChange}
          />
        </div>
        <div className="inputFocusBorder" style={style.inputFocused ? { width: "100%" } : { width: "0%" }} />
      </div>
      <label className="inputFloatingLabel" style={{ top: "0", color: style.labelColour, fontSize: "12px" }}>
        {props.label}
      </label>
      {validation ? <pre className="inputValidation">{validation}</pre> : null}
    </>
  );
};

export default SelectInput;

import { forwardRef, useState, useEffect } from "react";
import "./textInput.css";

//Export
const TextInput = forwardRef((props, ref) => {
  //State
  const [validation, setValidation] = useState("");
  const [style, setStyle] = useState({
    inputBorderColour: "black",
    inputFocused: false,
    labelColour: "white",
    labelFloating: false,
  });

  //Hooks...
  useEffect(() => {
    if (props.value) {
      setStyle((prevState) => {
        return { ...prevState, ...{ labelFloating: true } };
      });
    }
  }, [props.value]);

  //Visually communicate disabled input.
  useEffect(() => {
    if (props.disabled) {
      setStyle((prevState) => {
        return { ...prevState, ...{ inputBorderColour: "dimgrey", labelColour: "dimgrey" } };
      });
    } else {
      //Highlight the input if it is responsible for the error response.
      if (props.error) {
        setStyle((prevState) => {
          return { ...prevState, ...{ inputBorderColour: "orangered", labelColour: "white" } };
        });
      } else {
        setStyle((prevState) => {
          return { ...prevState, ...{ inputBorderColour: "black", labelColour: "white" } };
        });
      }
    }
  }, [props.disabled]);

  //Form Events...
  const handleFocus = () => {
    setStyle((prevState) => {
      return { ...prevState, ...{ inputFocused: true, labelFloating: true } };
    });
  };

  const handleBlur = () => {
    if (!props.value) {
      setStyle((prevState) => {
        return { ...prevState, ...{ inputFocused: false, labelFloating: false } };
      });
    } else {
      setStyle((prevState) => {
        return { ...prevState, ...{ inputFocused: false } };
      });
    }
  };

  const handleInvalid = (e) => {
    e.preventDefault();

    if (!props.value) {
      setValidation("Please fill out this field.");
    } else {
      setValidation(`${props.regex}`);
    }

    setStyle((prevState) => {
      return { ...prevState, ...{ inputBorderColour: "orangered" } };
    });
  };

  const handleChange = (e) => {
    setValidation("");

    setStyle((prevState) => {
      return { ...prevState, ...{ inputBorderColour: "black" } };
    });

    props.onChange(e);
  };

  //Render...
  return (
    <div className={props.size}>
      <input
        className="textInput"
        style={{ borderColor: style.inputBorderColour }}
        ref={ref}
        name={props.name}
        type={props.type}
        maxLength="255"
        pattern={props.pattern}
        title=""
        value={props.value}
        onFocus={handleFocus}
        onBlur={handleBlur}
        onInvalid={handleInvalid}
        onChange={handleChange}
        required={props.required}
        disabled={props.disabled}
      />
      <div
        className="focusBorder"
        style={style.inputFocused ? { width: "100%" } : { width: "0%" }}
      />
      {validation ? <pre className="validation">{validation}</pre> : null}
      <label
        className="floatingLabel"
        style={
          style.labelFloating
            ? { top: "0%", color: style.labelColour, fontSize: "12px" }
            : { top: "18px", color: style.labelColour, fontSize: "14px" }
        }
      >
        {props.label}
      </label>
      {
        props.type === "password" ? (
          <a className="forgotPasswordLink" href="www.google.co.za" target="_blank">
            Forgot Password?
          </a>
        ) : null /*Probably need to use react router*/
      }
    </div>
  );
});

export default TextInput;

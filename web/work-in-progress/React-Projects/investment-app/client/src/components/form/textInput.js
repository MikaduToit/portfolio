import { forwardRef, useState, useEffect } from "react";
import "./css/textInput.css";

//Export
const TextInput = forwardRef((props, ref) => {
  //State
  const [validation, setValidation] = useState("");
  const [style, setStyle] = useState({
    inputBorderColour: "black",
    inputFocused: false,
    labelColour: "gainsboro",
    labelFloating: false,
    cursor: "text",
    forgotPasswordColour: "darkgrey",
  });

  //Hooks...
  useEffect(() => {
    setValidation("");

    setStyle((prevState) => {
      return { ...prevState, ...{ inputBorderColour: "black" } };
    });

    if (props.value) {
      setStyle((prevState) => {
        return { ...prevState, ...{ labelFloating: true } };
      });
    } else if (!props.value && !style.inputFocused) {
      setStyle((prevState) => {
        return { ...prevState, ...{ labelFloating: false } };
      });
    }
  }, [props.value]);

  //Visually communicate disabled input.
  useEffect(() => {
    if (props.disabled) {
      setStyle((prevState) => {
        return { ...prevState, ...{ inputBorderColour: "dimgrey", labelColour: "dimgrey", cursor: "wait" } };
      });
    } else {
      //Highlight the input if it is responsible for the error response.
      if (props.serverRes.message === "Email not registered!" && props.name === "email") {
        setStyle((prevState) => {
          return { ...prevState, ...{ inputBorderColour: "orangered", labelColour: "gainsboro", cursor: "text" } };
        });
      } else if (props.serverRes.message === "Incorrect email or password!" && props.name === "password") {
        setStyle((prevState) => {
          return { ...prevState, ...{ inputBorderColour: "orangered", labelColour: "gainsboro", cursor: "text" } };
        });
      } else {
        setStyle((prevState) => {
          return { ...prevState, ...{ inputBorderColour: "black", labelColour: "gainsboro", cursor: "text" } };
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
    props.onChange("TextInput", e, null);
  };

  //Forgot Password Link Events...
  const handleLinkFocus = () => {
    setStyle((prevState) => {
      return { ...prevState, ...{ forgotPasswordColour: "rgba(255, 215, 0, 1)" } };
    });
  };

  const handleLinkBlur = () => {
    setStyle((prevState) => {
      return { ...prevState, ...{ forgotPasswordColour: "darkgrey" } };
    });
  };

  //Render...
  return (
    <>
      <div className="inputBox">
        <input
          className="textInput"
          style={{ borderColor: style.inputBorderColour, cursor: style.cursor }}
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
        <div className="inputFocusBorder" style={style.inputFocused ? { width: "100%" } : { width: "0%" }} />
      </div>
      <label
        className="inputFloatingLabel"
        style={
          style.labelFloating ? { top: "0", color: style.labelColour, fontSize: "12px" } : { top: "18px", color: style.labelColour, fontSize: "14px" }
        }
      >
        {props.label}
      </label>
      {validation ? <pre className="inputValidation">{validation}</pre> : null}
      {props.type === "password" && props.name === "password" ? (
        <a
          className="forgotPasswordLink"
          style={{ color: style.forgotPasswordColour }}
          href="/forgot-password"
          target="_blank"
          onFocus={handleLinkFocus}
          onBlur={handleLinkBlur}
        >
          Forgot Password?
        </a>
      ) : null}
    </>
  );
});

export default TextInput;

//Manual password validation...
/*
  const validatePassword = (value) => {
    if (!value) return { valid: true, value: value };
    const password = value.replace(/[^A-Za-z0-9~!@#$%^&*_=+;:,<>.?-]/g, "");
    if (password.match(/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[~!@#$%^&*_=+;:,<>.?-])(.{8,24})$/g)) {
      return { valid: true, value: password };
    } else {
      return { valid: false, value: password };
    }
  };

  const handleInvalid = (e) => {
    if (e.target.validationMessage === "Password Invalid") {
      setValidation(`${props.regex}`);
    } else {
      if (!props.value) {
        setValidation("Please fill out this field.");
      } else {
        setValidation(`${props.regex}`);
      }
    }
  };
  
  const handleChange = (e) => {
    if (e.target.name === "password") {
      let validatedPassword = validatePassword(e.target.value);
      if (validatedPassword.valid) {
        e.target.setCustomValidity("");
        //e.target.checkValidity(); //If we want to show the user in real-time if their password is valid or not.
        props.onChange("PasswordInput", e, validatedPassword.value);
      } else {
        e.target.setCustomValidity("Password Invalid");
        //e.target.checkValidity(); //If we want to show the user in real-time if their password is valid or not.
        props.onChange("PasswordInput", e, validatedPassword.value);
      }
    } else {
      props.onChange("TextInput", e, null);
    }
  };
*/

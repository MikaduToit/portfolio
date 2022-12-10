import { createRef, useRef, useState, useEffect } from "react";
import "./css/phoneInput.css";

//Components
import CustomReactSelect from "./customReactSelect";
import Icon from "../general/icon";

//Export
const PhoneInput = (props) => {
  //Reference forwarding
  const customReactSelectRef = createRef();
  //References
  const phoneInputRef = useRef();
  //State
  const [validation, setValidation] = useState("");
  const [dialCode, setDialCode] = useState("");
  const [country, setCountry] = useState({
    code: "ZA",
    flagSrc: "https://flagpedia.net/data/flags/w580/za.webp",
  });
  const [style, setStyle] = useState({
    inputBorderColour: "black",
    inputFocused: false,
    labelColour: "gainsboro",
  });

  //Hooks...
  useEffect(() => {
    setValidation("");

    setStyle((prevState) => {
      return { ...prevState, ...{ inputBorderColour: "black" } };
    });

    //Set the states for visual feedback...
    let value = props.value[0].value.split(" ");
    let flagSrc = "https://flagpedia.net/data/flags/w580/" + value[0].toLowerCase() + ".webp";
    setCountry((prevState) => {
      return { ...prevState, ...{ code: value[0], flagSrc: flagSrc } };
    });
    setDialCode(value[1]);
  }, [props.value]);

  //Visually communicate disabled input.
  useEffect(() => {
    if (props.disabled) {
      setStyle((prevState) => {
        return { ...prevState, ...{ inputBorderColour: "dimgrey", labelColour: "dimgrey" } };
      });
    } else {
      setStyle((prevState) => {
        return { ...prevState, ...{ inputBorderColour: "black", labelColour: "gainsboro" } };
      });
    }
  }, [props.disabled]);

  //Functions...
  const formatPhoneNumber = (value) => {
    if (!value) return value;

    let phoneNumber = value.replace(/[^\d]/g, "");

    if (phoneNumber.charAt(0) === "0") phoneNumber = "" + phoneNumber.slice(1);

    const phoneNumberLength = phoneNumber.length;
    if (phoneNumberLength < 3) return phoneNumber;
    else if (phoneNumberLength < 6) {
      return `${phoneNumber.slice(0, 2)} ${phoneNumber.slice(2)}`;
    } else {
      return `${phoneNumber.slice(0, 2)} ${phoneNumber.slice(2, 5)} ${phoneNumber.slice(5)}`;
    }
  };

  //Form Events...
  const handleClick = (e) => {
    if (e.target.className === "dialCodeSelectBox") {
      customReactSelectRef.current.focus();
    } else if (e.target.className === "dialCode") {
      phoneInputRef.current.focus();
    }
  };

  const handleMenuClose = () => {
    if (document.activeElement.localName === "body") {
      phoneInputRef.current.focus();
    }
  };

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

  const handleInvalid = (e) => {
    e.preventDefault();

    if (!props.value[1]) {
      setValidation("Please fill out this field.");
    } else {
      setValidation(`${props.regex}`);
    }

    setStyle((prevState) => {
      return { ...prevState, ...{ inputBorderColour: "orangered" } };
    });
  };

  const handleChange = (e) => {
    if (e.target) {
      let formattedPhoneNumber = formatPhoneNumber(e.target.value);
      props.onChange("PhoneInput", null, formattedPhoneNumber);
    } else {
      props.onChange("PhoneInput", null, e);
    }
  };

  //Render...
  return (
    <>
      <div className="inputBox">
        <div className="phoneInputBox" style={{ borderColor: style.inputBorderColour }}>
          <CustomReactSelect
            ref={customReactSelectRef}
            name="dialCodeSelect"
            form={props.form}
            options="dialCodes"
            defaultOption={null}
            placeholder=""
            isMulti={false}
            isSearchable={true}
            isClearable={false}
            isLoading={false}
            isInvalid={validation ? true : false}
            isDisabled={props.disabled}
            isVisible={false}
            value={props.value[0]}
            onMenuClose={() => setTimeout(handleMenuClose, 10)}
            onFocus={handleFocus}
            onBlur={handleBlur}
            onChange={handleChange}
          />
          <div className="phoneInputFlexBox">
            <div className="dialCodeSelectBox" style={props.disabled ? { cursor: "wait" } : { cursor: "pointer" }} onClick={handleClick}>
              <div className="flagBox">
                <img className="flag" src={country.flagSrc} alt={country.code} />
              </div>
              <Icon
                style={{ width: "20px", height: "20px", top: "50%", right: "0", transform: "translateY(-50%)", color: "gainsboro" }}
                icon="dropdownArrow"
                focused={false}
                disabled={props.disabled}
              />
            </div>
            <div className="dialCode" style={props.disabled ? { cursor: "wait" } : { cursor: "text" }} onClick={handleClick}>
              {dialCode}
            </div>
            <input
              className="phoneInput"
              style={props.disabled ? { cursor: "wait" } : { cursor: "text" }}
              ref={phoneInputRef}
              name={props.name}
              type={props.type}
              maxLength="255"
              pattern={props.pattern}
              title=""
              value={props.value[1]}
              onFocus={handleFocus}
              onBlur={handleBlur}
              onInvalid={handleInvalid}
              onChange={handleChange}
              required={props.required}
              disabled={props.disabled}
              placeholder="..."
            />
          </div>
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

export default PhoneInput;

import { forwardRef, useState, useEffect } from "react";
import "./css/submitButton.css";

//Export
const SubmitButton = forwardRef((props, ref) => {
  //State
  const [style, setStyle] = useState({
    borderColour: "white",
    cursor: "pointer",
  });

  //Hooks...
  //Visually communicate disabled input.
  useEffect(() => {
    if (props.disabled) {
      setStyle((prevState) => {
        return { ...prevState, ...{ borderColour: "dimgrey", cursor: "wait" } };
      });
    } else {
      setStyle((prevState) => {
        return { ...prevState, ...{ borderColour: "white", cursor: "pointer" } };
      });
    }
  }, [props.disabled]);

  //Form Events...
  const handleFocus = () => {
    setStyle((prevState) => {
      return { ...prevState, ...{ borderColour: "rgba(255, 215, 0, 1)" } };
    });
  };

  const handleBlur = () => {
    setStyle((prevState) => {
      return { ...prevState, ...{ borderColour: "white" } };
    });
  };

  //Render...
  return (
    <div className="submitButtonBox">
      <button
        className="submitButton"
        type="submit"
        ref={ref}
        style={{ borderColor: style.borderColour, cursor: style.cursor }}
        onFocus={handleFocus}
        onBlur={handleBlur}
        disabled={props.disabled}
      >
        {props.disabled ? (
          <div className="loadingAnimBox">
            <div className="loadingAnim" />
            <div className="loadingAnimOverlay" />
          </div>
        ) : (
          props.label
        )}
      </button>
    </div>
  );
});

export default SubmitButton;

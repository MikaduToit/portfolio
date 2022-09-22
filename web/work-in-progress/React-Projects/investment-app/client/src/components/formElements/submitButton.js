import { forwardRef } from "react";
import "./submitButton.css";

//Export
const SubmitButton = forwardRef((props, ref) => {
  //Render...
  return (
    <div className="submitButtonBox">
      <button
        className="submitButton"
        type="submit"
        ref={ref}
        style={
          props.disabled
            ? { borderColor: "dimgrey", cursor: "auto" }
            : { borderColor: "white", cursor: "pointer" }
        }
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

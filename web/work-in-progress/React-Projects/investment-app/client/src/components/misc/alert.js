import "./alert.css";

//Assets
import { IoClose } from "react-icons/io5";

//Export
const Alert = (props) => {
  //Render...
  return (
    <div className="alert">
      <button className="alertIconButton" onClick={props.onAlertClose} title="Close">
        <IoClose className="alertIcon" />
      </button>
      {props.alertMessage}
    </div>
  );
};

export default Alert;

import "./css/alert.css";

//Components
import IconButton from "./iconButton";
import Icon from "./icon";

//Export
const Alert = (props) => {
  //Render...
  return (
    <div className="alert">
      <Icon
        style={{ width: "20px", height: "20px", top: "50%", left: "10px", transform: "translateY(-50%)", color: "orangered" }}
        icon="alert"
        focused={false}
        disabled={false}
      />
      <div className="alertMessage">{props.alertMessage}</div>
      <IconButton
        style={{ width: "20px", height: "20px", top: "50%", right: "0" }}
        title="Close"
        handleClick={props.onAlertClose}
        icon="close"
        colour="orangered"
        disabled={false}
      />
    </div>
  );
};

export default Alert;

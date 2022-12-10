import "./css/interfaceBox.css";

//Components
import Logo from "./logo";

//Export
const InterfaceBox = (props) => {
  //Render...
  return (
    <section className="interfaceBox" style={props.processing ? { cursor: "wait" } : null}>
      <div className="interfaceBoxPreserveMinWidth" style={{ minWidth: props.minWidth }}>
        <div className="interfaceHeaderBox">{props.logo ? <Logo responsive={false} /> : null}</div>
        {props.children}
      </div>
    </section>
  );
};

export default InterfaceBox;

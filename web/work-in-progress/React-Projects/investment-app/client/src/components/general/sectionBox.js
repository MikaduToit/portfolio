import "./css/sectionBox.css";

//Components
import Icon from "./icon";

//Export
const SectionBox = (props) => {
  //Render...
  return (
    <section className="sectionBox" style={props.processing ? { cursor: "wait" } : null}>
      <div className="sectionBoxPreserveMinWidth" style={{ minWidth: props.minWidth }}>
        <div className="sectionHeaderBox">
          <Icon
            style={{ width: "140px", height: "140px", top: "70%", right: "20px", transform: "translateY(-50%)", color: props.iconColour }}
            icon={props.icon}
            focused={false}
            disabled={false}
          />
          <div className="sectionHeading">{props.heading}</div>
        </div>
        {props.children}
      </div>
    </section>
  );
};

export default SectionBox;

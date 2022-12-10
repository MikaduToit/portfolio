import { useState, useContext, useEffect } from "react";
import "./css/navButton.css";

//Libraries
import { Link } from "react-router-dom";

//Context
import { DashboardContext } from "../../../routes/dashboard";

//Export
const NavButton = (props) => {
  //State
  const [selected, setSelected] = useState(false);
  const [style, setStyle] = useState({
    backgroundColour: "transparent",
    textColour: "#505050",
    cursor: "pointer",
  });
  //Context
  const selectedTab = useContext(DashboardContext).selectedTab;
  const onTabSelection = useContext(DashboardContext).handleTabSelection;
  const awaitingProcess = useContext(DashboardContext).awaitingProcess;

  //Hooks...
  //Check if this button has been selected.
  useEffect(() => {
    if (selectedTab === props.id) {
      setSelected(true);
    } else {
      setSelected(false);
    }
  }, [selectedTab]);

  //Highlight this button if it is selected.
  useEffect(() => {
    if (selected) {
      setStyle((prevState) => {
        return { ...prevState, ...{ backgroundColour: "rgb(143,0,255)", textColour: "whitesmoke" } };
      });
    } else {
      setStyle((prevState) => {
        return { ...prevState, ...{ backgroundColour: "transparent", textColour: "#505050" } };
      });
    }
  }, [selected]);

  useEffect(() => {
    if (awaitingProcess) {
      setStyle((prevState) => {
        return { ...prevState, ...{ cursor: "wait" } };
      });
    } else {
      setStyle((prevState) => {
        return { ...prevState, ...{ cursor: "pointer" } };
      });
    }
  }, [awaitingProcess]);

  //Events...
  const handleFocus = () => {
    if (!selected) {
      setStyle((prevState) => {
        return { ...prevState, ...{ backgroundColour: "black", textColour: "whitesmoke" } };
      });
    }
  };

  const handleBlur = () => {
    if (!selected) {
      setStyle((prevState) => {
        return { ...prevState, ...{ backgroundColour: "transparent", textColour: "#505050" } };
      });
    }
  };

  const handleMouseEnter = () => {
    if (!selected) {
      setStyle((prevState) => {
        return { ...prevState, ...{ backgroundColour: "black", textColour: "whitesmoke" } };
      });
    }
  };

  const handleMouseLeave = () => {
    if (!selected) {
      setStyle((prevState) => {
        return { ...prevState, ...{ backgroundColour: "transparent", textColour: "#505050" } };
      });
    }
  };

  //Render...
  return (
    <Link to={props.href} className="navButtonLink" tabIndex="-1">
      <button
        className="navButton"
        style={{ background: style.backgroundColour, color: style.textColour, cursor: style.cursor }}
        onFocus={handleFocus}
        onBlur={handleBlur}
        onMouseEnter={handleMouseEnter}
        onMouseLeave={handleMouseLeave}
        onClick={() => onTabSelection(props.id)}
        disabled={awaitingProcess ? awaitingProcess : props.disabled}
      >
        {props.label}
      </button>
    </Link>
  );
};

export default NavButton;

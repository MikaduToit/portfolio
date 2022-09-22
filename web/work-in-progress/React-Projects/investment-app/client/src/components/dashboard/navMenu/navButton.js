import { useState, useEffect, useContext } from "react";

//Context
import { DashboardContext } from "../../../routes/dashboard";

//Export
const NavButton = (props) => {
  //State
  const [selected, setSelected] = useState();
  const [backgroundColour, setBackgroundColour] = useState();
  const [textColour, setTextColour] = useState();
  //Context
  const selectedTab = useContext(DashboardContext).selectedTab;
  const onTabSelection = useContext(DashboardContext).handleTabSelection;

  //Hooks...
  //Check if this button has been selected.
  useEffect(() => {
    if (selectedTab === props.id) {
      setSelected(true);
    } else {
      if (selected) {
        setSelected(false);
      }
    }
  }, [selectedTab]);

  //Highlight this button if it is selected.
  useEffect(() => {
    if (selected) {
      setBackgroundColour("rgb(143,0,255)");
      setTextColour("white");
    } else {
      setBackgroundColour("transparent");
      setTextColour("#5a5a5a");
    }
  }, [selected]);

  //Functions...
  const handleMouseEnter = (e) => {
    if (!selected) {
      setBackgroundColour("black");
      setTextColour("white");
    }
  };

  const handleMouseLeave = (e) => {
    if (!selected) {
      setBackgroundColour("transparent");
      setTextColour("#5a5a5a");
    }
  };

  //Render...
  return (
    <button
      className="navButton"
      style={{ backgroundColor: backgroundColour, color: textColour }}
      onMouseEnter={handleMouseEnter}
      onMouseLeave={handleMouseLeave}
      onClick={() => onTabSelection(props.id)}
    >
      {props.label}
    </button>
  );
};

export default NavButton;

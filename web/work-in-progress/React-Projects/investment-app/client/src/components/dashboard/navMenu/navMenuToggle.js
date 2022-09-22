import { useState, useEffect } from "react";

//Assets
import { FiMenu } from "react-icons/fi";

//Export
const NavMenuToggle = (props) => {
  //State
  const [visible, setVisible] = useState();

  //Hooks...
  //Show if user is on mobile viewport.
  useEffect(() => {
    if (props.mobileView) {
      if (!visible) {
        setVisible(true);
      }
    } else {
      if (visible) {
        setVisible(false);
      }
    }
  }, [props.mobileView]);

  //Render...
  return visible ? (
    <button
      className="iconButton"
      style={{ top: "50%", left: "15px" }}
      onClick={props.onNavMenuToggle}
      title="Menu"
    >
      <FiMenu className="icon" />
    </button>
  ) : null;
};

export default NavMenuToggle;

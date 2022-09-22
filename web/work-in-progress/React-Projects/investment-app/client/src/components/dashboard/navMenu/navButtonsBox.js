import { useState, useEffect } from "react";

//Components
import NavSpacer from "./navSpacer";
import NavButton from "./navButton";

//Export
const NavButtonsBox = (props) => {
  //State
  const [positionLeft, setPositionLeft] = useState("-300px");
  //To Map
  const [navButtons] = useState([
    { id: "0", label: "PERSONAL", type: "spacer" },
    { id: "00", label: "Wallet", type: "button" },
    { id: "01", label: "Transaction Log", type: "button" },
    { id: "1", label: "ADMIN", type: "spacer" },
    { id: "10", label: "Wallet", type: "button" },
    { id: "11", label: "Transaction Log", type: "button" },
    { id: "2", label: "HELP", type: "spacer" },
    { id: "20", label: "About", type: "button" },
    { id: "21", label: "Contact Us", type: "button" },
  ]);

  //Hooks...
  //Adjust navMenu position when toggled and when switching between desktop and mobile views.
  useEffect(() => {
    if (props.mobileView && !props.navMenuOpen) {
      if (positionLeft !== "-300px") {
        setPositionLeft("-300px");
      }
    } else {
      if (positionLeft !== "0") {
        setPositionLeft("0");
      }
    }
  }, [props.mobileView, props.navMenuOpen]);

  //Render...
  return (
    <section
      className="navButtonsBox"
      style={{ transition: props.navMenuTransitionStyle, left: positionLeft }}
    >
      <div className="topAndBottomFadeFilter">
        {navButtons.map((navButton) => {
          return navButton.type === "spacer" ? (
            <NavSpacer key={navButton.id} label={navButton.label} />
          ) : (
            <NavButton key={navButton.id} id={navButton.id} label={navButton.label} />
          );
        })}
      </div>
    </section>
  );
};

export default NavButtonsBox;

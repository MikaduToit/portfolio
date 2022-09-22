import { useState, useEffect } from "react";
import "./navMenu.css";

//Components
import NavMenuToggle from "./navMenuToggle";
import Logo from "../../misc/logo";
import NavButtonsBox from "./navButtonsBox";

//Export
const NavMenu = (props) => {
  //State
  const [navMenuOpen, setNavMenuOpen] = useState(false);
  const [navMenuTransitionStyle, setNavMenuTransitionStyle] = useState("none");
  const [backdropOpacity, setBackdropOpacity] = useState("0");
  const [backdropPointerEvents, setBackdropPointerEvents] = useState("none");
  const [backdropTouchAction, setBackdropTouchAction] = useState("none");

  //Hooks...
  //Reset navMenu position when switching between desktop and mobile views.
  useEffect(() => {
    setNavMenuOpen(false);
    setNavMenuTransitionStyle("none");
  }, [props.mobileView]);

  //Adjust backdropFadeFilter when navMenu is toggled.
  useEffect(() => {
    if (navMenuOpen) {
      setBackdropOpacity("0.6");
      setBackdropPointerEvents("auto");
      setBackdropTouchAction("auto");
    } else {
      setBackdropOpacity("0");
      setBackdropPointerEvents("none");
      setBackdropTouchAction("none");
    }
  }, [navMenuOpen]);

  //Functions...
  const handleNavMenuToggle = () => {
    setNavMenuOpen(!navMenuOpen);
    if (navMenuTransitionStyle !== "0.2s ease-in") {
      setNavMenuTransitionStyle("0.2s ease-in");
    }
  };

  //Render...
  return (
    <section className="navMenu">
      <NavMenuToggle mobileView={props.mobileView} onNavMenuToggle={handleNavMenuToggle} />
      <Logo responsive={true} />
      {props.mobileView ? (
        <div
          className="backdropFadeFilter"
          style={{
            opacity: backdropOpacity,
            pointerEvents: backdropPointerEvents,
            touchAction: backdropTouchAction,
          }}
          onClick={handleNavMenuToggle}
        />
      ) : null}
      <NavButtonsBox
        mobileView={props.mobileView}
        navMenuOpen={navMenuOpen}
        navMenuTransitionStyle={navMenuTransitionStyle}
      />
    </section>
  );
};

export default NavMenu;

import { useState, useEffect } from "react";
import "./css/navMenuBox.css";

//Components
import NavMenuToggle from "./navMenuToggle";
import Logo from "../../general/logo";
import NavMenu from "./navMenu";

//Export
const NavMenuBox = (props) => {
  //State
  const [navMenuOpen, setNavMenuOpen] = useState(false);
  const [style, setStyle] = useState({
    backdropFilterOpacity: "0",
    backdropFilterEvents: "none",
    navMenuLeft: "-300px",
    navMenuTransition: "none",
  });

  //Hooks...
  //Reset navMenu when switching between desktop and mobile view.
  useEffect(() => {
    if (props.mobileView) {
      setStyle((prevState) => {
        return { ...prevState, ...{ backdropFilterOpacity: "0", backdropFilterEvents: "none", navMenuLeft: "-300px", navMenuTransition: "none" } };
      });
      setNavMenuOpen(false);
    } else {
      setStyle((prevState) => {
        return { ...prevState, ...{ backdropFilterOpacity: "0", backdropFilterEvents: "none", navMenuLeft: "0", navMenuTransition: "none" } };
      });
      setNavMenuOpen(true);
    }
  }, [props.mobileView]);

  useEffect(() => {
    if (props.mobileView) {
      if (navMenuOpen) {
        setStyle((prevState) => {
          return { ...prevState, ...{ backdropFilterOpacity: "0.6", backdropFilterEvents: "auto", navMenuLeft: "0" } };
        });
      } else {
        setStyle((prevState) => {
          return { ...prevState, ...{ backdropFilterOpacity: "0", backdropFilterEvents: "none", navMenuLeft: "-300px" } };
        });
      }
    }
  }, [navMenuOpen]);

  useEffect(() => {
    if (props.mobileView) {
      setNavMenuOpen(false);
    }
  }, [props.selectedTab]);

  //Functions...
  const handleNavMenuToggle = () => {
    setStyle((prevState) => {
      return { ...prevState, ...{ navMenuTransition: "0.2s ease-in" } };
    });
    setNavMenuOpen(!navMenuOpen);
  };

  //Render...
  return (
    <section className="navMenuBox">
      <NavMenuToggle mobileView={props.mobileView} onNavMenuToggle={handleNavMenuToggle} />
      <Logo responsive={true} />
      {props.mobileView ? (
        <div
          className="backdropFilter"
          style={{
            opacity: style.backdropFilterOpacity,
            pointerEvents: style.backdropFilterEvents,
            touchAction: style.backdropFilterEvents,
          }}
          onClick={handleNavMenuToggle}
        />
      ) : null}
      <NavMenu style={{ left: style.navMenuLeft, transition: style.navMenuTransition }} disabled={!navMenuOpen} />
    </section>
  );
};

export default NavMenuBox;

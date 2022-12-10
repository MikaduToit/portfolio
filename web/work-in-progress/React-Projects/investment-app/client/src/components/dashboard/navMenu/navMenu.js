import { useState, useEffect } from "react";
import "./css/navMenu.css";

//Library Hooks
import { useSelector } from "react-redux";

//Custom Hooks
import { selectCurrentRoles } from "../../../app/authorization/authSlice";

//Components
import TopAndBottomFadeFilter from "../../general/topAndBottomFadeFilter";
import NavSpacer from "./navSpacer";
import NavButton from "./navButton";

//Export
const NavMenu = (props) => {
  //State
  const [navButtonsAll] = useState([
    { id: "0", label: "PERSONAL", type: "spacer", roles: "1010,1005,1000" },
    { id: "00", label: "Wallet", type: "button", roles: "1010,1005,1000", href: "wallet" },
    { id: "01", label: "Investments", type: "button", roles: "1000", href: "investments" },
    { id: "1", label: "ADMINISTRATOR", type: "spacer", roles: "1010,1005" },
    { id: "10", label: "Investment Logging", type: "button", roles: "1005", href: "investment-logging" },
    { id: "11", label: "Client Wallets", type: "button", roles: "1010,1005", href: "client-wallets" },
    { id: "12", label: "Client Investments", type: "button", roles: "1005", href: "client-investments" },
    { id: "13", label: "User Management", type: "button", roles: "1010", href: "user-management" },
    { id: "14", label: "User Registration", type: "button", roles: "1010,1005", href: "user-registration" },
    { id: "2", label: "SUPPORT", type: "spacer", roles: "1010,1005,1000" },
    { id: "20", label: "Contact", type: "button", roles: "1010,1005,1000", href: "contact" },
  ]);
  const [navButtons, setNavButtons] = useState([]);
  //Hooks
  const roles = useSelector(selectCurrentRoles);

  //Hooks...
  //Display only the tabs which are accessible to the user.
  useEffect(() => {
    let navButtonsRestricted = navButtonsAll.map((item) => {
      return { ...item };
    });
    let restrictedIndexes = [];

    navButtonsAll.forEach((element, index) => {
      const elementRoles = element.roles.split(",");

      const matchingRoles = elementRoles.filter((value) => roles.includes(value));
      if (!matchingRoles.length) {
        restrictedIndexes.push(index);
      }
    });

    restrictedIndexes = restrictedIndexes.reverse();
    restrictedIndexes.forEach(function (element) {
      navButtonsRestricted.splice(element, 1);
    });

    setNavButtons(navButtonsRestricted);
  }, [roles]);

  //Render...
  return (
    <section className="navMenu" style={props.style}>
      <TopAndBottomFadeFilter style={null}>
        {navButtons.map((navButton) => {
          return navButton.type === "spacer" ? (
            <NavSpacer key={navButton.id} label={navButton.label} />
          ) : (
            <NavButton key={navButton.id} id={navButton.id} label={navButton.label} href={navButton.href} disabled={props.disabled} />
          );
        })}
        <div className="forceBottomMargin" style={{ height: "20px" }} />
      </TopAndBottomFadeFilter>
    </section>
  );
};

export default NavMenu;

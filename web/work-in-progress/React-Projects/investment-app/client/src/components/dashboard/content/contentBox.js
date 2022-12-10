import { useState, useEffect } from "react";
import "./css/contentBox.css";

//Libraries
import { Routes, Route, Navigate } from "react-router-dom";

//Library Hooks
import { useSelector } from "react-redux";

//Custom Hooks
import { selectCurrentRoles } from "../../../app/authorization/authSlice";

//Components
import TopAndBottomFadeFilter from "../../general/topAndBottomFadeFilter";
import UserRegistration from "./tabs/userRegistration";

//Export
const ContentBox = () => {
  //State
  const [routesAll] = useState([
    { id: "00", roles: "1010,1005,1000", path: "wallet", element: <div style={{ color: "white" }}>Wallet</div> },
    { id: "01", roles: "1000", path: "investments", element: <div style={{ color: "white" }}>Investments</div> },
    { id: "10", roles: "1005", path: "investment-logging", element: <div style={{ color: "white" }}>Investment Logging</div> },
    { id: "11", roles: "1010,1005", path: "client-wallets", element: <div style={{ color: "white" }}>Client Wallets</div> },
    { id: "12", roles: "1005", path: "client-investments", element: <div style={{ color: "white" }}>Client Investments</div> },
    { id: "13", roles: "1010", path: "user-management", element: <div style={{ color: "white" }}>User Management</div> },
    { id: "14", roles: "1010,1005", path: "user-registration", element: <UserRegistration /> },
    { id: "20", roles: "1010,1005,1000", path: "contact", element: <div style={{ color: "white" }}>Contact</div> },
    { id: "9999", roles: "", path: "*", element: <Navigate to="wallet" replace /> },
  ]);
  const [routes, setRoutes] = useState([]);
  //Hooks
  const roles = useSelector(selectCurrentRoles);

  //Hooks...
  //Display only the tabs which are accessible to the user.
  useEffect(() => {
    let routesRestricted = routesAll.map((item) => {
      return { ...item };
    });
    let restrictedIndexes = [];

    routesAll.forEach((element, index) => {
      const elementRoles = element.roles.split(",");

      const matchingRoles = elementRoles.filter((value) => roles.includes(value));
      if (!matchingRoles.length) {
        restrictedIndexes.push(index);
      }
    });

    restrictedIndexes = restrictedIndexes.reverse();
    restrictedIndexes.forEach(function (element) {
      routesRestricted.splice(element, 1);
    });

    setRoutes(routesRestricted);
  }, [roles]);

  //Render...
  return (
    <section className="contentBox">
      <TopAndBottomFadeFilter style={{ scrollbarGutter: "stable both-edges" }}>
        <div className="tabDisplayBox">
          <Routes>
            {routes.map((route) => {
              return <Route key={route.id} path={route.path} element={route.element} />;
            })}
          </Routes>
          <div className="forceBottomMargin" style={{ height: "20px" }} />
        </div>
      </TopAndBottomFadeFilter>
    </section>
  );
};

export default ContentBox;

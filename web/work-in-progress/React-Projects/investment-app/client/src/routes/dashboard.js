import React from "react";
import { useState, useEffect, useCallback } from "react";
import "./css/dashboard.css";

//Libraries
import { useLocation } from "react-router-dom";

//Components
import NavMenuBox from "../components/dashboard/navMenu/navMenuBox";
import NavBarBox from "../components/dashboard/navBar/navBarBox";
import ContentBox from "../components/dashboard/content/contentBox";

//Environment Variables
const gMapsAPIKey = process.env.REACT_APP_GMaps_API_Key;

//Call to Load Google Maps API JS.
function loadGMapsAPIScript() {
  const src = `https://maps.googleapis.com/maps/api/js?v=weekly&key=${gMapsAPIKey}&libraries=places`;

  return new Promise((resolve) => {
    //Create the Google Maps script element...
    const script = document.createElement("script");
    Object.assign(script, {
      type: "text/javascript",
      async: true,
      src,
    });
    //Listen for when the script has loaded before resolving the promise.
    script.addEventListener("load", () => resolve(script));

    document.head.appendChild(script);
  });
}

//Export
const Dashboard = () => {
  //State
  const [mobileView, setMobileView] = useState(window.matchMedia("(max-width: 999px)").matches);
  const [selectedTab, setSelectedTab] = useState("00");
  const [awaitingProcess, setAwaitingProcess] = useState(false);
  const [tabs] = useState([
    { id: "00", href: "wallet" },
    { id: "01", href: "investments" },
    { id: "10", href: "investment-logging" },
    { id: "11", href: "client-wallets" },
    { id: "12", href: "client-investments" },
    { id: "13", href: "user-management" },
    { id: "14", href: "user-registration" },
    { id: "20", href: "contact" },
  ]);
  //Hooks
  const location = useLocation();

  //Hooks...
  //Executes once when component first mounts, because of empty [].
  useEffect(() => {
    //Google maps...
    const initGMaps = () => {
      //Check if an instance of Google Maps has already been loaded into the document.
      if (!window.google) {
        //If an instance doesn't exists, load one.
        return loadGMapsAPIScript();
      }
    };
    initGMaps();

    //Listen for window dimension changes...
    const updateView = () => {
      setMobileView(window.matchMedia("(max-width: 999px)").matches);
    };
    window.matchMedia("(max-width: 999px)").addEventListener("change", updateView);

    setAwaitingProcess(false);

    //Executes once when component first unmounts, because of empty [].
    return () => {
      window.matchMedia("(max-width: 999px)").removeEventListener("change", updateView);
      window.removeEventListener("beforeunload", handleUnload);
    };
  }, []);

  //Updates selectedTab to match path if dissimilar.
  useEffect(() => {
    const path = location.pathname.replace("/dashboard/", "");

    tabs.forEach((element) => {
      if (path === element.href) {
        if (selectedTab !== element.id) {
          setSelectedTab(element.id);
        }
      }
    });
  }, [location]);

  //When processing, warn the user about leaving the page.
  useEffect(() => {
    if (awaitingProcess) {
      window.addEventListener("beforeunload", handleUnload);
    } else {
      window.removeEventListener("beforeunload", handleUnload);
    }
  }, [awaitingProcess]);

  //useCallback ensures the function is not redeclared every render (which would alter its reference).
  const handleUnload = useCallback((e) => {
    e.preventDefault();
    e.returnValue = "";
    return "";
  }, []);

  //Functions...
  const handleTabSelection = (navButtonID) => {
    if (selectedTab !== navButtonID) {
      setSelectedTab(navButtonID);
    }
  };

  const handleAwaitingProcess = (value) => {
    if (value && !awaitingProcess) {
      setAwaitingProcess(value);
    } else if (!value && awaitingProcess) {
      setAwaitingProcess(value);
    }
  };

  //Render...
  return (
    <section className="dashboard">
      <DashboardContext.Provider value={{ selectedTab, handleTabSelection, awaitingProcess, handleAwaitingProcess }}>
        <NavMenuBox mobileView={mobileView} selectedTab={selectedTab} />
        <NavBarBox />
        <ContentBox />
        <div className="bottomShadow" />
      </DashboardContext.Provider>
    </section>
  );
};

export default Dashboard;

//Context API
export const DashboardContext = React.createContext();

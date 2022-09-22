import React from "react";
import { useState, useEffect } from "react";
import "./dashboard.css";

//Custom Hooks
import { useTestMutation } from "../app/authorization/authApiMutation";

//Components
import NavMenu from "../components/dashboard/navMenu/navMenu";
import NavBar from "../components/dashboard/navBar/navBar";
import ContentBox from "../components/dashboard/contentBox/contentBox";

//Context
export const DashboardContext = React.createContext();

//Export
const Dashboard = () => {
  //State
  const [mediaQuery] = useState(window.matchMedia("(max-width: 999px)"));
  const [mobileView, setMobileView] = useState(window.matchMedia("(max-width: 999px)").matches);
  const [selectedTab, setSelectedTab] = useState("00");
  //Hooks
  const [test] = useTestMutation();

  //Hooks...
  //Executes once when component first mounts, because of empty [].
  useEffect(() => {
    mediaQuery.addEventListener("change", updateView);

    //Executes once when component first unmounts, because of empty [].
    return () => {
      mediaQuery.removeEventListener("change", updateView);
    };
  }, []);

  //Functions...
  const updateView = () => {
    setMobileView(mediaQuery.matches);
  };

  const handleTabSelection = (navButtonID) => {
    if (selectedTab !== navButtonID) {
      setSelectedTab(navButtonID);
      handleSubmitTest();
    }
  };

  const handleSubmitTest = async () => {
    try {
      const response = await test().unwrap();
      console.log("Response: " + response.message);
    } catch (err) {
      console.log(err);
    }
  };

  //Render...
  return (
    <section className="dashboard">
      <DashboardContext.Provider value={{ selectedTab, handleTabSelection }}>
        <NavMenu mobileView={mobileView} />
        <NavBar />
        <ContentBox />
      </DashboardContext.Provider>
    </section>
  );
};

export default Dashboard;

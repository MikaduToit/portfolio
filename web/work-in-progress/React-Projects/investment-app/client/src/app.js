import { useState, useEffect } from "react";
import "./app.css";

//Libraries
import { BrowserRouter, Routes, Route, Navigate } from "react-router-dom";

//Library Hooks
import { useSelector } from "react-redux";

//Custom Hooks
import { usePreLoadCheckMutation } from "./app/authorization/authApiMutation";
import { selectCurrentToken } from "./app/authorization/authSlice";

//Routes
import LoadingOverlay from "./components/general/loadingOverlay";
import Login from "./routes/login";
import Dashboard from "./routes/dashboard";
import ForgotPassword from "./routes/forgotPassword";
import ChangePassword from "./routes/changePassword";

//Export
const App = () => {
  //State
  const [preLoadComplete, setPreLoadComplete] = useState(false);
  //Hooks
  const [preLoadCheck] = usePreLoadCheckMutation();
  const token = useSelector(selectCurrentToken);

  //Hooks...
  //Executes once when component first mounts, because of empty [].
  useEffect(() => {
    if (!window.location.pathname.includes("forgot-password") && !window.location.pathname.includes("change-password")) {
      preLoad();
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  //Functions...
  const preLoad = async () => {
    try {
      await preLoadCheck().unwrap();
    } catch {
      //Nothing needs to happen in the case of an error.
    }

    setPreLoadComplete(true);
  };

  //Render...
  return (
    <BrowserRouter>
      <Routes>
        {preLoadComplete ? (
          token ? (
            <>
              <Route path="*" element={<Navigate to="dashboard" replace />} />
              <Route path="dashboard/*" element={<Dashboard />} />
            </>
          ) : (
            <>
              <Route path="*" element={<Navigate to="login" replace />} />
              <Route path="login" element={<Login />} />
            </>
          )
        ) : (
          <Route path="*" element={<LoadingOverlay />} />
        )}
        <Route path="forgot-password" element={<ForgotPassword />} />
        <Route path="forgot-password/change-password" element={<ChangePassword />} />
        <Route path="user-registration/change-password" element={<ChangePassword />} />
      </Routes>
    </BrowserRouter>
  );
};

export default App;

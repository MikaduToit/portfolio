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
import Login from "./routes/login";
import Dashboard from "./routes/dashboard";
import LoadingOverlay from "./components/misc/loadingOverlay";

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
    preLoad();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  //Functions...
  const preLoad = async () => {
    try {
      await preLoadCheck().unwrap();
    } catch {
      //Do nothing.
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
              <Route path="*" element={<Navigate to="/dashboard" replace />} />
              <Route path="/dashboard" element={<Dashboard />} />
            </>
          ) : (
            <>
              <Route path="*" element={<Navigate to="/login" replace />} />
              <Route path="/login" element={<Login />} />
            </>
          )
        ) : (
          <Route path="*" element={<LoadingOverlay />} />
        )}
      </Routes>
    </BrowserRouter>
  );
};

export default App;

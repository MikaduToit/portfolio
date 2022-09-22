import React from "react";
import ReactDOM from "react-dom/client";
import "./index.css";

//Libraries
import { Provider } from "react-redux";

//Redux State
import { store } from "./app/store";

//Routes
import App from "./app";

const root = ReactDOM.createRoot(document.getElementById("root"));
root.render(
  <Provider store={store}>
    <App />
  </Provider>
);

//Production Note: Remove React.StrictMode

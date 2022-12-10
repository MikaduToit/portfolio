import { useContext } from "react";

//Context
import { DashboardContext } from "../../../routes/dashboard";

//Library Hooks
import { useDispatch } from "react-redux";

//Custom Hooks
import { logout } from "../../../app/authorization/authSlice";

//Components
import IconButton from "../../general/iconButton";

//Export
const LogoutButton = () => {
  //Context
  const awaitingProcess = useContext(DashboardContext).awaitingProcess;
  //Hooks
  const dispatch = useDispatch();

  //Functions...
  const handleLogout = () => {
    fetch("http://localhost:3001/logout", { method: "GET", credentials: "include" });
    dispatch(logout());
  };

  //Render...
  return (
    <IconButton
      style={{ width: "20px", height: "20px", top: "50%", right: "5px" }}
      title="Logout"
      handleClick={handleLogout}
      icon="logout"
      colour="black"
      disabled={awaitingProcess}
    />
  );
};

export default LogoutButton;

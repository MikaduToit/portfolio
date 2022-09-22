//Library Hooks
import { useDispatch } from "react-redux";

//Custom Hooks
import { logout } from "../../../app/authorization/authSlice";

//Assets
import { BiLogOut } from "react-icons/bi";

//Export
const LogoutButton = () => {
  //Hooks
  const dispatch = useDispatch();

  //Functions...
  const handleLogout = () => {
    fetch("http://localhost:3001/logout", { method: "GET", credentials: "include" });
    dispatch(logout());
  };

  //Render...
  return (
    <button
      className="iconButton"
      style={{ top: "50%", right: "15px" }}
      onClick={handleLogout}
      title="Logout"
    >
      <BiLogOut className="icon" />
    </button>
  );
};

export default LogoutButton;

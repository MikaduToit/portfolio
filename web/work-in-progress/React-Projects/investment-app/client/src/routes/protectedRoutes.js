//Libraries
import { Outlet, Navigate } from "react-router-dom";

//Library Hooks
import { useSelector } from "react-redux";

//Custom Hooks
import { selectCurrentToken } from "../app/authorization/authSlice";

//Export
const ProtectedRoutes = () => {
  //Hooks
  const token = useSelector(selectCurrentToken);

  return token ? <Outlet /> : <Navigate to="/login" replace />;
};

export default ProtectedRoutes;

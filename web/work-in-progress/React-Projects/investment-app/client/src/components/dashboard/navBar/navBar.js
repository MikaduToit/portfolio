//Components
import LogoutButton from "./logoutButton";
import "./navBar.css";

//Export
const NavBar = () => {
  //Render...
  return (
    <section className="navBar">
      <LogoutButton />
    </section>
  );
};

export default NavBar;

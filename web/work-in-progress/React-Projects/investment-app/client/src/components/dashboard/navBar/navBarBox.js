import "./css/navBarBox.css";

//Components
import LogoutButton from "./logoutButton";

//Export
const NavBarBox = () => {
  //Render...
  return (
    <section className="navBarBox">
      <LogoutButton />
    </section>
  );
};

export default NavBarBox;

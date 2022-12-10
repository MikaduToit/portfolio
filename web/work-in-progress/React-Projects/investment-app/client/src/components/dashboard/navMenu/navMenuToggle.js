//Components
import IconButton from "../../general/iconButton";

//Export
const NavMenuToggle = (props) => {
  //Render...
  return props.mobileView ? (
    <IconButton
      style={{ width: "20px", height: "20px", top: "50%", left: "25px" }}
      title="Menu"
      handleClick={props.onNavMenuToggle}
      icon="menu"
      colour="black"
      disabled={false}
    />
  ) : null;
};

export default NavMenuToggle;

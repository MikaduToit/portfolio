import { useState, useEffect } from "react";
import "./css/icon.css";

//Assets
import { FiAlertTriangle } from "react-icons/fi";
import { IoClose } from "react-icons/io5";
import { FiMenu } from "react-icons/fi";
import { BiLogOut } from "react-icons/bi";
import { IoPersonAddSharp } from "react-icons/io5";
import { IoSearchSharp } from "react-icons/io5";
import { IoIosArrowDown } from "react-icons/io";

//Export
const Icon = (props) => {
  //State
  const [style, setStyle] = useState(props.style);

  //Hooks...
  useEffect(() => {
    if (props.disabled) {
      setStyle((prevState) => {
        return { ...prevState, ...{ color: "dimgrey" } };
      });
    } else {
      setStyle((prevState) => {
        return { ...prevState, ...{ color: props.style.color } };
      });
    }
  }, [props.disabled]);

  useEffect(() => {
    if (props.focused) {
      setStyle((prevState) => {
        return { ...prevState, ...{ color: "rgb(143,0,255)" } };
      });
    } else {
      setStyle((prevState) => {
        return { ...prevState, ...{ color: props.style.color } };
      });
    }
  }, [props.focused]);

  //Render...
  return (
    <>
      {props.icon === "alert" ? (
        <FiAlertTriangle className="icon" style={style} />
      ) : props.icon === "close" ? (
        <IoClose className="icon" style={style} />
      ) : props.icon === "menu" ? (
        <FiMenu className="icon" style={style} />
      ) : props.icon === "logout" ? (
        <BiLogOut className="icon" style={style} />
      ) : props.icon === "newUser" ? (
        <IoPersonAddSharp className="icon" style={style} />
      ) : props.icon === "search" ? (
        <IoSearchSharp className="icon" style={style} />
      ) : props.icon === "dropdownArrow" ? (
        <IoIosArrowDown className="icon" style={style} />
      ) : null}
    </>
  );
};

export default Icon;

import { useState, useEffect } from "react";
import "./css/iconButton.css";

//Components
import Icon from "./icon";

//Export
const IconButton = (props) => {
  //State
  const [style, setStyle] = useState({
    cursor: "pointer",
    focused: false,
  });

  //Hooks...
  //Visually communicate disabled input.
  useEffect(() => {
    if (props.disabled) {
      if (props.icon === "logout") {
        setStyle((prevState) => {
          return { ...prevState, ...{ cursor: "wait" } };
        });
      } else {
        setStyle((prevState) => {
          return { ...prevState, ...{ cursor: "default" } };
        });
      }
    } else {
      setStyle((prevState) => {
        return { ...prevState, ...{ cursor: "pointer" } };
      });
    }
  }, [props.disabled]);

  //Form Events...
  const handleFocus = () => {
    setStyle((prevState) => {
      return { ...prevState, ...{ focused: true } };
    });
  };

  const handleBlur = () => {
    setStyle((prevState) => {
      return { ...prevState, ...{ focused: false } };
    });
  };

  //Render...
  return (
    <button
      className="iconButton"
      style={{
        width: props.style.width,
        height: props.style.height,
        top: props.style.top,
        left: props.style.left,
        right: props.style.right,
        cursor: style.cursor,
      }}
      title={props.title}
      onFocus={handleFocus}
      onBlur={handleBlur}
      onClick={props.handleClick}
      disabled={props.disabled}
    >
      <Icon
        style={{ width: "100%", height: "100%", top: "0", left: "0", color: props.colour }}
        icon={props.icon}
        focused={style.focused}
        disabled={props.disabled}
      />
    </button>
  );
};

export default IconButton;

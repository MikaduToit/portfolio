import { useState, useRef, useEffect } from "react";

//Components
import Select from "react-select";

//React-Select Options
import dialCodeOptions from "./data/reactSelectOptions";

//Export
const DialCodeReactSelect = (props) => {
  //State
  const [menuStyle, setMenuStyle] = useState({
    below: true,
    maxHeight: "220px",
  });
  const [test, setTest] = useState();
  //References
  const selectElement = useRef(null);

  const customStyles = {
    container: () => ({
      zIndex: "1",
      width: "100%",
      minHeight: "30px",
      position: "relative",
      left: "0",
      top: "10px",
      background: "none",
      borderWidth: "0 0 2px 0",
      borderStyle: "none none solid none",
      borderColor: "rgba(255, 215, 0, 1)",
      background: "green",
    }),
    control: () => ({
      width: "100%",
      height: "100%",
      position: "relative",
      left: "0",
      top: "0",
      display: "flex",
      flexFlow: "row nowrap",
      justifyContent: "flex-start",
      alignItems: "flex-end",
    }),
    valueContainer: () => ({
      width: "100%",
      position: "relative",
      padding: "3px 5px 3px 10px",
      flex: "0 1 auto",
      display: "flex",
      flexFlow: "row wrap",
      justifyContent: "flex-start",
      alignItems: "center",
      gap: "3px 3px",
    }),
    placeholder: () => ({
      position: "absolute",
      left: "0",
      top: "50%",
      transform: "translateY(-50%)",
      padding: "3px 10px 3px 10px",
      color: "darkgrey",
      fontFamily: '"Poppins500", sans-serif',
      fontSize: "14px",
      textOverflow: "ellipsis",
      whiteSpace: "nowrap",
      overflow: "hidden",
    }),
    input: () => ({
      height: "20px",
      position: "relative",
      flex: "1 0 0",
      color: "darkgrey",
      fontFamily: '"Poppins500", sans-serif',
      fontSize: "14px",
      textOverflow: "ellipsis",
      whiteSpace: "nowrap",
      overflow: "hidden",
    }),
    singleValue: () => ({
      maxWidth: "100%",
      position: "absolute",
      left: "0",
      top: "50%",
      transform: "translateY(-50%)",
      padding: "3px 10px 3px 10px",
      color: "darkgrey",
      fontFamily: '"Poppins500", sans-serif',
      fontSize: "14px",
      textOverflow: "ellipsis",
      whiteSpace: "nowrap",
      overflow: "hidden",
    }),
    multiValue: () => ({
      position: "relative",
      display: "flex",
      flexFlow: "row nowrap",
      justifyContent: "flex-start",
      alignItems: "center",
      background: "rgba(80, 80, 80, 1)",
      borderRadius: "10px",
      overflow: "hidden",
      cursor: "pointer",
    }),
    multiValueLabel: () => ({
      position: "relative",
      padding: "2px 5px 2px 10px",
      flex: "0 1 auto",
      color: "black",
      fontFamily: '"Poppins500", sans-serif',
      fontSize: "12px",
      lineHeight: "110%",
    }),
    multiValueRemove: () => ({
      position: "relative",
      padding: "2px 5px 0 0",
      flex: "0 0 auto",
      color: "white",
    }),
    indicatorsContainer: () => ({
      height: "28px",
      position: "relative",
      paddingRight: "5px",
      flex: "0 0 auto",
      display: "flex",
      flexFlow: "row nowrap",
      justifyContent: "flex-start",
      alignItems: "center",
      cursor: "pointer",
    }),
    dropdownIndicator: () => ({
      width: "20px",
      height: "20px",
      position: "relative",
      color: "white",
    }),
    indicatorSeparator: () => ({
      width: "1px",
      height: "14px",
      position: "relative",
      marginRight: "5px",
      background: "white",
    }),
    clearIndicator: () => ({
      width: "20px",
      height: "20px",
      position: "relative",
      marginRight: "5px",
      color: "white",
    }),
    loadingIndicator: () => ({
      width: "20px",
      height: "20px",
      position: "relative",
      marginRight: "5px",
      padding: "8px 0px 12px 0px",
      color: "white",
      fontFamily: '"Poppins500", sans-serif',
      fontSize: "3px",
      textAlign: "center",
    }),
    menu: () => ({
      width: "100%",
      position: "absolute",
      left: "0",
      top: menuStyle.below ? "calc(100% + 2px)" : null,
      bottom: menuStyle.below ? null : "calc(100% + 2px)",
      padding: menuStyle.below ? "5px 0 20px 0" : "20px 0 5px 0",
      background: "rgba(80, 80, 80, 1)",
      borderWidth: menuStyle.below ? "0 2px 2px 2px" : "2px 2px 2px 2px",
      borderStyle: menuStyle.below ? "none solid solid solid" : "solid solid solid solid",
      borderColor: "rgba(255, 215, 0, 1)",
      borderRadius: menuStyle.below ? "0 0 20px 20px" : "20px 20px 0 0",
      overflow: "hidden",
    }),
    menuPortal: (provided) => ({
      ...provided,
    }),
    menuList: (provided) => ({
      ...provided,
      maxHeight: menuStyle.maxHeight,
      scrollbarGutter: "stable both-edges",
    }),
    loadingMessage: () => ({
      width: "100%",
      position: "relative",
      padding: "15px 0 5px 0",
      color: "white",
      fontFamily: '"Poppins400", sans-serif',
      fontSize: "12px",
      textAlign: "center",
      textOverflow: "ellipsis",
      whiteSpace: "nowrap",
      overflow: "hidden",
    }),
    noOptionsMessage: () => ({
      width: "100%",
      position: "relative",
      padding: "15px 0 5px 0",
      color: "white",
      fontFamily: '"Poppins400", sans-serif',
      fontSize: "12px",
      textAlign: "center",
      textOverflow: "ellipsis",
      whiteSpace: "nowrap",
      overflow: "hidden",
    }),
    group: () => ({
      position: "relative",
      background: "rgba(70, 70, 70, 1)",
    }),
    groupHeading: () => ({
      position: "relative",
      margin: "0 10px 10px 10px",
      padding: "0 10px 0 10px",
      background: "none",
      color: "white",
      fontFamily: '"Poppins500", sans-serif',
      fontSize: "14px",
      textOverflow: "ellipsis",
      whiteSpace: "nowrap",
      overflow: "hidden",
    }),
    option: () => ({
      position: "relative",
      margin: "0 10px 10px 10px",
      padding: "10px",
      background: "none",
      borderWidth: "0 0 1px 0",
      borderStyle: "none none solid none",
      borderColor: "dimgrey",
      color: "black",
      fontFamily: '"Poppins500", sans-serif',
      fontSize: "12px",
      textOverflow: "ellipsis",
      whiteSpace: "nowrap",
      overflow: "hidden",
      cursor: "pointer",
    }),
  };

  //Hooks...
  useEffect(() => {
    console.log(props);
  }, []);

  useEffect(() => {
    console.log(test);
  }, [test]);

  //Functions...
  const handleMenuOpen = () => {
    //This is running twice sometimes for some reason.
    //We want to run this check less often. It should run when the form size changes. Add a check like our media check in state on the dashboard.
    const form = document.getElementById(selectElement.current.props.form); //There is a better way of storing form and fetching form

    const formHeight = form.offsetHeight;
    console.log(formHeight);
    const distFromTop = props.parent.current.offsetTop;
    console.log(distFromTop);
    const elemHeight = props.parent.current.offsetHeight;
    console.log(elemHeight);

    const spaceBelow = formHeight - (distFromTop + elemHeight);
    console.log(spaceBelow);

    if (
      form.offsetHeight - props.parent.current.offsetTop - props.parent.current.offsetHeight < 220 &&
      props.parent.current.offsetTop + props.parent.current.offsetHeight / 2 > form.offsetHeight / 2
    ) {
      var maxHeight = props.parent.current.offsetTop;
      console.log(maxHeight);
      if (maxHeight >= 220) {
        maxHeight = "220px";
      } else {
        maxHeight = maxHeight + "px";
      }

      setMenuStyle((prevState) => {
        return {
          ...prevState,
          ...{ below: false, maxHeight: maxHeight },
        };
      });
    } else {
      var maxHeight = form.offsetHeight - props.parent.current.offsetTop - props.parent.current.offsetHeight - 40;
      let calcHeight;
      if (maxHeight >= 220) {
        calcHeight = "220px";
      } else {
        calcHeight = maxHeight + "px";
      }
      setMenuStyle((prevState) => {
        return {
          ...prevState,
          ...{ below: true, maxHeight: calcHeight },
        };
      });
    }
  };

  //Render...
  return (
    <Select
      ref={selectElement}
      styles={customStyles}
      name=""
      form={props.form}
      options={dialCodeOptions}
      defaultValue={dialCodeOptions[202]}
      //value={}
      placeholder="Search..."
      //Style
      minMenuHeight={0} //Not working
      maxMenuHeight={0}
      menuPlacement={"top"} //Not working
      //Functionality
      isMulti={false}
      isSearchable={true}
      isClearable={false}
      isLoading={false}
      isDisabled={false}
      //Events
      openMenuOnFocus={true}
      openMenuOnClick={true}
      blurInputOnSelect={true}
      closeMenuOnSelect={true}
      closeMenuOnScroll={false}
      captureMenuScroll={true}
      menuShouldScrollIntoView={true} //Not working
      pageSize={5} //Make it the same number of default diplayed options
      backspaceRemovesValue={true}
      escapeClearsValue={false}
      //DOM Events
      onMenuOpen={handleMenuOpen}
      onMenuClose={null}
      onMenuScrollToTop={null}
      onMenuScrollToBottom={null}
      onInputChange={null}
      onFocus={null}
      onBlur={null}
      onKeyDown={null}
      onChange={setTest}
    />
  );
};

export default DialCodeReactSelect;

//react-select version: 5.4.0

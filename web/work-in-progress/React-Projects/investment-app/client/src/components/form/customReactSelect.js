import { forwardRef, useState, useEffect } from "react";

//Library Hooks
import { useSelector } from "react-redux";

//Custom Hooks
import { selectCurrentRoles } from "../../app/authorization/authSlice";

//Components
import Select from "react-select";

//React-Select Options
import reactSelectOptions from "./data/reactSelectOptions";

//Export
const CustomReactSelect = forwardRef((props, ref) => {
  //State
  const [selectOptions, setSelectOptions] = useState(reactSelectOptions);
  const [menuStyle, setMenuStyle] = useState({
    below: true,
    maxHeight: "200px",
    isVisible: props.isVisible,
  });
  //Hooks
  const roles = useSelector(selectCurrentRoles);
  //Declarations
  const customStyle = {
    container: (styles, { isDisabled }) => ({
      width: "100%",
      minHeight: "30px",
      position: "relative",
      background: "none",
      cursor: isDisabled ? "wait" : "pointer",
    }),
    control: (styles, { isDisabled }) => ({
      width: "100%",
      minHeight: "30px",
      position: "relative",
      left: "0",
      top: "0",
      display: "flex",
      flexFlow: "row nowrap",
      justifyContent: "flex-start",
      alignItems: "flex-end",
      borderWidth: menuStyle.isVisible ? "0 0 2px 0" : "0",
      borderStyle: menuStyle.isVisible ? "none none solid none" : "none",
      borderColor: isDisabled ? "dimgrey" : props.isInvalid ? "orangered" : "black",
    }),
    valueContainer: () => ({
      width: "100%",
      minHeight: "28px",
      position: "relative",
      padding: "4px 10px 4px 10px",
      flex: "0 1 auto",
      display: "flex",
      flexFlow: "row wrap",
      justifyContent: "flex-start",
      alignItems: "center",
      gap: "4px 4px",
      opacity: menuStyle.isVisible ? "1" : "0",
    }),
    placeholder: () => ({
      position: "absolute",
      left: "0",
      top: "50%",
      transform: "translateY(-50%)",
      padding: "3px 10px 3px 10px",
      color: "darkgrey",
      fontFamily: '"Font2", sans-serif',
      fontWeight: "500",
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
      fontFamily: '"Font2", sans-serif',
      fontWeight: "500",
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
      fontFamily: '"Font2", sans-serif',
      fontWeight: "500",
      fontSize: "14px",
      textOverflow: "ellipsis",
      whiteSpace: "nowrap",
      overflow: "hidden",
    }),
    multiValue: () => ({
      minHeight: "20px",
      position: "relative",
      display: "flex",
      flexFlow: "row nowrap",
      justifyContent: "flex-start",
      alignItems: "center",
      background: "rgba(60, 60, 60, 1)",
      borderRadius: "0 10px 0 10px",
      overflow: "hidden",
      cursor: "pointer",
    }),
    multiValueLabel: () => ({
      position: "relative",
      padding: "3px 5px 5px 5px",
      flex: "0 1 auto",
      color: "darkgrey",
      fontFamily: '"Font2", sans-serif',
      fontWeight: "500",
      fontSize: "12px",
      lineHeight: "100%",
    }),
    multiValueRemove: () => ({
      position: "relative",
      padding: "1px 5px 0 0",
      flex: "0 0 auto",
      color: "gainsboro",
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
      opacity: menuStyle.isVisible ? "1" : "0",
    }),
    dropdownIndicator: (styles, { isDisabled }) => ({
      width: "20px",
      height: "20px",
      position: "relative",
      color: isDisabled ? "dimgrey" : "gainsboro",
    }),
    indicatorSeparator: (styles, { isDisabled }) => ({
      width: "1px",
      height: "14px",
      position: "relative",
      marginRight: "5px",
      background: isDisabled ? "dimgrey" : "gainsboro",
    }),
    clearIndicator: (styles, { isDisabled }) => ({
      width: "20px",
      height: "20px",
      position: "relative",
      marginRight: "5px",
      color: isDisabled ? "dimgrey" : "gainsboro",
    }),
    loadingIndicator: (styles, { isDisabled }) => ({
      width: "20px",
      height: "20px",
      position: "relative",
      marginRight: "5px",
      paddingTop: "9px",
      color: isDisabled ? "dimgrey" : "gainsboro",
      fontFamily: '"Font2", sans-serif',
      fontWeight: "500",
      fontSize: "3px",
      textAlign: "center",
    }),
    menu: () => ({
      zIndex: "1",
      width: "100%",
      position: "absolute",
      left: "0",
      top: menuStyle.below ? "100%" : null,
      bottom: menuStyle.below ? null : "98%",
      margin: "0",
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
      boxSizing: "border-box",
      maxHeight: menuStyle.maxHeight,
      margin: "0",
      padding: "0",
      scrollbarGutter: "stable both-edges",
    }),
    loadingMessage: () => ({
      width: "100%",
      position: "relative",
      padding: "15px 0 5px 0",
      color: "gainsboro",
      fontFamily: '"Font1", sans-serif',
      fontWeight: "500",
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
      color: "gainsboro",
      fontFamily: '"Font1", sans-serif',
      fontWeight: "500",
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
      color: "gainsboro",
      fontFamily: '"Font1", sans-serif',
      fontWeight: "500",
      fontSize: "14px",
      textOverflow: "ellipsis",
      whiteSpace: "nowrap",
      overflow: "hidden",
    }),
    option: (styles, { isSelected, isFocused, isDisabled }) => ({
      height: "30px",
      position: "relative",
      margin: "10px 10px 0 10px",
      padding: "8px 5px 10px 5px",
      background: "none",
      borderWidth: "0 0 1px 0",
      borderStyle: "none none solid none",
      borderColor: isSelected ? "rgba(255, 215, 0, 1)" : isFocused ? "white" : "dimgrey",
      color: isDisabled ? "dimgrey" : "black",
      fontFamily: '"Font2", sans-serif',
      fontWeight: "500",
      fontSize: "12px",
      textOverflow: "ellipsis",
      whiteSpace: "nowrap",
      overflow: "hidden",
    }),
  };

  //Hooks...
  //Executes once when component first mounts, because of empty [].
  useEffect(() => {
    handleMenuSize(); //If available space changes regularly, rather call handleMenuSize onMenuOpen.
  }, []);

  //Display only the roles options the user is authorized to register.
  useEffect(() => {
    if (!roles.includes("1010")) {
      let restrictedOptions = selectOptions["roles"].map((item) => {
        return { ...item };
      });
      //Disable Investor and Administrator role options...
      restrictedOptions[1].disabled = true;
      restrictedOptions[2].disabled = true;

      setSelectOptions((prevState) => {
        return {
          ...prevState,
          ...{ roles: restrictedOptions },
        };
      });
    }
  }, [roles]);

  //Functions...
  const handleMenuSize = () => {
    //200 menuList maxHeight for 5 options viewable (matching the pageSize of 5). Space available should be 230 max to account for padding as well.
    const input = ref.current.controlRef.parentElement;
    const inputBox = input.parentElement.parentElement.parentElement;
    const form = inputBox.parentElement;

    const inputHeight = input.offsetHeight;
    const isSpaceBelowGreater = form.offsetHeight / 2 > inputBox.offsetTop + 10 + inputHeight / 2; //The 10 accounts for the 10px margin between the top of the container and the top of the input.
    const spaceBelow = form.offsetHeight - (inputBox.offsetTop + inputBox.offsetHeight - 2); //The 2 accounts for the 2px border at the bottom of the input.
    const spaceAbove = inputBox.offsetTop + 10; //The 10 accounts for the 10px margin between the top of the container and the top of the input.

    if (!isSpaceBelowGreater && spaceBelow < 230) {
      let maxHeight = 0;
      if (spaceAbove >= 230) {
        maxHeight = "200px";
      } else {
        maxHeight = spaceAbove - 30 + "px";
      }

      setMenuStyle((prevState) => {
        return {
          ...prevState,
          ...{ below: false, maxHeight: maxHeight },
        };
      });
    } else {
      let maxHeight = 0;
      if (spaceBelow >= 230) {
        maxHeight = "200px";
      } else {
        maxHeight = spaceBelow - 30 + "px";
      }

      setMenuStyle((prevState) => {
        return {
          ...prevState,
          ...{ below: true, maxHeight: maxHeight },
        };
      });
    }
  };

  //Render...
  return (
    <Select
      ref={ref}
      styles={customStyle}
      name={props.name}
      form={props.form}
      options={selectOptions[props.options]}
      defaultValue={null}
      value={props.value}
      placeholder={props.placeholder}
      //Style
      minMenuHeight={0}
      maxMenuHeight={0}
      menuPlacement={"auto"}
      //Functionality
      isMulti={props.isMulti}
      isSearchable={props.isSearchable}
      isClearable={props.isClearable}
      isLoading={props.isLoading}
      isOptionDisabled={(option) => option.disabled}
      isDisabled={props.isDisabled}
      //Events
      openMenuOnFocus={true}
      openMenuOnClick={true}
      blurInputOnSelect={props.isMulti ? false : true}
      closeMenuOnSelect={props.isMulti ? false : true}
      closeMenuOnScroll={false}
      captureMenuScroll={true}
      menuShouldScrollIntoView={false}
      pageSize={5}
      tabSelectsValue={false}
      backspaceRemovesValue={true}
      escapeClearsValue={false}
      //DOM Events
      onMenuOpen={null}
      onMenuClose={props.onMenuClose}
      onMenuScrollToTop={null}
      onMenuScrollToBottom={null}
      onInputChange={null}
      onFocus={props.onFocus}
      onBlur={props.onBlur}
      onKeyDown={null}
      onChange={props.onChange}
    />
  );
});

export default CustomReactSelect;

//react-select version: 5.4.0

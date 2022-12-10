import { useRef, useState, useEffect } from "react";
import "./css/placesAutocomplete.css";

//Components
import Icon from "../general/icon";

//Export
const PlacesAutocomplete = (props) => {
  //References
  const gMapsLoadCheckCounterRef = useRef(0);
  const addressSearchRef = useRef();
  //State
  const [style, setStyle] = useState({
    inputBorderColour: "black",
    inputFocused: false,
    cursor: "text",
  });

  //Hooks...
  //Executes once when component first mounts, because of empty [].
  useEffect(() => {
    //Initialize Places Autocomplete after short preiod.
    setTimeout(initPlacesAutocomplete, 1000);
  }, []);

  //Visually communicate disabled input.
  useEffect(() => {
    if (props.disabled) {
      setStyle((prevState) => {
        return { ...prevState, ...{ inputBorderColour: "dimgrey", cursor: "wait" } };
      });
    } else {
      setStyle((prevState) => {
        return { ...prevState, ...{ inputBorderColour: "black", cursor: "text" } };
      });
    }
  }, [props.disabled]);

  //Functions...
  const initPlacesAutocomplete = () => {
    //Check if an instance of Google Maps has been loaded into the document.
    if (!window.google) {
      gMapsLoadCheckCounterRef.current = gMapsLoadCheckCounterRef.current + 1;
      if (gMapsLoadCheckCounterRef.current < 5) {
        //Check every second, up to a max of 5 times, if google maps has been initialized on the Dashboard.
        return setTimeout(initPlacesAutocomplete, 1000);
      } else {
        console.log("Google Maps did not initialize correctly! Please refresh your browser to use the autocomplete functionality!");
        return;
      }
    }

    //Make sure the reference for the target container of the autocomplete exists.
    if (!addressSearchRef.current) return;

    const boundsCenter = { lat: -26.195246, lng: 28.034088 }; //Johannesburg's Co-ords.
    const customBounds = {
      north: boundsCenter.lat + 0.1,
      south: boundsCenter.lat - 0.1,
      east: boundsCenter.lng + 0.1,
      west: boundsCenter.lng - 0.1,
    }; //Define a bounding box with sides ~10km from the boundsCenter. This creates a search bias towards the boundsCenter.

    //Define Autocomplete options.
    var options = {
      bounds: customBounds,
      strictBounds: false,
      types: ["address"],
      fields: ["address_components"],
    };

    //Intantiate Autocomplete search bar inside the referenced container with the defined options.
    const autocomplete = new window.google.maps.places.Autocomplete(addressSearchRef.current, options);
    autocomplete.addListener("place_changed", () => handleAddressChange(autocomplete)); //Add an event listener for when a new address is selected.

    //Append Places Autocomplete container to input container.
    setTimeout(() => {
      const pacContainer = autocomplete.gm_accessors_.place.oj.gm_accessors_.input.oj.C;
      addressSearchRef.current.parentElement.appendChild(pacContainer);
    }, 1000);
  };

  const handleAddressChange = (autocomplete) => {
    props.onChange("PlacesAutocomplete", null, autocomplete.getPlace());
  };

  //Form Events...
  const handleFocus = (e) => {
    e.target.select();

    setStyle((prevState) => {
      return { ...prevState, ...{ inputFocused: true } };
    });
  };

  const handleBlur = (e) => {
    setStyle((prevState) => {
      return { ...prevState, ...{ inputFocused: false } };
    });
  };

  //Render...
  return (
    <>
      <div className="inputBox">
        <input
          className="placesAutocompleteInput"
          style={{ borderColor: style.inputBorderColour, cursor: style.cursor }}
          ref={addressSearchRef}
          name={props.name}
          type={props.type}
          pattern={props.pattern}
          title=""
          onFocus={handleFocus}
          onBlur={handleBlur}
          required={false}
          disabled={props.disabled}
          placeholder="Address search..."
        />
        <div className="inputFocusBorder" style={style.inputFocused ? { width: "100%" } : { width: "0%" }} />
        <Icon
          style={{ width: "20px", height: "20px", top: "15px", left: "10px", transform: "translateY(-50%)", color: "gainsboro" }}
          icon="search"
          focused={false}
          disabled={props.disabled}
        />
      </div>
    </>
  );
};

export default PlacesAutocomplete;

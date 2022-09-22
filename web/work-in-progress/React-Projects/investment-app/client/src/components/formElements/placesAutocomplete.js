import { useState, useRef, useEffect } from "react";
import "./placesAutocomplete.css";

//Assets
import { IoSearchSharp } from "react-icons/io5";

//Environment Variables
const gMapsAPIKey = process.env.REACT_APP_GMaps_API_Key;

//Call to Load Google Maps API JS.
function loadGMapsAPIScript() {
  const src = `https://maps.googleapis.com/maps/api/js?v=weekly&key=${gMapsAPIKey}&libraries=places`;

  return new Promise((resolve) => {
    //Create the Google Maps script element...
    const script = document.createElement("script");
    Object.assign(script, {
      type: "text/javascript",
      async: true,
      src,
    });
    //Listen for when the script has loaded before resolving the promise.
    script.addEventListener("load", () => resolve(script));

    document.head.appendChild(script);
  });
}

//Export
const PlacesAutocomplete = (props) => {
  //State
  const [style, setStyle] = useState({
    inputBorderColour: "black",
    inputFocused: false,
    iconColour: "white",
  });
  //References
  const addressSearch = useRef(null);

  //Hooks...
  //Executes once when component first mounts, because of empty [].
  useEffect(() => {
    //First initialize Google Maps, then initialize the Places Autocomplete.
    initGMaps().then(() => initPlacesAutocomplete());
  }, []);

  //Visually communicate disabled input.
  useEffect(() => {
    if (props.disabled) {
      setStyle((prevState) => {
        return { ...prevState, ...{ inputBorderColour: "dimgrey", iconColour: "dimgrey" } };
      });
    } else {
      setStyle((prevState) => {
        return { ...prevState, ...{ inputBorderColour: "black", iconColour: "white" } };
      });
    }
  }, [props.disabled]);

  //Functions...
  const initGMaps = () => {
    //Check if an instance of Google Maps has already been loaded into the document.
    if (window.google) {
      //If an instance exists, DO NOT load another.
      return Promise.resolve();
    }

    //If an instance does not exist, load one.
    return loadGMapsAPIScript();
  };

  const initPlacesAutocomplete = () => {
    //Check if an instance of Google Maps has been loaded into the document.
    if (!window.google) {
      return;
    }

    //Make sure the reference for the target container of the autocomplete exists.
    if (!addressSearch.current) return;

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
    const autocomplete = new window.google.maps.places.Autocomplete(addressSearch.current, options);
    autocomplete.addListener("place_changed", () => handleAddressChange(autocomplete)); //Add an event listener for when a new address is selected.
  };

  const handleAddressChange = (autocomplete) => {
    props.onChange(autocomplete.getPlace());
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
    <div className={props.size}>
      <input
        className="placesAutocompleteInput"
        style={{ borderColor: style.inputBorderColour }}
        ref={addressSearch}
        name={props.name}
        type={props.type}
        pattern={props.pattern}
        title=""
        onFocus={handleFocus}
        onBlur={handleBlur}
        required={props.required}
        disabled={props.disabled}
        placeholder="Address search..."
      />
      <div
        className="focusBorder"
        style={style.inputFocused ? { width: "100%" } : { width: "0%" }}
      />
      <div className="searchIconBox">
        <IoSearchSharp className="icon" style={{ color: style.iconColour }} />
      </div>
    </div>
  );
};

export default PlacesAutocomplete;

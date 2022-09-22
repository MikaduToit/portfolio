import { useState, createRef, useEffect } from "react";

//Assets
import { IoPersonAddSharp } from "react-icons/io5";

//Components
import TextInput from "../../../formElements/textInput";
import PhoneInput from "../../../formElements/phoneInput";
import PlacesAutocomplete from "../../../formElements/placesAutocomplete";
import SubmitButton from "../../../formElements/submitButton";

//Export
const UserRegistration = () => {
  //State
  const [formData, setFormData] = useState({
    firstName: "",
    lastName: "",
    email: "",
    dialCode: "+27",
    phoneNumber: "",
    address: "",
    city: "",
    provinceOrState: "",
    country: "",
    postalCode: "",
  });
  const [error, setError] = useState("");
  const [processing, setProcessing] = useState(false);
  //Reference hooks
  const emailRef = createRef();
  //To Map
  const [formElements] = useState([
    {
      id: "0",
      ref: null,
      component: "TextInput",
      size: "inputBox50",
      name: "firstName",
      type: "text",
      pattern: null,
      regex: "",
      required: true,
      label: "First Name *",
    },
    {
      id: "1",
      ref: null,
      component: "TextInput",
      size: "inputBox50",
      name: "lastName",
      type: "text",
      pattern: null,
      regex: "",
      required: true,
      label: "Last Name *",
    },
    {
      id: "2",
      ref: emailRef,
      component: "TextInput",
      size: "inputBox50",
      name: "email",
      type: "email",
      pattern: null,
      regex: "",
      required: true,
      label: "Email *",
    },
    {
      id: "3",
      ref: null,
      component: "PhoneInput",
      size: "inputBox50",
      name: "phoneNumber",
      type: "text",
      pattern: null,
      regex: "",
      required: false,
      label: "Phone Number",
    },
    {
      id: "4",
      ref: null,
      component: "PlacesAutocomplete",
      size: "inputBox100",
      name: "placesAutocomplete",
      type: "text",
      pattern: null,
      regex: "",
      required: false,
    },
    {
      id: "5",
      ref: null,
      component: "TextInput",
      size: "inputBox100",
      name: "address",
      type: "text",
      pattern: null,
      regex: "",
      required: true,
      label: "Address *",
    },
    {
      id: "6",
      ref: null,
      component: "TextInput",
      size: "inputBox50",
      name: "city",
      type: "text",
      pattern: null,
      regex: "",
      required: true,
      label: "City *",
    },
    {
      id: "7",
      ref: null,
      component: "TextInput",
      size: "inputBox50",
      name: "provinceOrState",
      type: "text",
      pattern: null,
      regex: "",
      required: true,
      label: "Province/State *",
    },
    {
      id: "8",
      ref: null,
      component: "TextInput",
      size: "inputBox50",
      name: "country",
      type: "text",
      pattern: null,
      regex: "",
      required: true,
      label: "Country *",
    },
    {
      id: "9",
      ref: null,
      component: "TextInput",
      size: "inputBox50",
      name: "postalCode",
      type: "text",
      pattern: null,
      regex: "",
      required: true,
      label: "Postal Code *",
    },
  ]);

  useEffect(() => {
    console.log(formData);
  }, [formData]);

  //Form Events...
  const handleChange = (e, value) => {
    if (error) {
      setError("");
    }

    setFormData((prevState) => {
      return { ...prevState, ...{ [e.target.name]: e.target.value } };
    });
  };

  const handlePhoneNumberChange = (value) => {
    if (error) {
      setError("");
    }

    let key = Object.keys(value);
    key = key[0];
    let data = value[key];
    setFormData((prevState) => {
      return { ...prevState, ...{ [key]: data } };
    });
  };

  const handleGMapsChange = (place) => {
    const address = {
      address: "",
      city: "",
      provinceOrState: "",
      country: "",
      postalCode: "",
    };

    if (!place?.address_components) {
      return;
    }

    place.address_components.forEach((component) => {
      const types = component.types;
      const value = component.long_name;

      if (types.includes("street_number") || types.includes("route")) {
        if (!address.address) {
          address.address = value;
        } else {
          address.address = address.address + " " + value;
        }
      }

      if (types.includes("sublocality")) {
        if (!address.address) {
          address.address = value;
        } else {
          address.address = address.address + ", " + value;
        }
      }

      if (types.includes("locality")) {
        address.city = value;
      }

      if (types.includes("administrative_area_level_1")) {
        address.provinceOrState = value;
      }

      if (types.includes("country")) {
        address.country = value;
      }

      if (types.includes("postal_code")) {
        address.postalCode = value;
      }
    });

    console.log(address);
    setFormData((prevState) => {
      return { ...prevState, ...address };
    });
  };

  const handleSubmit = (e) => {
    e.preventDefault();

    setProcessing(true);
  };

  //Render...
  return (
    <>
      <section className="contentDisplayBox" style={{ left: "50%", transform: "translateX(-50%)" }}>
        <div className="internalContainer">
          <div className="headerBox">
            <IoPersonAddSharp className="headingIcon" />
            <div className="heading">Register New Investor...</div>
          </div>
          <form
            className="formBox"
            id="investorRegistrationForm"
            autoComplete="off"
            onSubmit={handleSubmit}
            onKeyDown={(e) => e.key === "Enter" && e.preventDefault()}
          >
            <div className="formKey">* required</div>
            {formElements.map((formElement) => {
              if (formElement.component === "TextInput") {
                return (
                  <TextInput
                    key={formElement.id}
                    size={formElement.size}
                    ref={formElement.ref}
                    name={formElement.name}
                    type={formElement.type}
                    pattern={formElement.pattern}
                    regex={formElement.regex}
                    value={formData[formElement.name]}
                    onChange={handleChange}
                    required={formElement.required}
                    disabled={processing}
                    label={formElement.label}
                  />
                );
              } else if (formElement.component === "PhoneInput") {
                return (
                  <PhoneInput
                    key={formElement.id}
                    size={formElement.size}
                    name={formElement.name}
                    type={formElement.type}
                    dialCode={formData.dialCode}
                    value={formData[formElement.name]}
                    onChange={handlePhoneNumberChange}
                    required={formElement.required}
                    disabled={processing}
                    label={formElement.label}
                    form="investorRegistrationForm"
                  />
                );
              } else if (formElement.component === "PlacesAutocomplete") {
                return (
                  <PlacesAutocomplete
                    key={formElement.id}
                    size={formElement.size}
                    name={formElement.name}
                    type={formElement.type}
                    onChange={handleGMapsChange}
                    required={formElement.required}
                    disabled={processing}
                  />
                );
              }
            })}
            <PhoneInput size="inputBox50" form="investorRegistrationForm" />
            {error ? <div className="serverError">{error}</div> : null}
            <SubmitButton label="Register" ref={null} disabled={processing} />
          </form>
        </div>
      </section>
      <section className="contentDisplayBox" style={{ left: "50%", transform: "translateX(-50%)" }}>
        <div className="internalContainer">
          <form
            className="formBox"
            id="testForm"
            autoComplete="off"
            onSubmit={handleSubmit}
            onKeyDown={(e) => e.key === "Enter" && e.preventDefault()}
          >
            <div style={{ position: "absolute", top: "0px", left: "0px", width: "100%", height: "30px", background: "red" }}></div>
            <PhoneInput size="inputBox50" form="testForm" />
          </form>
        </div>
      </section>
    </>
  );
};

export default UserRegistration;

import { createRef, useState, useContext, useEffect } from "react";

//Context
import { DashboardContext } from "../../../../routes/dashboard";

//Custom Hooks
import { useUserRegistrationMutation } from "../../../../app/authorization/authApiMutation";

//Components
import SectionBox from "../../../general/sectionBox";
import Form from "../../../form/form";

//Export
const UserRegistration = () => {
  //Reference forwarding
  const emailRef = createRef();
  const submitRef = createRef();
  //State
  const [formData, setFormData] = useState({
    roles: [],
    firstName: "",
    lastName: "",
    email: "",
    phoneNumber: [{ value: "ZA +27", label: "South Africa" }, ""],
    address: "",
    city: "",
    provinceOrState: "",
    country: "",
    postalCode: "",
  });
  const [serverRes, setServerRes] = useState({ error: false, message: "" });
  const [processing, setProcessing] = useState(false);
  const [formElements] = useState([
    {
      component: "SelectInput",
      box: "inputBox100",
      id: "0",
      forwardRef: null,
      name: "roles",
      options: "roles",
      multiSelect: true,
      pattern: null,
      regex: "",
      required: true,
      label: "User Roles *",
    },
    {
      component: "TextInput",
      box: "inputBox50",
      id: "1",
      forwardRef: null,
      name: "firstName",
      type: "text",
      pattern: null,
      regex: "",
      required: true,
      label: "First Name *",
    },
    {
      component: "TextInput",
      box: "inputBox50",
      id: "2",
      forwardRef: null,
      name: "lastName",
      type: "text",
      pattern: null,
      regex: "",
      required: true,
      label: "Last Name *",
    },
    {
      component: "TextInput",
      box: "inputBox50",
      id: "3",
      forwardRef: emailRef,
      name: "email",
      type: "email",
      pattern: null,
      regex: "Please input a valid email address.",
      required: true,
      label: "Email *",
    },
    {
      component: "PhoneInput",
      box: "inputBox50",
      id: "4",
      forwardRef: null,
      name: "phoneNumber",
      type: "text",
      pattern: null,
      regex: "",
      required: false,
      label: "Phone Number",
    },
    {
      component: "PlacesAutocomplete",
      box: "inputBox100",
      id: "5",
      forwardRef: null,
      name: "placesAutocomplete",
      type: "text",
      pattern: null,
      regex: "",
      required: false,
    },
    {
      component: "TextInput",
      box: "inputBox100",
      id: "6",
      forwardRef: null,
      name: "address",
      type: "text",
      pattern: null, //"^(?=.*[0-9])(?=.*[A-Za-z])(?=.*[^ ,.])([0-9A-Za-z ,.]+)$" Doesn't work with non-english letters.
      regex: "",
      required: true,
      label: "Address *",
    },
    {
      component: "TextInput",
      box: "inputBox50",
      id: "7",
      forwardRef: null,
      name: "city",
      type: "text",
      pattern: null,
      regex: "",
      required: true,
      label: "City *",
    },
    {
      component: "TextInput",
      box: "inputBox50",
      id: "8",
      forwardRef: null,
      name: "provinceOrState",
      type: "text",
      pattern: null,
      regex: "",
      required: true,
      label: "Province/State *",
    },
    {
      component: "TextInput",
      box: "inputBox50",
      id: "9",
      forwardRef: null,
      name: "country",
      type: "text",
      pattern: null,
      regex: "",
      required: true,
      label: "Country *",
    },
    {
      component: "TextInput",
      box: "inputBox50",
      id: "10",
      forwardRef: null,
      name: "postalCode",
      type: "text",
      pattern: null,
      regex: "",
      required: true,
      label: "Postal Code *",
    },
  ]);
  //Context
  const onAwaitingProcess = useContext(DashboardContext).handleAwaitingProcess;
  //Hooks
  const [userRegistration] = useUserRegistrationMutation();

  //Hooks...
  useEffect(() => {
    onAwaitingProcess(processing);
  }, [processing]);

  //Form Events...
  const handleChange = (name, e, value) => {
    setServerRes((prevState) => {
      return { ...prevState, ...{ error: false, message: "" } };
    });

    if (name === "TextInput") {
      setFormData((prevState) => {
        return { ...prevState, ...{ [e.target.name]: e.target.value } };
      });
    } else if (name === "PhoneInput") {
      setFormData((prevState) => {
        let data = prevState.phoneNumber.map((item, index) => {
          if (index === 0 && typeof value === "object") {
            return value;
          } else if (index === 1 && typeof value === "string") {
            return value;
          } else {
            return item;
          }
        });
        return { ...prevState, ...{ phoneNumber: data } };
      });
    } else if (name === "SelectInput") {
      setFormData((prevState) => {
        return { ...prevState, ...{ roles: value } };
      });
    } else if (name === "PlacesAutocomplete") {
      let address = {
        address: "",
        city: "",
        provinceOrState: "",
        country: "",
        postalCode: "",
      };

      if (!value?.address_components) {
        return;
      } else {
        value.address_components.forEach((component) => {
          const types = component.types;
          const value = component.long_name;

          if (types.includes("street_number") || types.includes("route")) {
            if (!address.address) {
              address.address = value;
            } else {
              address.address = address.address + " " + value;
            }
          } else if (types.includes("sublocality")) {
            if (!address.address) {
              address.address = value;
            } else {
              address.address = address.address + ", " + value;
            }
          } else if (types.includes("locality")) {
            address.city = value;
          } else if (types.includes("administrative_area_level_1")) {
            address.provinceOrState = value;
          } else if (types.includes("country")) {
            address.country = value;
          } else if (types.includes("postal_code")) {
            address.postalCode = value;
          }
        });
      }

      setFormData((prevState) => {
        return { ...prevState, ...address };
      });
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    //Submission prevention for required fields that are manually validated.
    if (formElements[0].required && !Object.keys(formData.roles).length) return;

    setProcessing(true);

    try {
      const response = await userRegistration(formData).unwrap();

      //Communicate instructions to the user.
      setServerRes((prevState) => {
        return { ...prevState, ...{ error: false, message: response.message } };
      });

      setFormData((prevState) => {
        return {
          ...prevState,
          ...{
            roles: [],
            firstName: "",
            lastName: "",
            email: "",
            phoneNumber: [{ value: "ZA +27", label: "South Africa" }, ""],
            address: "",
            city: "",
            provinceOrState: "",
            country: "",
            postalCode: "",
          },
        };
      });

      setProcessing(false);
    } catch (err) {
      if (err.status === 403) {
        setServerRes((prevState) => {
          return { ...prevState, ...{ error: true, message: err.data.message } };
        });
      } else if (err.status === 503) {
        setServerRes((prevState) => {
          return { ...prevState, ...{ error: true, message: "Server connection failed!\nPlease try again later or contact support!" } };
        });
      } else {
        setServerRes((prevState) => {
          return { ...prevState, ...{ error: true, message: "Request failed!\nPlease contact support!" } };
        });
      }

      setProcessing(false);
    }
  };

  //Render...
  return (
    <>
      <SectionBox minWidth="280px" heading="Register New User..." icon="newUser" iconColour="rgba(255, 215, 0, 0.8)" processing={processing}>
        <Form
          id="userRegistrationForm"
          onSubmit={handleSubmit}
          formRequired={true}
          formDescription={null}
          formElements={formElements}
          formData={formData}
          onChange={handleChange}
          serverRes={serverRes}
          buttonLabel="Register"
          buttonForwardRef={submitRef}
          processing={processing}
          autoComplete="off"
          onKeyDown={null}
        />
      </SectionBox>
    </>
  );
};

export default UserRegistration;

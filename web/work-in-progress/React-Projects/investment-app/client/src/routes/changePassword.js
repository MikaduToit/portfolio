import { createRef, useState, useEffect, useCallback } from "react";

//Custom Hooks
import { useChangePasswordMutation } from "../app/authorization/authApiMutation";

//Components
import InterfaceBox from "../components/general/interfaceBox";
import Form from "../components/form/form";

//Export
const ChangePassword = () => {
  //Reference forwarding
  const passwordRef = createRef();
  const submitRef = createRef();
  //State
  const [formData, setFormData] = useState({ newPassword: "", confirmNewPassword: "" });
  const [matching, setMatching] = useState(false);
  const [serverRes, setServerRes] = useState({ error: false, message: "" });
  const [processing, setProcessing] = useState(false);
  const [formElements] = useState([
    {
      component: "TextInput",
      box: "inputBox100",
      id: "0",
      forwardRef: passwordRef,
      name: "newPassword",
      type: "password",
      pattern: "^(?=.{8,24}$)(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[~!@#$%^&*_=+;:,<>.?-])([A-Za-z0-9~!@#$%^&*_=+;:,<>.?-]+)$",
      regex:
        "Password must be 8 - 24 characters in length, including atleast one:\nLowercase Letter, Uppercase Letter, Number, and Symbol [~!@#$%^&*_=+;:,<>.?-]",
      required: true,
      label: "New Password",
    },
    {
      component: "TextInput",
      box: "inputBox100",
      id: "1",
      forwardRef: null,
      name: "confirmNewPassword",
      type: "password",
      pattern: "^(?=.{8,24}$)(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[~!@#$%^&*_=+;:,<>.?-])([A-Za-z0-9~!@#$%^&*_=+;:,<>.?-]+)$",
      regex:
        "Password must be 8 - 24 characters in length, including atleast one:\nLowercase Letter, Uppercase Letter, Number, and Symbol [~!@#$%^&*_=+;:,<>.?-]",
      required: true,
      label: "Confirm New Password",
    },
  ]);
  //Hooks
  const [changePassword] = useChangePasswordMutation();

  //Hooks...
  //Executes once when component first mounts, because of empty [].
  useEffect(() => {
    formElements[0].forwardRef.current.focus();

    //Executes once when component first unmounts, because of empty [].
    return () => {
      window.removeEventListener("beforeunload", handleUnload);
    };
  }, []);

  //When processing, warn the user about leaving the page.
  useEffect(() => {
    if (processing) {
      window.addEventListener("beforeunload", handleUnload);
    } else {
      window.removeEventListener("beforeunload", handleUnload);
    }
  }, [processing]);

  useEffect(() => {
    if (formData.newPassword === formData.confirmNewPassword) {
      setMatching(true);
    } else {
      setMatching(false);
    }
  }, [formData]);

  useEffect(() => {
    if (matching) {
      setServerRes((prevState) => {
        return { ...prevState, ...{ error: false, message: "" } };
      });
    } else {
      setServerRes((prevState) => {
        return { ...prevState, ...{ error: true, message: "Passwords do not match!" } };
      });
    }
  }, [matching]);

  //useCallback ensures the function is not redeclared every render (which would alter its reference).
  const handleUnload = useCallback((e) => {
    e.preventDefault();
    e.returnValue = "";
    return "";
  }, []);

  //Form Events...
  const handleChange = (name, e, value) => {
    if (matching) {
      setServerRes((prevState) => {
        return { ...prevState, ...{ error: false, message: "" } };
      });
    }

    setFormData((prevState) => {
      return { ...prevState, ...{ [e.target.name]: e.target.value } };
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    if (!matching) return;

    submitRef.current.focus();

    setProcessing(true);

    try {
      //Fetch verification JWT from link.
      const searchString = window.location.search;
      const linkVerification = new URLSearchParams(searchString).get("verification");

      if (linkVerification) {
        const response = await changePassword({ verificationJWT: linkVerification, newPassword: formData.newPassword }).unwrap();

        //Communicate instructions to the user.
        setServerRes((prevState) => {
          return { ...prevState, ...{ error: false, message: response.message } };
        });

        setFormData((prevState) => {
          return { ...prevState, ...{ newPassword: "", confirmNewPassword: "" } };
        });

        setProcessing(false);
      } else {
        setServerRes((prevState) => {
          return { ...prevState, ...{ error: true, message: "The URL link you are using is invalid!" } };
        });

        setProcessing(false);
      }
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
      <InterfaceBox minWidth="200px" logo={true} processing={processing}>
        <Form
          id="changePasswordForm"
          onSubmit={handleSubmit}
          formRequired={false}
          formDescription={null}
          formElements={formElements}
          formData={formData}
          onChange={handleChange}
          serverRes={serverRes}
          buttonLabel="Submit"
          buttonForwardRef={submitRef}
          processing={processing}
          autoComplete="off"
          onKeyDown={null}
        />
      </InterfaceBox>
      <div className="forceBottomMargin" style={{ height: "60px" }} />
    </>
  );
};

export default ChangePassword;

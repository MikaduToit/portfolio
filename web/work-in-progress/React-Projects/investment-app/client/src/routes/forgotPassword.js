import { createRef, useState, useEffect, useCallback } from "react";

//Custom Hooks
import { useForgotPasswordMutation } from "../app/authorization/authApiMutation";

//Components
import InterfaceBox from "../components/general/interfaceBox";
import Form from "../components/form/form";

//Export
const ForgotPassword = () => {
  //Reference forwarding
  const emailRef = createRef();
  const submitRef = createRef();
  //State
  const [formData, setFormData] = useState({ email: "" });
  const [serverRes, setServerRes] = useState({ error: false, message: "" });
  const [processing, setProcessing] = useState(false);
  const [formElements] = useState([
    {
      component: "TextInput",
      box: "inputBox100",
      id: "0",
      forwardRef: emailRef,
      name: "email",
      type: "email",
      pattern: null,
      regex: "Please input a valid email address.",
      required: true,
      label: "Email",
    },
  ]);
  //Hooks
  const [forgotPassword] = useForgotPasswordMutation();

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

  //useCallback ensures the function is not redeclared every render (which would alter its reference).
  const handleUnload = useCallback((e) => {
    e.preventDefault();
    e.returnValue = "";
    return "";
  }, []);

  //Focus on appropriate input after server response is received.
  useEffect(() => {
    if (serverRes.error) {
      formElements[0].forwardRef.current.focus();
    }
  }, [serverRes.error]);

  //Form Events...
  const handleChange = (name, e, value) => {
    setServerRes((prevState) => {
      return { ...prevState, ...{ error: false, message: "" } };
    });

    setFormData((prevState) => {
      return { ...prevState, ...{ [e.target.name]: e.target.value } };
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    submitRef.current.focus();

    setProcessing(true);

    try {
      const response = await forgotPassword(formData).unwrap();

      //Communicate instructions to the user.
      setServerRes((prevState) => {
        return { ...prevState, ...{ error: false, message: response.message } };
      });

      setFormData((prevState) => {
        return { ...prevState, ...{ email: "" } };
      });

      setProcessing(false);
    } catch (err) {
      if (err.status === 403 || err.status === 500) {
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
          id="forgotPasswordForm"
          onSubmit={handleSubmit}
          formRequired={false}
          formDescription="Please enter the email address associated with the forgotten password."
          formElements={formElements}
          formData={formData}
          onChange={handleChange}
          serverRes={serverRes}
          buttonLabel="Submit"
          buttonForwardRef={submitRef}
          processing={processing}
          autoComplete="on"
          onKeyDown={null}
        />
      </InterfaceBox>
      <div className="forceBottomMargin" style={{ height: "60px" }} />
    </>
  );
};

export default ForgotPassword;

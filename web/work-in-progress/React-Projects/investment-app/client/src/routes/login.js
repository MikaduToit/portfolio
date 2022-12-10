import { createRef, useState, useEffect, useCallback } from "react";
import jwtDecode from "jwt-decode";

//Library Hooks
import { useSelector, useDispatch } from "react-redux";

//Custom Hooks
import { selectCurrentSessionExpired } from "../app/authorization/authSlice";
import { useLoginMutation } from "../app/authorization/authApiMutation";
import { setCredentials, session } from "../app/authorization/authSlice";

//Components
import Alert from "../components/general/alert";
import InterfaceBox from "../components/general/interfaceBox";
import Form from "../components/form/form";

//Export
const Login = () => {
  //Reference forwarding
  const emailRef = createRef();
  const passwordRef = createRef();
  const submitRef = createRef();
  //State
  const [alert, setAlert] = useState();
  const [formData, setFormData] = useState({ email: "", password: "" });
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
    {
      component: "TextInput",
      box: "inputBox100",
      id: "1",
      forwardRef: passwordRef,
      name: "password",
      type: "password",
      pattern: "^(?=.{8,24}$)(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[~!@#$%^&*_=+;:,<>.?-])([A-Za-z0-9~!@#$%^&*_=+;:,<>.?-]+)$",
      regex:
        "Password must be 8 - 24 characters in length, including atleast one:\nLowercase Letter, Uppercase Letter, Number, and Symbol [~!@#$%^&*_=+;:,<>.?-]",
      required: true,
      label: "Password",
    },
  ]);
  //Hooks
  const sessionExpired = useSelector(selectCurrentSessionExpired);
  const dispatch = useDispatch();
  const [login] = useLoginMutation();

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

  //Alert user of expired session.
  useEffect(() => {
    window.scrollTo(0, 0);
    setAlert(sessionExpired);
  }, [sessionExpired]);

  //Focus on appropriate input after server response is received.
  useEffect(() => {
    if (serverRes.error) {
      if (serverRes.message === "Email not registered!") {
        formElements[0].forwardRef.current.focus();
      } else {
        formElements[1].forwardRef.current.focus();
      }
    }
  }, [serverRes.error]);

  //Functions...
  const handleAlertClose = () => {
    dispatch(session({ sessionExpired: false }));
  };

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
      const response = await login(formData).unwrap();

      //Reset state to default...
      setFormData({ email: "", password: "" });

      const userInfo = jwtDecode(response.accessToken).UserInfo;

      //Update Redux store with logged in user's info...
      dispatch(
        setCredentials({
          id: userInfo.id,
          roles: userInfo.roles,
          token: response.accessToken,
        })
      );
      dispatch(session({ sessionExpired: false }));

      //When credentials are set, App will automatically navigate to Dashboard.
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
          return { ...prevState, ...{ error: true, message: "Login failed!\nPlease contact support!" } };
        });
      }

      setFormData((prevState) => {
        return { ...prevState, ...{ password: "" } };
      });

      setProcessing(false);
    }
  };

  //Render...
  return (
    <>
      {alert ? <Alert onAlertClose={handleAlertClose} alertMessage={"Session expired."} /> : null}
      <InterfaceBox minWidth="200px" logo={true} processing={processing}>
        <Form
          id="loginForm"
          onSubmit={handleSubmit}
          formRequired={false}
          formDescription={null}
          formElements={formElements}
          formData={formData}
          onChange={handleChange}
          serverRes={serverRes}
          buttonLabel="Login"
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

export default Login;

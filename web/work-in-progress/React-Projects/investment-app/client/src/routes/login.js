import { useState, createRef, useEffect } from "react";
import "./login.css";
import jwtDecode from "jwt-decode";

//Library Hooks
import { useSelector, useDispatch } from "react-redux";

//Custom Hooks
import { selectCurrentSessionExpired } from "../app/authorization/authSlice";
import { useLoginMutation } from "../app/authorization/authApiMutation";
import { setCredentials, session } from "../app/authorization/authSlice";

//Components
import Alert from "../components/misc/alert";
import Logo from "../components/misc/logo";
import TextInput from "../components/formElements/textInput";
import SubmitButton from "../components/formElements/submitButton";

//Export
const Login = () => {
  //State
  const [alert, setAlert] = useState();
  const [formData, setFormData] = useState({ email: "", password: "" });
  const [error, setError] = useState("");
  const [processing, setProcessing] = useState(false);
  //Reference forwarding
  const emailRef = createRef();
  const passwordRef = createRef();
  const submitRef = createRef();
  //To Map
  const [formElements] = useState([
    {
      id: "0",
      ref: emailRef,
      component: "TextInput",
      size: "inputBox100",
      name: "email",
      type: "email",
      pattern: null,
      regex: "Please input a valid email address.",
      required: true,
      label: "Email",
    },
    {
      id: "1",
      ref: passwordRef,
      component: "TextInput",
      size: "inputBox100",
      name: "password",
      type: "password",
      pattern: "^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[.!@#$%^&*_=+-]).{8,24}$",
      regex:
        "Password must be 8 - 24 characters in length, including atleast one:\nLowercase Letter, Uppercase Letter, Number, and Symbol [.!@#$%^&*_=+-]",
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
    if (formElements[0].ref.current) {
      formElements[0].ref.current.focus();
    }
  }, []);

  //Alert user of expired session.
  useEffect(() => {
    window.scrollTo(0, 0);
    setAlert(sessionExpired);
  }, [sessionExpired]);

  //Focus on appropriate input after error is returned.
  useEffect(() => {
    if (error) {
      if (error === "Email not registered!") {
        formElements[0].ref.current.focus();
      } else {
        formElements[1].ref.current.focus();
      }
    }
  }, [error]);

  //Functions...
  const handleAlertClose = () => {
    dispatch(session({ sessionExpired: false }));
  };

  //Form Events...
  const handleChange = (e) => {
    if (error) {
      setError("");
    }

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
      setError("");

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
        setError(err.data.message);
      } else if (err.status === 503) {
        setError("Server connection failed! Please try again later or contact support!");
      } else {
        setError("Login failed! Please contact support!");
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
      {alert ? <Alert onAlertClose={handleAlertClose} alertMessage={"Session Expired!"} /> : null}
      <section className="loginBox">
        <div className="internalContainer">
          <div className="headerBox">
            <Logo responsive={false} />
          </div>
          <form className="formBox" onSubmit={handleSubmit}>
            {formElements.map((formElement) => {
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
                  error={
                    error === "Email not registered!" && formElement.name === "email"
                      ? true
                      : error === "Incorrect email or password!" && formElement.name === "password"
                      ? true
                      : false
                  }
                />
              );
            })}
            {error ? <div className="serverError">{error}</div> : null}
            <SubmitButton label="Login" ref={submitRef} disabled={processing} />
          </form>
        </div>
      </section>
      <div className="forcePageBottomMargin" />
    </>
  );
};

export default Login;

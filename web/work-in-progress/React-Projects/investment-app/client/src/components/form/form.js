import "./css/form.css";

//Components
import SelectInput from "./selectInput";
import TextInput from "./textInput";
import PhoneInput from "./phoneInput";
import PlacesAutocomplete from "./placesAutocomplete";
import SubmitButton from "./submitButton";

//Export
const Form = (props) => {
  //Render...
  return (
    <form className="form" id={props.id} onSubmit={props.onSubmit} autoComplete={props.autoComplete} onKeyDown={props.onKeyDown}>
      {props.formRequired ? <div className="formKey">* required</div> : null}
      {props.formDescription ? <pre className="formDescription">{props.formDescription}</pre> : null}
      {props.formElements.map((formElement) => {
        if (formElement.component === "TextInput") {
          return (
            <div key={formElement.id} className={formElement.box}>
              <TextInput
                key={formElement.id}
                ref={formElement.forwardRef}
                name={formElement.name}
                type={formElement.type}
                pattern={formElement.pattern}
                regex={formElement.regex}
                value={props.formData[formElement.name]}
                onChange={props.onChange}
                required={formElement.required}
                disabled={props.processing}
                label={formElement.label}
                serverRes={props.serverRes}
              />
            </div>
          );
        } else if (formElement.component === "PhoneInput") {
          return (
            <div key={formElement.id} className={formElement.box}>
              <PhoneInput
                key={formElement.id}
                form={props.id}
                name={formElement.name}
                type={formElement.type}
                value={props.formData[formElement.name]}
                onChange={props.onChange}
                required={formElement.required}
                disabled={props.processing}
                label={formElement.label}
              />
            </div>
          );
        } else if (formElement.component === "SelectInput") {
          return (
            <div key={formElement.id} className={formElement.box}>
              <SelectInput
                key={formElement.id}
                form={props.id}
                name={formElement.name}
                options={formElement.options}
                multiSelect={formElement.multiSelect}
                value={props.formData[formElement.name]}
                onChange={props.onChange}
                required={formElement.required}
                disabled={props.processing}
                label={formElement.label}
              />
            </div>
          );
        } else if (formElement.component === "PlacesAutocomplete") {
          return (
            <div key={formElement.id} className={formElement.box}>
              <PlacesAutocomplete
                key={formElement.id}
                name={formElement.name}
                type={formElement.type}
                onChange={props.onChange}
                disabled={props.processing}
              />
            </div>
          );
        }
      })}
      <div className="forceBottomMargin" style={{ height: "30px" }} />
      {props.serverRes.message ? (
        <pre className="serverRes" style={props.serverRes.error ? { color: "tomato" } : { color: "rgba(255, 215, 0, 1)" }}>
          {props.serverRes.message}
        </pre>
      ) : null}
      <SubmitButton label={props.buttonLabel} ref={props.buttonForwardRef} disabled={props.processing} />
    </form>
  );
};

export default Form;

import "./loadingOverlay.css";

//Export
const LoadingOverlay = () => {
  //Render...
  return (
    <div className="loadingOverlay">
      <div className="submitButtonBox">
        <button className="submitButton" type="submit" disabled={true}>
          <div className="loadingAnimBox">
            <div className="loadingAnim" />
            <div className="loadingAnimOverlay" />
          </div>
        </button>
      </div>
    </div>
  );
};

export default LoadingOverlay;

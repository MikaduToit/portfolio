import "./css/loadingOverlay.css";

//Export
const LoadingOverlay = () => {
  //Render...
  return (
    <div className="loadingOverlay">
      <div className="loadingOverlayAnimBox">
        <div className="loadingOverlayAnimBullet" id="loadingOverlayAnimBullet1">
          &#8226;
        </div>
        <div className="loadingOverlayAnimBullet" id="loadingOverlayAnimBullet2">
          &#8226;
        </div>
        <div className="loadingOverlayAnimBullet" id="loadingOverlayAnimBullet3">
          &#8226;
        </div>
      </div>
    </div>
  );
};

export default LoadingOverlay;

import "./contentBox.css";

//Components
import UserRegistration from "./tabs/userRegistration";

//Export
const ContentBox = () => {
  //Render...
  return (
    <section className="contentBox">
      <div className="topAndBottomFadeFilter" style={{ scrollbarGutter: "stable both-edges" }}>
        <div className="contentBoxFlexLayout">
          <UserRegistration />
          <div className="forcePageBottomMargin" />
        </div>
      </div>
    </section>
  );
};

export default ContentBox;

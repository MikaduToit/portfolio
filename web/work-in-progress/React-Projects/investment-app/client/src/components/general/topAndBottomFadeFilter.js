import "./css/topAndBottomFadeFilter.css";

//Export
const TopAndBottomFadeFilter = (props) => {
  //Render...
  return (
    <div className="topAndBottomFadeFilter" style={props.style}>
      {props.children}
    </div>
  );
};

export default TopAndBottomFadeFilter;

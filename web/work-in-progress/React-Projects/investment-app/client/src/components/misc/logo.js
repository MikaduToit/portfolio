import "./logo.css";

//Assets
import logo40p from "../../assets/logos/MQWLogoBlack_40p.png";
import logo80p from "../../assets/logos/MQWLogoBlack_80p.png";
import logo120p from "../../assets/logos/MQWLogoBlack_120p.png";
import logo160p from "../../assets/logos/MQWLogoBlack_160p.png";
import logo200p from "../../assets/logos/MQWLogoBlack_200p.png";
import logo240p from "../../assets/logos/MQWLogoBlack_240p.png";
import logo280p from "../../assets/logos/MQWLogoBlack_280p.png";
import logo320p from "../../assets/logos/MQWLogoBlack_320p.png";
import logo360p from "../../assets/logos/MQWLogoBlack_360p.png";
import logo400p from "../../assets/logos/MQWLogoBlack_400p.png";

//Export
const Logo = (props) => {
  //Render...
  return props.responsive ? (
    <picture className="logoBox">
      <source
        media="(max-width: 999px)"
        srcSet={`${logo40p} 1x, ${logo80p} 2x, ${logo120p} 3x, ${logo160p} 4x, ${logo200p} 5x`}
      />
      <source
        media="(min-width: 1000px)"
        srcSet={`${logo80p} 0.5x, ${logo120p} 1x, ${logo160p} 1.5x, ${logo200p} 2x, ${logo240p} 2.5x, ${logo280p} 3x, ${logo320p} 3.5x, ${logo360p} 4x, ${logo400p} 4.5x`}
      />
      <img className="logo" src={logo200p} alt="Logo" />
    </picture>
  ) : (
    <picture className="logoBoxAlt">
      <source
        media="(min-width: 0px)"
        srcSet={`${logo80p} 0.5x, ${logo120p} 1x, ${logo160p} 1.5x, ${logo200p} 2x, ${logo240p} 2.5x, ${logo280p} 3x, ${logo320p} 3.5x, ${logo360p} 4x, ${logo400p} 4.5x`}
      />
      <img className="logoAlt" src={logo200p} alt="Logo" />
    </picture>
  );
};

export default Logo;

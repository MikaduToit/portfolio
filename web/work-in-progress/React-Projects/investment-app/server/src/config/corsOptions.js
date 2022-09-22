const allowedOrigins = require("./allowedOrigins");

//Production Note: Remove !origin condition.
const corsOptions = {
  origin: (origin, callback) => {
    if (allowedOrigins.indexOf(origin) !== -1 || !origin) {
      callback(null, true);
    } else {
      callback(new Error("Request blocked by CORS"));
    }
  },
  optionsSuccessStatus: 200,
};

module.exports = corsOptions;

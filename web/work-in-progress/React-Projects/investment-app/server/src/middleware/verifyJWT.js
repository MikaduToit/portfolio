const dotenv = require("dotenv").config();
const jwt = require("jsonwebtoken");

const verifyJWT = (req, res, next) => {
  const authHeader = req.headers.authorization || req.headers.Authorization;

  if (!authHeader?.startsWith("Bearer ")) return res.sendStatus(403); //FORBIDDEN, because request header does not contain an Access Token.

  //If request header DOES contain an Access Token...
  const accessToken = authHeader.split(" ")[1];

  //Verify the Access Token recieved.
  jwt.verify(accessToken, process.env.access_Token_Secret, (err, decoded) => {
    if (err) return res.sendStatus(401); //UNAUTHORIZED, because verification of the Access Token failed.

    //Access Token passed verification check.
    req.id = decoded.UserInfo.id; //Token verification has take place so these credentials can be used for any further processes.
    req.roles = decoded.UserInfo.roles; //Token verification has take place so these credentials can be used for any further processes.

    next();
  });
};

module.exports = verifyJWT;

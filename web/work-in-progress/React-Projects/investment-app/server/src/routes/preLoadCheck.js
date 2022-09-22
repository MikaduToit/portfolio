const express = require("express");
const router = express.Router();

const dotenv = require("dotenv").config();
const jwt = require("jsonwebtoken");

//Middleware
const verifyJWTPreLoad = (req, res, next) => {
  const authHeader = req.headers.authorization || req.headers.Authorization;

  if (!authHeader?.startsWith("Bearer ")) return res.sendStatus(401); //UNAUTHORIZED, because request header does not contain an Access Token, but we still want to check Cookies for a Refresh Token.

  //If request header DOES contain an Access Token...
  const accessToken = authHeader.split(" ")[1];

  //Verify the Access Token recieved.
  jwt.verify(accessToken, process.env.access_Token_Secret, (err, decoded) => {
    if (err) return res.sendStatus(401); //UNAUTHORIZED, because verification of the Access Token failed, but we still want to check Cookies for a Refresh Token.

    next();
  });
};

//Custom Middleware (Authorization)
router.use(verifyJWTPreLoad);

//Route
router.get("/", (req, res) => {
  return res.sendStatus(204); //NO CONTENT, but successful.
});

module.exports = router;

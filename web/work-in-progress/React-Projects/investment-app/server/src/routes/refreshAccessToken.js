const express = require("express");
const router = express.Router();
const dotenv = require("dotenv").config();
const mysql = require("mysql");
const dbConfig = require("../config/dbConfig");
const jwt = require("jsonwebtoken");

//Custom middleware.
const { errorHandler } = require("../middleware/logHandler");

router.get("/", (req, res) => {
  const cookies = req.cookies;

  let errors = "";
  let statusCode = null;

  if (!cookies?.jwt) return res.sendStatus(403); //FORBIDDEN, because Cookie expired, therefore Refresh Token expired.

  const refreshToken = cookies.jwt;

  jwt.verify(refreshToken, process.env.refresh_Token_Secret, (err, decoded) => {
    if (err) {
      return res.sendStatus(403); //FORBIDDEN, because verification of the Refresh Token failed.
    } else {
      function handleLostConnectionDB(db) {
        db.on("error", (err) => {
          if (err.code === "PROTOCOL_CONNECTION_LOST") {
            db.end();
            errorHandler(err.message);
            return res.sendStatus(503); //SERVICE UNAVAILABLE
          }
        });
      }

      let handleRefreshAccessToken = new Promise((resolve, reject) => {
        let db = mysql.createConnection(dbConfig);

        db.connect((err) => {
          if (err) {
            db.end();
            errors = err.message;
            statusCode = 503; //SERVICE UNAVAILABLE
            reject();
          } else {
            handleLostConnectionDB(db);
            resolve(db);
          }
        });
      })
        .then((db) => {
          return new Promise((resolve, reject) => {
            db.query(
              "SELECT Roles, LoginRefreshToken FROM tbl_users WHERE ID = ?",
              [decoded.id],
              (err, result) => {
                if (err) {
                  db.end();
                  errors = err.message;
                  statusCode = 503; //SERVICE UNAVAILABLE
                  reject();
                } else if (result) {
                  if (!result.length) {
                    db.end();
                    errors = "Refresh Token recieved containing an invalid ID!";
                    statusCode = 403; //FORBIDDEN, because ID in Refresh Token does not exist in the DB.
                    reject();
                  } else {
                    db.end();
                    resolve(result);
                  }
                }
              }
            );
          });
        })
        .then((result) => {
          if (refreshToken === result[0].LoginRefreshToken) {
            accessToken = jwt.sign(
              {
                UserInfo: { id: decoded.id, roles: result[0].Roles },
              },
              process.env.access_Token_Secret,
              { expiresIn: "10m" }
            );
            return res.json({ accessToken });
          } else {
            return res.sendStatus(403); //FORBIDDEN, because Refresh Token in cookie does not match Refresh Token stored in DB.
          }
        })
        .catch(() => {
          if (errors) {
            errorHandler(errors);
          }
          return res.sendStatus(statusCode);
        });
    }
  });
});

module.exports = router;

const express = require("express");
const router = express.Router();
const mysql = require("mysql");
const dbConfig = require("../config/dbConfig");
const jwt = require("jsonwebtoken");

//Custom middleware.
const { errorHandler } = require("../middleware/logHandler");

router.get("/", (req, res) => {
  const cookies = req.cookies;

  let errors = "";
  let statusCode = null;

  if (!cookies?.jwt) return res.sendStatus(204); //NO CONTENT, but successful. Cookie doesn't exist which is fine.

  const refreshToken = cookies.jwt;

  function handleLostConnectionDB(db) {
    db.on("error", (err) => {
      if (err.code === "PROTOCOL_CONNECTION_LOST") {
        db.end();
        errorHandler(err.message);
        return res
          .clearCookie("jwt", {
            httpOnly: true,
            sameSite: "None",
            secure: true,
          })
          .sendStatus(503); //SERVICE UNAVAILABLE
      }
    });
  }

  let handleLogout = new Promise((resolve, reject) => {
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
        const decoded = jwt.decode(refreshToken);

        db.query(
          "UPDATE tbl_users SET LoginRefreshToken = ? WHERE ID = ?",
          ["", decoded.id],
          (err, result) => {
            if (err) {
              db.end();
              errors = err.message;
              statusCode = 503; //SERVICE UNAVAILABLE
              reject();
            } else if (result) {
              db.end();
              resolve();
            }
          }
        );
      });
    })
    .then(() => {
      return res
        .clearCookie("jwt", {
          httpOnly: true,
          sameSite: "None",
          secure: true,
        })
        .sendStatus(204); //NO CONTENT, but successful, and clear Cookie.
    })
    .catch(() => {
      if (errors) {
        errorHandler(errors);
      }
      return res
        .clearCookie("jwt", {
          httpOnly: true,
          sameSite: "None",
          secure: true,
        })
        .sendStatus(statusCode);
    });
});

module.exports = router;

const express = require("express");
const router = express.Router();
const dotenv = require("dotenv").config();
const mysql = require("mysql");
const dbConfig = require("../config/dbConfig");
const nodemailer = require("nodemailer");
const bcrypt = require("bcrypt");
const jwt = require("jsonwebtoken");

//Custom middleware.
const { errorHandler, resultHandler } = require("../middleware/logHandler");

router.post("/", (req, res) => {
  const loginEmail = req.body.email;
  const loginPassword = req.body.password;

  let errors = "";
  let statusCode = null;
  let clientError = "";
  let queryData = [];
  let refreshToken;
  let accessToken;

  function handleLostConnectionDB(db) {
    db.on("error", (err) => {
      if (err.code === "PROTOCOL_CONNECTION_LOST") {
        db.end();
        errorHandler(err.message);
        return res.sendStatus(503); //SERVICE UNAVAILABLE
      }
    });
  }

  let handleLogin = new Promise((resolve, reject) => {
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
          "SELECT ID, FirstName, Email, Password, Roles, LoginIP FROM tbl_users WHERE Email = ?",
          [loginEmail],
          (err, result) => {
            if (err) {
              db.end();
              errors = err.message;
              statusCode = 503; //SERVICE UNAVAILABLE
              reject();
            } else if (result) {
              if (!result.length) {
                db.end();
                statusCode = 403; //UNAUTHORIZED, because email submitted does not exist in the DB.
                clientError = "Email not registered!";
                reject();
              } else {
                queryData = result[0];
                resolve(db);
              }
            }
          }
        );
      });
    })
    .then((db) => {
      return new Promise((resolve, reject) => {
        if (queryData.LoginIP === "Email not verified") {
          db.end();

          //Re-send email verification link.
          async function emailVerificationLink() {
            //Generate test SMTP service account from ethereal.email
            let testAccount = await nodemailer.createTestAccount();

            //Create reusable transporter object using the default SMTP transport.
            const transporter = nodemailer.createTransport({
              host: "smtp.ethereal.email",
              port: 587,
              auth: {
                user: "kaci8@ethereal.email",
                pass: "YbvRHZcqQ8SMm8kQY7",
              },
            });

            let mailOptions = {
              from: '"Invezat" <no-reply@invezat.com>',
              to: loginEmail,
              subject: "Invezat - Email Verification",
              text:
                "Good day " +
                queryData.FirstName +
                ", and welcome to Invezat! " +
                "\n\n" +
                "To complete your registration, please copy the following link and open it in your browser to verify your email address and set up your new password: " +
                "\n\n" +
                "http://localhost:3000/emailVerification/",
              html:
                "Good day <b>" +
                queryData.FirstName +
                "</b>, and welcome to Invezat! " +
                "<br><br>" +
                "To complete your registration, please click on the following link to verify your email address and set up your new password: " +
                "<br><br>" +
                "<a href='http://localhost:3000/emailVerification/' target='_blank'>Verify Email Adress</a>",
            };

            //Send email with defined transport object.
            await transporter.sendMail(mailOptions, function (error, info) {
              if (error) {
                errorHandler("Nodemailer " + error);
              } else {
                // Preview only available when sending through an Ethereal account
                console.log("Preview URL: ", nodemailer.getTestMessageUrl(info));
              }
            });
          }
          emailVerificationLink();

          statusCode = 403; //UNAUTHORIZED, because email submitted has not been verified.
          clientError = "Email not verified! Please check your email for a verification link!";
          reject();
        } else {
          resolve(db);
        }
      });
    })
    /*.then((db) => {
      return new Promise((resolve, reject) => {
        //Check request IP against stored IP addresses.
        //If match not found either: warn user via email, but allow login anyway, or, block login until user allows via link send to email (for this option, store a string that blocks login attempts in DB until access has been allowed).
        }
      });
    })*/
    .then((db) => {
      return new Promise(async (resolve, reject) => {
        if (await bcrypt.compare(loginPassword, queryData.Password)) {
          refreshToken = jwt.sign(
            {
              id: queryData.ID,
            },
            process.env.refresh_Token_Secret,
            { expiresIn: "12h" }
          );

          db.query(
            "UPDATE tbl_users SET LoginRefreshToken = ? WHERE ID = ?",
            [refreshToken, queryData.ID],
            (err, result) => {
              if (err) {
                db.end();
                errors = err.message;
                statusCode = 503; //SERVICE UNAVAILABLE
                reject();
              } else if (result) {
                db.end();
                accessToken = jwt.sign(
                  {
                    UserInfo: { id: queryData.ID, roles: queryData.Roles },
                  },
                  process.env.access_Token_Secret,
                  { expiresIn: "10m" }
                );
                resolve();
              }
            }
          );
        } else {
          db.end();
          statusCode = 403; //UNAUTHORIZED, because email or password entered was incorrect.
          clientError = "Incorrect email or password!";
          reject();
        }
      });
    })
    .then(() => {
      resultHandler("Successful login!");

      return res
        .cookie("jwt", refreshToken, {
          httpOnly: true,
          sameSite: "None",
          secure: true,
          maxAge: 12 * 60 * 60 * 1000,
        })
        .json({ accessToken });
    })
    .catch(() => {
      if (errors) {
        errorHandler(errors);
      }
      if (statusCode) {
        if (statusCode === 403) {
          return res.status(statusCode).json({ message: clientError });
        } else {
          return res.sendStatus(statusCode);
        }
      }
    });
});

module.exports = router;

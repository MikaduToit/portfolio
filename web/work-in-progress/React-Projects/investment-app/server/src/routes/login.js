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
    let db = mysql.createConnection(dbConfig.config);

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
        db.query("SELECT ID, Password, Roles, LoginIPs, VerificationToken FROM tbl_users WHERE Email = ?", [loginEmail], (err, result) => {
          if (err) {
            db.end();
            errors = err.message;
            statusCode = 503; //SERVICE UNAVAILABLE
            reject();
          } else if (result) {
            if (!result.length) {
              db.end();
              statusCode = 403; //FORBIDDEN, because email submitted does not exist in the DB.
              clientError = "Email not registered!";
              reject();
            } else {
              queryData = result[0];
              resolve(db);
            }
          }
        });
      });
    })
    .then((db) => {
      return new Promise((resolve, reject) => {
        //Check that the user's email has been verified.
        if (!queryData.Password) {
          db.end();

          //Re-send email verification link.
          async function sendVerifyEmailLink() {
            //Generate test SMTP service account from ethereal.email
            let testAccount = await nodemailer.createTestAccount();
            let link = "http://localhost:3000/user-registration/change-password?verification=" + queryData.VerificationToken;

            //Create reusable transporter object using the default SMTP transport.
            const transporter = nodemailer.createTransport({
              host: "smtp.ethereal.email",
              port: 587,
              auth: {
                user: "granville.cartwright19@ethereal.email",
                pass: "5ad4V3vd6zr5YaYP3E",
              },
            });

            let mailOptions = {
              from: '"Invezat" <no-reply@invezat.com>',
              to: loginEmail,
              subject: "Invezat - Email Verification",
              text:
                "Good day!" +
                "\n\n" +
                "Welcome to Invezat!" +
                "\n" +
                "You have been registered on our app which makes keeping track of all your investments a piece of cake." +
                "\n\n" +
                "Please visit the link below to complete your registration process." +
                "\n\n" +
                link,
              html:
                "Good day!" +
                "<br><br>" +
                "Welcome to Invezat!" +
                "<br>" +
                "You have been registered on our app which makes keeping track of all your investments a piece of cake." +
                "<br><br>" +
                "Please visit the link below to complete your registration process." +
                "<br><br>" +
                `<a href=${link} target='_blank'>Change Password</a>`,
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
          sendVerifyEmailLink();

          statusCode = 403; //FORBIDDEN, because email submitted has not been verified.
          clientError = "Email not verified!\nPlease check your email for a verification link!";
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

          db.query("UPDATE tbl_users SET LoginRefreshToken = ? WHERE ID = ?", [refreshToken, queryData.ID], (err, result) => {
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
          });
        } else {
          db.end();
          statusCode = 403; //FORBIDDEN, because email or password entered was incorrect.
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

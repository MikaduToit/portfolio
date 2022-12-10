const express = require("express");
const router = express.Router();
const mysql = require("mysql");
const dbConfig = require("../config/dbConfig");
const jwt = require("jsonwebtoken");
const dotenv = require("dotenv").config();
const nodemailer = require("nodemailer");

//Custom middleware.
const { errorHandler } = require("../middleware/logHandler");

router.post("/", (req, res) => {
  const email = req.body.email;

  let errors = "";
  let statusCode = null;
  let clientError = "";
  let verificationToken;

  function handleLostConnectionDB(db) {
    db.on("error", (err) => {
      if (err.code === "PROTOCOL_CONNECTION_LOST") {
        db.end();
        errorHandler(err.message);
        return res.sendStatus(503); //SERVICE UNAVAILABLE
      }
    });
  }

  let handleForgotPassword = new Promise((resolve, reject) => {
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
        db.query("SELECT null FROM tbl_users WHERE Email = ?", [email], (err, result) => {
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
              resolve(db);
            }
          }
        });
      });
    })
    .then((db) => {
      return new Promise((resolve, reject) => {
        verificationToken = jwt.sign(
          {
            type: "change password",
            email: email,
          },
          process.env.verification_Token_Secret,
          { expiresIn: "24h" }
        );

        db.query("UPDATE tbl_users SET VerificationToken = ? WHERE Email = ?", [verificationToken, email], (err, result) => {
          if (err) {
            db.end();
            errors = err.message;
            statusCode = 503; //SERVICE UNAVAILABLE
            reject();
          } else if (result) {
            db.end();
            resolve();
          }
        });
      });
    })
    .then(() => {
      return new Promise((resolve, reject) => {
        //Send email with link to change password.
        async function sendForgotPasswordLink() {
          //Generate test SMTP service account from ethereal.email
          let testAccount = await nodemailer.createTestAccount();
          let link = "http://localhost:3000/forgot-password/change-password?verification=" + verificationToken;

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
            to: email,
            subject: "Invezat - Forgot your password?",
            text:
              "Good day!" +
              "\n\n" +
              "We received a request to change the password for " +
              email +
              " because the existing password was forgotten." +
              "\n\n" +
              "If this was you, please continue the process by navigating to the link below and following the instructions there." +
              "\n\n" +
              link +
              "\n\n\n" +
              "If on the otherhand you did not request a password change, feel free to ignore this email, or contact our support team should you have any security concerns.",
            html:
              "Good day!" +
              "<br><br>" +
              "We received a request to change the password for <b>" +
              email +
              "</b> because the existing password was forgotten." +
              "<br><br>" +
              "If this was you, please continue the process by navigating to the link below and following the instructions there." +
              "<br><br>" +
              `<a href=${link} target='_blank'>Change Password</a>` +
              "<br><br><br>" +
              "If on the otherhand you did not request a password change, feel free to ignore this email, or contact our support team should you have any security concerns.",
          };

          //Send email with defined transport object.
          await transporter.sendMail(mailOptions, function (error, info) {
            if (error) {
              errors = "Nodemailer " + error;
              statusCode = 500; //INTERNAL SERVER ERROR
              clientError = "Email could not be sent!\nPlease try again later or contact support!";
              reject();
            } else {
              // Preview only available when sending through an Ethereal account
              console.log("Preview URL: ", nodemailer.getTestMessageUrl(info));
              resolve();
            }
          });
        }
        sendForgotPasswordLink();
      });
    })
    .then(() => {
      return res.status(200).json({ message: "Please check your email for instructions to complete this process." });
    })
    .catch(() => {
      if (errors) {
        errorHandler(errors);
      }
      if (statusCode) {
        if (statusCode === 403 || statusCode === 500) {
          return res.status(statusCode).json({ message: clientError });
        } else {
          return res.sendStatus(statusCode);
        }
      }
    });
});

module.exports = router;

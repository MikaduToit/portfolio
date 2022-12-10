const express = require("express");
const router = express.Router();
const mysql = require("mysql");
const dbConfig = require("../config/dbConfig");
const jwt = require("jsonwebtoken");
const dotenv = require("dotenv").config();
const bcrypt = require("bcrypt");
const nodemailer = require("nodemailer");

//Custom middleware.
const { errorHandler } = require("../middleware/logHandler");

router.post("/", (req, res) => {
  const body = req.body;
  let verificationJWT = "";
  let id = "";
  let oldPassword = "";
  let newPassword = "";

  let errors = "";
  let statusCode = null;
  let clientError = "";
  let queryData = [];

  //Functions...
  function handleLostConnectionDB(db) {
    db.on("error", (err) => {
      if (err.code === "PROTOCOL_CONNECTION_LOST") {
        db.end();
        errorHandler(err.message);
        return res.sendStatus(503); //SERVICE UNAVAILABLE
      }
    });
  }

  function unknownPassword() {
    jwt.verify(verificationJWT, process.env.verification_Token_Secret, (err, decoded) => {
      if (err) {
        return res.status(403).json({ message: "This request is no longer valid!\nPlease make a new password change request!" }); //FORBIDDEN, because verification of the JWT failed.
      } else {
        if (decoded.type !== "change password") {
          return res.status(403).json({ message: "This request is not valid!\nPlease make a new password change request!" }); //FORBIDDEN, because the JWT is the wrong type.
        }

        let handleUnknownPassword = new Promise((resolve, reject) => {
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
              db.query("SELECT null FROM tbl_users WHERE Email = ? AND VerificationToken = ?", [decoded.email, verificationJWT], (err, result) => {
                if (err) {
                  db.end();
                  errors = err.message;
                  statusCode = 503; //SERVICE UNAVAILABLE
                  reject();
                } else if (result) {
                  if (!result.length) {
                    db.end();
                    statusCode = 403; //FORBIDDEN, because email + verificationJWT combination does not exist in the DB.
                    clientError = "This request is not valid!\nPlease make a new password change request!";
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
              //Clear the VerificationToken field in the DB.
              db.query("UPDATE tbl_users SET VerificationToken = ? WHERE Email = ?", ["", decoded.email], (err, result) => {
                if (err) {
                  resolve(db);
                } else if (result) {
                  resolve(db);
                }
              });
            });
          })
          .then((db) => {
            //Verification complete, time to change the password.
            return changePassword(db, decoded.email);
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
      }
    });
  }

  function rememberedPassword() {
    let handleRememberedPassword = new Promise((resolve, reject) => {
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
          db.query("SELECT Email, Password FROM tbl_users WHERE ID = ?", [id], (err, result) => {
            if (err) {
              db.end();
              errors = err.message;
              statusCode = 503; //SERVICE UNAVAILABLE
              reject();
            } else if (result) {
              if (!result.length) {
                db.end();
                statusCode = 403; //FORBIDDEN, because id does not exist in the DB.
                clientError = "User ID does not exist!";
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
        return new Promise(async (resolve, reject) => {
          if (await bcrypt.compare(oldPassword, queryData.Password)) {
            resolve(db);
          } else {
            db.end();
            statusCode = 403; //FORBIDDEN, because password entered was incorrect.
            clientError = "Password is incorrect!";
            reject();
          }
        });
      })
      .then((db) => {
        //Verification complete, time to change the password.
        return changePassword(db, queryData.Email);
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
  }

  function changePassword(db, email) {
    let handleChangePassword = new Promise(async (resolve, reject) => {
      const hashedPassword = await bcrypt.hash(newPassword, 10);

      db.query("UPDATE tbl_users SET Password = ? WHERE Email = ?", [hashedPassword, email], (err, result) => {
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
    })
      .then(() => {
        return new Promise((resolve, reject) => {
          //Send email with link to change password.
          async function sendSuccessfulPasswordChange() {
            //Generate test SMTP service account from ethereal.email
            let testAccount = await nodemailer.createTestAccount();

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
              subject: "Invezat - Password successfully changed.",
              text:
                "Good day!" +
                "\n\n" +
                "We are sending this mail to notify you that the password for " +
                email +
                " was successfully changed. Please feel free to login using your new password." +
                "\n\n" +
                "If this was not you, please contact our support team immediately.",
              html:
                "Good day!" +
                "<br><br>" +
                "We are sending this mail to notify you that the password for <b>" +
                email +
                "</b> was successfully changed. Please feel free to login using your new password." +
                "<br><br>" +
                "If this was not you, please contact our support team immediately.",
            };

            //Send email with defined transport object.
            await transporter.sendMail(mailOptions, function (error, info) {
              if (error) {
                errors = "Nodemailer " + error;
                errorHandler(errors);
                resolve();
              } else {
                // Preview only available when sending through an Ethereal account
                console.log("Preview URL: ", nodemailer.getTestMessageUrl(info));
                resolve();
              }
            });
          }

          sendSuccessfulPasswordChange();
        });
      })
      .then(() => {
        return res.status(200).json({ message: "Password successfully changed." });
      })
      .catch(() => {
        if (errors) {
          errorHandler(errors);
        }
        if (statusCode) {
          return res.sendStatus(statusCode);
        }
      });
  }

  if (body.verificationJWT) {
    verificationJWT = body.verificationJWT;
    newPassword = body.newPassword;

    unknownPassword();
  } else if (body.oldPassword) {
    id = body.id;
    oldPassword = body.oldPassword;
    newPassword = body.newPassword;

    rememberedPassword();
  }
});

module.exports = router;

const express = require("express");
const router = express.Router();
const mysql = require("mysql");
const dbConfig = require("../config/dbConfig");
const jwt = require("jsonwebtoken");
const dotenv = require("dotenv").config();
const nodemailer = require("nodemailer");

//Custom middleware.
const verifyRoles = require("../middleware/verifyRoles");
const { errorHandler } = require("../middleware/logHandler");

//Custom Middleware (Roles Authorization)
router.use(verifyRoles([1010, 1005]));

//Route
router.post("/", (req, res) => {
  const id = req.id;
  let body = req.body;

  let errors = "";
  let statusCode = null;
  let clientError = "";
  let verificationToken;

  function cleanUserInput() {
    const keys = Object.keys(body);
    keys.forEach((key) => {
      if (typeof body[key] === "string") {
        body[key] = body[key].replace(/\s+/g, " ").trim();
      }
    });
  }
  cleanUserInput();

  function handleLostConnectionDB(db) {
    db.on("error", (err) => {
      if (err.code === "PROTOCOL_CONNECTION_LOST") {
        db.end();
        errorHandler(err.message);
        return res.sendStatus(503); //SERVICE UNAVAILABLE
      }
    });
  }

  let handleUserRegistration = new Promise((resolve, reject) => {
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
        db.query("SELECT null FROM tbl_users WHERE Email = ?", [body.email], (err, result) => {
          if (err) {
            db.end();
            errors = err.message;
            statusCode = 503; //SERVICE UNAVAILABLE
            reject();
          } else if (result) {
            if (result.length) {
              db.end();
              statusCode = 403; //FORBIDDEN, because email submitted already exists in the DB.
              clientError = "A user with this email has already been registered!";
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
        let phoneNumber;
        let roles;

        verificationToken = jwt.sign(
          {
            type: "change password",
            email: body.email,
          },
          process.env.verification_Token_Secret,
          { expiresIn: "24h" }
        );

        if (body.phoneNumber[1]) {
          phoneNumber = body.phoneNumber[0].value.split(" ")[1] + " " + body.phoneNumber[1];
        } else {
          phoneNumber = "";
        }

        roles = body.roles.map((role) => {
          return role.value;
        });
        roles = roles.sort().reverse().toString();

        const userData = [
          body.firstName,
          body.lastName,
          body.address,
          body.city,
          body.provinceOrState,
          body.country,
          body.postalCode,
          phoneNumber,
          body.email,
          "",
          roles,
          new Date(),
          id,
          null,
          null,
          null,
          verificationToken,
          "DefaultPP.png",
        ];

        db.query(
          "INSERT INTO tbl_users (FirstName, LastName, Address, City, ProvinceOrState, Country, PostalCode, PhoneNumber, Email, Password, Roles, RegistrationDate, RegisteredByID, LastLogin, LoginIPs, LoginRefreshToken, VerificationToken, ProfilePicture) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
          userData,
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
      return new Promise((resolve, reject) => {
        //Send email with link to change password.
        async function sendVerifyEmailLink() {
          //Generate test SMTP service account from ethereal.email
          let testAccount = await nodemailer.createTestAccount();
          let link = "http://localhost:3000/user-registration/change-password?verification=" + verificationToken;

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
            to: body.email,
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
        sendVerifyEmailLink();
      });
    })
    .then(() => {
      return res.status(200).json({ message: "User successfully registered." });
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

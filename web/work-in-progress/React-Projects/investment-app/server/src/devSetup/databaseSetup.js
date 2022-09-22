const express = require("express");
const router = express.Router();
const mysql = require("mysql");
const dbConfig = require("../config/dbConfig");
const bcrypt = require("bcrypt");

//Custom middleware.
const { errorHandler, resultHandler } = require("../middleware/logHandler");

const databaseSetup = () => {
  let errors = "";
  let results = "";

  function handleLostConnectionDB(db) {
    db.on("error", (err) => {
      if (err.code === "PROTOCOL_CONNECTION_LOST") {
        db.end();
        errorHandler(err.message);
        return console.log("Database setup error: " + err.message);
      }
    });
  }

  let handleDatabaseSetup = new Promise((resolve, reject) => {
    let db = mysql.createConnection(dbConfig);

    db.connect((err) => {
      if (err) {
        errors = err.message;
        reject(db);
      } else {
        handleLostConnectionDB(db);
        resolve(db);
      }
    });
  })
    .then((db) => {
      return new Promise((resolve, reject) => {
        db.query(
          'CREATE TABLE `tbl_users` (`ID` int NOT NULL AUTO_INCREMENT, `FirstName` varchar(255) NOT NULL, `LastName` varchar(255) NOT NULL, `Address` varchar(255) NOT NULL, `City` varchar(255) NOT NULL, `ProvinceOrState` varchar(255) NOT NULL, `Country` varchar(255) NOT NULL, `PostalCode` varchar(255) NOT NULL, `PhoneNumber` varchar(255) DEFAULT NULL, `Email` varchar(255) NOT NULL, `Password` varchar(255) NOT NULL, `Roles` varchar(255) NOT NULL, `RegistrationDate` datetime NOT NULL, `RegisteredByID` int NOT NULL, `LastLogin` datetime DEFAULT NULL, `LoginRefreshToken` varchar(255) DEFAULT NULL, `LoginIP` varchar(255) DEFAULT NULL, `ProfilePicture` varchar(255) NOT NULL DEFAULT "DefaultPP.png", PRIMARY KEY (`ID`), UNIQUE KEY `Email_UNIQUE` (`Email`)) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci',
          (err, result) => {
            if (err) {
              errors = err.message;
              reject(db);
            } else if (result) {
              results = "Users table created! ";
              resolve(db);
            }
          }
        );
      });
    })
    .then((db) => {
      return new Promise(async (resolve, reject) => {
        const hashedPassword = await bcrypt.hash("#Admin00", 10);

        const adminUserData = [
          "admin",
          "admin",
          "admin",
          "admin",
          "admin",
          "admin",
          "0000",
          "+0000000000",
          "admin@admin",
          hashedPassword,
          "1010",
          new Date(),
          1,
          null,
          null,
          null,
          "DefaultPP.png",
        ];

        db.query(
          "INSERT INTO tbl_users (FirstName, LastName, Address, City, ProvinceOrState, Country, PostalCode, PhoneNumber, Email, Password, Roles, RegistrationDate, RegisteredByID, LastLogin, LoginRefreshToken, LoginIP, ProfilePicture) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
          adminUserData,
          (err, result) => {
            if (err) {
              errors = err.message;
              reject(db);
            } else if (result) {
              results = results + "Admin user created! Login credentials: Email = admin@admin | Password = #Admin00";
              resolve(db);
            }
          }
        );
      });
    })
    .then((db) => {
      db.end();
      resultHandler(results);
      return console.log("Database setup results: " + results);
    })
    .catch((db) => {
      db.end();
      if (errors) {
        errorHandler(errors);
      }
      if (results) {
        resultHandler(results);
      }
      return console.log("Database setup error: " + errors + "\n" + "Database setup results: " + results);
    });
};

module.exports = databaseSetup;

//  This function allows you to trigger a reconnection attempt every time connection is lost. The delay prevents a hot loop.
//  function handleReconnection() {
//    let db = mysql.createConnection(dbConfig);
//
//    db.on("error", function (err) {
//      if (err.code === "PROTOCOL_CONNECTION_LOST") {
//        setTimeout(handleReconnection, 2000);
//      }
//    });
//  }

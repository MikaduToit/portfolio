const { format } = require("date-fns");
const { v4: uuid } = require("uuid");
const fs = require("fs");
const fsPromises = require("fs").promises;
const path = require("path");

//Event logging to file.
const logEvents = async (type, message, logLocation) => {
  const dateTime = `${format(new Date(), "yyyy/MM/dd\tHH:mm:ss")}`;
  const event = `${dateTime}\t${uuid()}\t${type}\t${message}\n`;
  try {
    if (!fs.existsSync(path.join(__dirname, "..", "logs"))) {
      await fsPromises.mkdir(path.join(__dirname, "..", "logs"));
    }
    await fsPromises.appendFile(
      path.join(__dirname, "..", "logs", logLocation),
      event
    );
  } catch (err) {
    console.log(err);
  }
};

//Logs automated server errors.
const serverErrorHandler = (err, req, res, next) => {
  logEvents(
    "",
    `${req.method}\t${req.url}\t${err.name}: ${err.message}`,
    "errorLog.txt"
  );
  res.sendStatus(500);
  next();
};

//Logs request events.
const requestHandler = (req, res, next) => {
  logEvents(
    `REQUEST\t`,
    `${req.method}\t${req.url}\t${req.origin}`,
    "eventLog.txt"
  );
  next();
};

//Logs manual server errors.
const errorHandler = (message) => {
  logEvents("", message, "errorLog.txt");
};

//Logs manual event results.
const resultHandler = (message) => {
  logEvents(`RESULT\t`, message, "eventLog.txt");
};

module.exports = {
  serverErrorHandler,
  requestHandler,
  errorHandler,
  resultHandler,
};

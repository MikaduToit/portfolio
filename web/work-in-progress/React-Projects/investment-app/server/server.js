//Every app.use middleware that comes before another app.use, will execute before that app.use and therefore influence it.

const express = require("express");
const cors = require("cors");
const dotenv = require("dotenv").config();
const allowCredentials = require("./src/middleware/allowCredentials");
const corsOptions = require("./src/config/corsOptions");
const cookieParser = require("cookie-parser");
const { serverErrorHandler, requestHandler, resultHandler } = require("./src/middleware/logHandler");
const verifyJWT = require("./src/middleware/verifyJWT");

//devSetup
/*const databaseSetup = require("./src/devSetup/databaseSetup");
databaseSetup();*/

const app = express();
const port = process.env.port;

//Custom Middleware...
app.use(requestHandler);
app.use(allowCredentials); //Access-Control-Allow-Credentials

//Cross Origin Resource Sharing.
app.use(cors(corsOptions));

//Built-in Middleware...
app.use(express.json());
app.use(cookieParser());

//Requests...
app.get("/", (req, res) => {
  res.send(`Welcome! Server running on Port ${port}!`);
}); //Production Note: Restrict access.

//Routes...
app.use("/preLoadCheck", require("./src/routes/preLoadCheck"));
app.use("/login", require("./src/routes/login"));
app.use("/refreshAccessToken", require("./src/routes/refreshAccessToken"));
app.use("/logout", require("./src/routes/logout"));

//Custom Middleware (Authorization).
app.use(verifyJWT);

//Protected Routes...
app.use("/protectedRequestTest", require("./src/routes/protectedRequestTest"));

//Custom Middleware...
app.use(serverErrorHandler);

app.listen(port, () => {
  resultHandler(`Server started on Port ${port}!`);
  console.log(`Server started on Port ${port}!`);
});

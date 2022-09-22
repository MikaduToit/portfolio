const dotenv = require("dotenv").config();

const dbConfig = {
  host: process.env.db_Host,
  user: process.env.db_User,
  password: process.env.db_Password,
  database: process.env.db_Database,
};

module.exports = dbConfig;

const dotenv = require("dotenv").config();

const connection = {
  host: process.env.db_Host,
  user: process.env.db_User,
  password: process.env.db_Password,
};

const config = {
  host: process.env.db_Host,
  user: process.env.db_User,
  password: process.env.db_Password,
  database: process.env.db_Database,
};

module.exports = { connection, config };

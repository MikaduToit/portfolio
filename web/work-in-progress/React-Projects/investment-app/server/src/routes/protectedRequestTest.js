const express = require("express");
const router = express.Router();

router.get("/", (req, res) => {
  res.send({
    message: "ID: " + req.id + " Roles: " + req.roles + " Message: Protected Request Test Passed",
  });
});

module.exports = router;

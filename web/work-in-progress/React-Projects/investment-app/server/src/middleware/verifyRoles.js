const verifyRoles = (allowedRoles) => {
  return (req, res, next) => {
    if (!req?.roles) return res.status(403).json({ message: "You do not have the required authorization to make this request!" }); //FORBIDDEN

    //Check that at least one of the the roles recieved through the request, matches the roles authorized for the request.
    const matchingRoles = allowedRoles.filter((value) => req.roles.includes(value));
    if (!matchingRoles.length) return res.status(403).json({ message: "You do not have the required authorization to make this request!" }); //FORBIDDEN

    next();
  };
};

module.exports = verifyRoles;

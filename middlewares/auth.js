const jwt = require('jsonwebtoken');

function createAuthMiddleware(secret){
  return function authenticate(req, res, next){
    const header = req.headers['authorization'];
    if(!header) return res.status(401).json({ message: 'Missing token' });
    const token = header.split(' ')[1];
    try{
      req.user = jwt.verify(token, secret);
      next();
    }catch(err){
      res.status(401).json({ message: 'Invalid token' });
    }
  };
}

module.exports = { createAuthMiddleware };

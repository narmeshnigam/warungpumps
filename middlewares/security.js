const helmet = require('helmet');
const rateLimit = require('express-rate-limit');

function setupSecurity(app){
  app.use(helmet());
  const generalLimiter = rateLimit({
    windowMs: 15 * 60 * 1000,
    max: 100,
    standardHeaders: true,
    legacyHeaders: false
  });
  app.use(generalLimiter);
}

function createLoginLimiter(){
  return rateLimit({
    windowMs: 15 * 60 * 1000,
    max: 5,
    message: { success: false, message: 'Too many login attempts. Try again later.' }
  });
}

module.exports = { setupSecurity, createLoginLimiter };

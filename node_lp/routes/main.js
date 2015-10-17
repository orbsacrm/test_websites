/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 */
'use strict';

let _ = require('lodash')
  , express = require('express')
  , conf = require('./../conf')
  , log = require('./../lib/log')
  , Client = require('./../lib/client');

let client = new Client(conf.orbsa);
let app = new express.Router();

// middleware to get offer info
app.use((req,res,next) => {
  client.query(`GET /offer/${conf.orbsa.offer}`,(e,r) => {
    if(e) return next(e);
    res.locals.offer = r;
    next();
  });
});

// middleware to get upsell offer info
if(conf.orbsa.upsell.enabled){
  app.use((req,res,next) => {
    client.query(`GET /offer/${conf.orbsa.upsell.offer}`,(e,r) => {
      if(e) return next(e);
      res.locals.upsell = r;
      next();
    });
  });
}

// middleware to get customer info
app.use((req,res,next) => {
  if(req.session.customer_id){
    client.query(`GET /customer/${req.session.customer_id}`,(e,r) => {
      if(e) return next(e);
      res.locals.customer = r;
      next();
    });
  } else{
    next();
  }
});

// make sure people can't just get to the payment page
let internal = (req,res,next) => {
  if(!req.session.customer_id || !res.locals.customer){
    return res.redirect('/');
  }
  next();
};

// first page
app.get('/',(req,res) => {
  let data = {
    title: 'Landing page',
  };
  req.session.destroy();
  res.render('lander.hbs',data);
});

// first page submit (create customer)
app.post('/',(req,res,next) => {
  client.query('POST /customer/create',req.body,(e,r) => {
    if(e) return next(e);
    req.session.customer_id = r;
    res.json({ok:true});
  });
});

// payment page
app.get('/payment',internal,(req,res) => {
  let data = {
    title: 'Payment',
  };
  res.render('payment.hbs',data);
});

// payment page submit (purchase offer)
app.post('/payment',internal,(req,res,next) => {
  let packet = {
    customer: req.session.customer_id,

    // this will update billing info then do the purchase in one request
    billing: _.clone(req.body),
  };

  let selected_offer = req.body.offer || conf.orbsa.offer;

  client.query(`POST /offer/${selected_offer}/purchase`,packet,(e/*, r*/) => {
    if(e) return next(e);
    res.json({ok:true});
  });
});

// thanks
app.get('/thanks',internal,(req, res) => {
  let data = {
    title: 'Thanks',
  };
  res.render('thanks.hbs',data);
});

// upsell accept click (on thank you page)
if(conf.orbsa.upsell.enabled){
  app.post('/thanks/upsell',internal,(req,res,next) => {

    // the customer already has billing info at this point
    let packet = {
      customer: req.session.customer_id,
    };

    let selected_offer = conf.orbsa.upsell.offer;

    client.query(`POST /offer/${selected_offer}/purchase`, packet, (e/*, r*/) => {
      if(e) return next(e);
      res.json({ok:true});
    });
  });
}

module.exports = app;

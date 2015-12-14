/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 */
'use strict';
let Client = require('./../lib/client');
let _ = require('lodash');
let conf = require('./../conf');
let express = require('express');

let client = new Client(conf.orbsa);
let app = new express.Router();

// get all offers
app.use((req,res,next) => {
  client.query(`GET /offer?per_page=100`, (err, r) => {
    if(err) return next(err);

    if(!Array.isArray(r.items)) r.items = [r.items];
    res.locals.offers = (r.items || []).map(function(offer) {
      if(offer.type === 'cycle') {
        offer.price = offer.cycle.cycle_amount_dollars + ' / ' +
          offer.cycle.cycle_seconds_human;
      } else {
        offer.price = offer.flat.price;
      }

      var productWithPhoto = _.find(offer.product_docs, function(product) {
        return product.photos[0];
      });

      if(productWithPhoto) {
        offer.photo_url =
          'http://' + conf.orbsa.url + productWithPhoto.photos[0]['200x200'];
      }

      return offer;
    });

    next();
  });
});

// home
app.get('/',(req,res) => {
  let data = {
    title: 'Welcome',
  };
  res.render('home.hbs', data);
});

// cart
app.get('/cart',(req,res) => {
  let data = {
    title: 'Cart',
    items: req.session.items || [
      {
        name: "Product 1",
        description: "Product 1 is a great solution for everything",
        photo_url: 'http://placehold.it/100x70',
        price: 100
      },
      {
        name: "Product 2",
        description: "Product 2 is another great solution for everything",
        photo_url: 'http://placehold.it/100x70',
        price: 10
      },
    ],
  };

  res.render('cart.hbs', data);
});

// product
app.get('/product',(req,res) => {
  let data = {
    title: 'Product',
  };

  res.render('product.hbs',data);
});

app.post('/product/:id/purchase', (req, res) => {
  if(!req.session.items) {
    req.session.items = [];
  }

  var offer = _.find(res.locals.offers, { _id: req.params.id });
  req.session.items.push(offer);

  res.redirect('/browse');
});


// browse
app.get('/browse', (req, res) => {
  let data = {
    title: 'Products',
  };

  res.render('browse.hbs', data);
});

module.exports = app;

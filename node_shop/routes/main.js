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
    items: req.session.items,
  };

  res.render('cart.hbs', data);
});

app.post('/cart/add_product/:id', (req, res) => {
  if(!req.session.items) {
    req.session.items = [];
  }

  var offer = _.find(res.locals.offers, { _id: req.params.id });
  req.session.items.push(offer);

  res.redirect('/browse');
});

app.post('/cart/remove_product/:id',(req,res) => {
  console.log(req.session.items);
  req.session.items = _.filter(req.session.items, (p) => {
    return p._id !== req.params.id;
  });

  res.redirect('/cart');
});

app.get('/product/:id', (req, res) => {
  let data = _.extend({
    title: 'Product'
  }, _.find(res.locals.offers, { _id: req.params.id }));

  res.render('product.hbs', data);
});

// browse
app.get('/browse', (req, res) => {
  let data = {
    title: 'Products',
  };

  res.render('browse.hbs', data);
});

app.get('/checkout', (req, res) => {
  res.render('checkout.hbs');
});

app.post('/checkout', (req, res, next) => {
  let body = _.reduce(req.body, (memo, value, key) => {
    if(_.startsWith(key, 'billing.')) {
      key = key.slice('billing.'.length);
      memo.billing[key] = value;
    }
    memo.customer[key] = value;
    return memo;
  }, {
    billing: {},
    customer: {},
  });

  client.query(`POST /customer/create`, body.customer, (err, customer) => {
    if(err) return next(err);
    client.query(`POST /customer/${customer._id}/billing/update`, body.billing, (err, orbsaRes) => {
      if(err) return next(err);
      if(orbsaRes) {
        let bulkBody = {
          customer: customer._id,
          offers: _.pluck(req.session.items, '_id'),
        };

        client.query(`POST /offer/purchase_bulk`, bulkBody, (err) => {
          if(err) return next(err);
          return res.redirect('/checkout_success');
        });
      }
    });
  });
});

module.exports = app;

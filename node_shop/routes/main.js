/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 */
'use strict'

let
  _ = require('lodash')
  , express = require('express')
  , conf = require('./../conf')
  , log = require('./../lib/log')
  , Client = require('./../lib/client')
;

let client = new Client(conf.orbsa)
let app = new express.Router()

// get all offers
app.use((req,res,next) => {
  client.query(`GET /offer?per_page=100`,(e,r) => {
    res.locals.offers = r.items || []
    next()
  })
})

// home
app.get('/',(req,res) => {
  let data = {
    title: 'Welcome',
  }
  res.render('home.hbs',data)
})

// cart
app.get('/cart',(req,res) => {
  let data = {
    title: 'Cart',
  }
  res.render('cart.hbs',data)
})

// product
app.get('/product',(req,res) => {
  let data = {
    title: 'Product',
  }
  res.render('product.hbs',data)
})

// browse
app.get('/browse',(req,res) => {
  let data = {
    title: 'Products',
  }
  res.render('browse.hbs',data)
})

module.exports = app


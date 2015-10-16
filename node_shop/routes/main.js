/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 */
'use strict'

let
  _ = require('lodash')
  , express = require('express')
  , conf = require('./../conf')
  , log = require('./../lib/log')
  , orbsa = require('./../lib/orbsa') 

let app = new express.Router()

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
    items: [],
  }

  while(data.items.length<10)
    data.items.push(Math.random())

  res.render('browse.hbs',data)
})

module.exports = app


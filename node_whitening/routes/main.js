/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 */
'use strict'

require('date-utils')

let
  _ = require('lodash')
  , express = require('express')
  , moment = require('moment')
  , conf = require('./../conf')
  , log = require('./../lib/log')
  , Client = require('./../lib/client') 

let client = new Client(conf.orbsa)
let app = new express.Router()

// middleware to get offer info
app.use((req,res,next) => {
  client.query(`GET /offer/${conf.orbsa.offer}`,(e,r) => {
    if(e) return next(e)
    res.locals.offer = r
    next()
  })
})

// middleware to get upsell offer info
if(conf.orbsa.upsell.enabled){
  app.use((req,res,next) => {
    client.query(`GET /offer/${conf.orbsa.upsell.offer}`,(e,r) => {
      if(e) return next(e)
      res.locals.upsell = r
      next()
    })
  })
}

// middleware to get customer info
app.use((req,res,next) => {
  if(req.session.customer_id){
    client.query(`GET /customer/${req.session.customer_id}`,(e,r) => {
      if(e) return next(e)
      res.locals.customer = r
      next()
    })
  }else{
    next()
  }
})

// make sure people can't just get to the payment page
let internal = (req,res,next) => {
  if(!req.session.customer_id || !res.locals.customer){
    return res.redirect('/')
  }
  next()
}

// step 1
app.get('/',(req,res,next) => {
  let data = {
    title: 'Step 1',
    step_1: true,
  }

  req.session.destroy()
  res.render('index.hbs',data)
})

// step 1 submit (create customer)
app.post('/',(req,res,next) => {
  let packet = _.clone(req.body)
  packet.same_shipping = true

  client.query('POST /customer/create',packet,(e,r) => {
    if(e) return next(e)
    req.session.customer_id = r
    res.json({ok:true})
  })
})

// step 2
app.get('/step2',internal,(req,res,next) => {
  let data = {
    title: 'Step 2',
    step_2: true,
    today_date: moment().format('dddd, MMMM Do'),
    three_days: () => {
      let d = new Date
      d.addDays(3)
      return moment(d).format('dddd, MMMM Do')
    }(),
    fourteen_days: () => {
      let d = new Date
      d.addDays(14)
      return moment(d).format('dddd, MMMM Do')
    }(),
  }
  res.render('step2.hbs',data)
})

// step 2 submit (charge card)
app.post('/step2',internal,(req,res,next) => {
  let packet = {
    customer: req.session.customer_id,

    // this will update billing info then do the purchase in one request
    billing: _.clone(req.body),
    update_billing: true,
  }

  let selected_offer = req.body.offer || conf.orbsa.offer

  client.query(`POST /offer/${selected_offer}/purchase`,packet,(e,r) => {
    if(e) return next(e)
    res.json({ok:true})
  })
})

// step 3
app.get('/step3',internal,(req,res,next) => {
  let data = {
    title: 'Step 3',
    step_3: true,
  }
  res.render('step3.hbs',data)
})

// upsell accept click (on thank you page)
if(conf.orbsa.upsell.enabled){
  app.post('/thanks/upsell',internal,(req,res,next) => {

    // the customer already has billing info at this point
    let packet = {
      customer: req.session.customer_id,
    }

    let selected_offer = conf.orbsa.upsell.offer

    client.query(`POST /offer/${selected_offer}/purchase`,packet,(e,r) => {
      if(e) return next(e)
      res.json({ok:true})
    })
  })
}

module.exports = app


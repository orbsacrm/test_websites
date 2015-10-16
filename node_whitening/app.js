/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 */
'use strict'

let
  _ = require('lodash')
  , fs = require('fs')
  , express = require('express')
  , conf = require('./conf')
  , log = require('./lib/log')
  , Client = require('./lib/client')
  , exphbs = require('express-handlebars')

let app = express()

app.engine('hbs',exphbs({
  defaultLayout: 'main',
  extname: '.hbs',
}))

app.set('view engine','hbs')

app.use('/public',express.static('./public'))

app.use(require('body-parser').urlencoded({extended:false}))

app.use((req,res,next) => {
  res.locals.conf = conf
  next()
})

// cookie session
app.use(require('express-session')({
  secret: 'session_secret',
  saveUninitialized: true,
  resave: false,
}))

app.listen(conf.port)
log(`:${conf.port}`)

for(let file of fs.readdirSync('./routes')){
  if(file.endsWith('.js')){
    app.use('/',require(`./routes/${file}`))
  }
}

// handle errors
app.use((err,req,res,next) => {
  res.json({error:err.toString()})
})


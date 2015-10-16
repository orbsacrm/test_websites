/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 */
'use strict'

let
  _ = require('lodash')
  , fs = require('fs')
  , express = require('express')
  , conf = require('./conf')
  , log = require('./lib/log')
  , orbsa = require('./lib/orbsa')
  , exphbs = require('express-handlebars')

let app = express()

app.engine('hbs',exphbs({
  defaultLayout: 'main',
  extname: '.hbs',
}))

app.set('view engine','hbs')

app.use('/public',express.static('./public'))

app.use((req,res,next) => {
  res.locals.conf = conf
  next()
})

app.listen(conf.port)
log(`:${conf.port}`)

for(let file of fs.readdirSync('./routes')){
  if(file.includes('.js')){
    app.use('/',require(`./routes/${file}`))
    log(`loaded ./routes/${file}`)
  }
}


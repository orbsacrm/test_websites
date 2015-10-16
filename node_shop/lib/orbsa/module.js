/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 */
'use strict'

let
  _ = require('lodash')
  , needle = require('needle')
  , Promise = require('bluebird')

let log = (x) => {console.log(x)}

class Client {

  constructor(opts) {
    if(!opts.url){
      throw new Error('opts.url is required')
    }

    this.opts = opts
  }

  hello(cb) {
    log('hello() called')
    cb(null,'world')
  }

}

module.exports = Client

/*
let c = new Client({
  url: 'http://example.com'
})

c.hello((e,r) => {
  log('results:')
  log(e)
  log(r)
})
*/



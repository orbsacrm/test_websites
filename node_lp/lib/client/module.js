/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 */
'use strict'

let
  _ = require('lodash')
  , qs = require('querystring')
  , needle = require('needle')

let log = (x) => {console.log(x)}

class Client {

  constructor (opts) {
    if(!opts.url){
      throw new Error('opts.url is required')
    }else if(!opts.key){
      throw new Error('opts.key is required')
    }

    this.opts = _.clone(opts)

    if(this.opts.prefix)
      this.opts.url += this.opts.prefix

    this.needle_opts = {
      compressed: true,
      follow_max: 1,
      username: `token:${opts.key}`,
      auth: 'auto',
      open_timeout: 25000,
      read_timeout: 50000,
    }
  }

  // easy format
  query (str,body,cb) {
    if(!cb && typeof body == 'function'){
      cb = body
      body = null
    }

    let low = str.toLowerCase()
    let type = null
    let prefix_len = 0

    if(low.startsWith('get ')){
      prefix_len = 4
      type = 'get'
    }else if(low.startsWith('post ')){
      prefix_len = 5
      type = 'post'
    }else if(low.startsWith('put ')){
      prefix_len = 4
      type = 'put'
    }else if(low.startsWith('del ')){
      prefix_len = 4
      type = 'del'
    }else if(low.startsWith('delete ')){
      prefix_len = 7
      type = 'del'
    }else{
      return cb(new Error('Unparsable request type'))
    }

    let url = str.substr(prefix_len)

    if(type=='put' || type=='post' || type=='del'){
      return this[type](url,{},body,cb)

    }else if(type=='get'){
      return this.get(url,{},cb) 
    }

    return new Error('Failed to parse request type')
  }

  // request methods
  get (url,query,cb) {
    url = this._build_url(url,query)

    needle.get(url,this.needle_opts,(e,r) => {
      if (e) return cb(e)
      if (!r.body || !r.body.ok) 
        return cb(new Error(r.body.errors || r.body.error || 'Unknown error'))
      cb(null,(r.body.result || true))
    })
  }

  post (url,query,body,cb) {
    if(!cb && typeof body == 'function'){
      cb = body
      body = query
      query = {}
    }

    url = this._build_url(url,query)

    needle.post(url,body,this.needle_opts,(e,r) => {
      if (e) return cb(e)
      if (!r.body || !r.body.ok) 
        return cb(new Error(r.body.errors || r.body.error || 'Unknown error'))
      cb(null,(r.body.result || true))
    }) 
  }

  del (url,query,cb) {
    url = this._build_url(url,query)

    needle.delete(url,null,this.needle_opts,(e,r) => {
      if (e) return cb(e)
      if (!r.body || !r.body.ok) 
        return cb(new Error(r.body.errors || r.body.error || 'Unknown error'))
      cb(null,(r.body.result || true))
    })  
  }

  _build_url (url,query) {
    let base = url
    let extras = {}

    if(url.includes('?')){
      base = url.split('?').shift() 
      extras = qs.parse(url.split('?').pop())
    }

    if(!query) query = {}

    query = _.defaults(query,extras)

    return [
      'http://',
      this.opts.url,
      base,
      '?',
      qs.stringify(query),
    ].join('')
  }

}

module.exports = Client

/*
let conf = require('./../../conf')

let c = new Client({
  url: conf.orbsa.url,
  key: conf.orbsa.key,
  prefix: '/v1',
})

c.query('GET /product',(e,r) =>{
  log(e)
  log(r)
})
*/


/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 */
module.exports = {
  app: {
    name: 'MySparklyWhites',
  },
  port: 7777,
  orbsa: {
    url: 'localhost:9051',
    prefix: '/v1',
    key: 'dAfQTBWqSZQJcmchqfKkwdj24KSklKDHnkjrdR9dLpjTEi1cHOfWOfl3K59aqfNf',
    
    // id of the subscription offer to use for the lp
    offer: '419ezf4ynw8z',
    
    // optional one-click upsell offer to be shown after purchase
    upsell: {
      enabled: false,
      offer: '819cw7p9a281',
    },

  },
}


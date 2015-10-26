/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 */
var log = function(x){try{console.log(x)}catch(e){}}

var _ = orbsa._
var $ = orbsa.$

$(function(){

  // alert message handlers
  var flash = function(type,str){
    $('div.flash-alert').remove()

    str = str
      .split('Error: ')
      .join('')
      .split(',')

    str = _.map(str,function(line){
      return line.split(' (field').shift()
    })

    str = str.join('<br>')

    $('#flash').after('<div class="flash-alert alert alert-' + type + ' dismiss-pointer">' + str + '</div>')
  }

  $(document).on('click','.dismiss-pointer',function(e){
    e.preventDefault()
    $(this).remove()
  })

  // populate countries
  _.each(orbsa.geo.countries,function(item){
    var html_item = '<option value=' + item.iso2 + '>' + item.name + '</option>'
    $('form#lead-form select[name=country]').append(html_item)
  })

  // select us as default country
  $('form#lead-form select[name=country]').val('US')

  // populate us states
  _.each(orbsa.geo.us_states,function(item){
    var html_item = '<option value=' + item.code + '>' + item.name + '</option>'
    $('form#lead-form select[name=state]').append(html_item)
  })

  // fake customer signup form population
  $('a#fill-lead').click(function(e){
    e.preventDefault()
    $.get('public/people.json',function(r){
      r = JSON.parse(r);
      var item = _.first(_.shuffle(r))
      item.address = item.address_1
      item.zipcode = item.zipcode.substr(0,5)
      $('form#lead-form').find('input,select').each(function(){
        var name = $(this).prop('name')
        if(name && item[name]){
          $(this).val(item[name])
        }
      })
    })
  })

  // populate payment form with test data
  $('a#fill-payment').click(function(e){
    e.preventDefault()
    var item = {
      number: '4470330769941000',
      expires: _.random(1,12) + '/' + _.random(20,25),
      verification_value: _.random(100,999),
    }
    $('form#payment-form').find('input,select').each(function(){
      var name = $(this).prop('name')
      if(name && item[name]){
        $(this).val(item[name])
      }
    })
  })



  // ajax upsell accept button click
  /*$('#accept-upsell').click(function(e){
    e.preventDefault()
    $('#upsell-offer').hide()
    orbsa.page.lock({image:'balls'})
    $.post('/thanks',{},function(r){
      orbsa.page.unlock()
      if(r.error){
        $('#upsell-offer').hide()
        $('#upsell-sorry').show()
      }else{
        $('#upsell-offer').hide()
        $('#upsell-thanks').show()
      }
    })
  })*/

  // ajax upsell accept button click
  $('#deny-upsell').click(function(e){
    e.preventDefault()
    $('#upsell-offer').hide()
  })

})


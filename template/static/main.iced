# vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2
window.log = (x) -> try console.log(x)

$ ->
  log 'Ready'

  window.lock = ->
    if !$('#_lock').length
      $('body').append """
        <div id="_lock" style="height:5000px;width:5000px;background:#000;position:absolute;z-index:999;opacity:0.85;overflow:hidden;top:0;left:0;display:none"></div>
      """
    $('#_lock').show()
    $('body').css overflow:'hidden'

  window.unlock = ->
    $('#_lock').hide()
    $('body').css overflow:'auto'


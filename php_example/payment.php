<?php
require_once(__DIR__.'/deps/class.billing.php');

session_start();

if(empty($_SESSION['customer'])){
  header('location: index.php');
  die;
}

# form post
if($_SERVER['REQUEST_METHOD']=='POST'){

  $b = new Billing($config);

  // update the customer's billing information
  $res = $b->customer_update_billing($_SESSION['customer'],$_POST);

  # successfully updated billing info, purchase the product now
  if($res['ok']){

    # purchase request
    $res = $b->purchase_product($_SESSION['customer'],'418w7a9clv4q',[]);

    # goto the thank you page, the purchase was successful
    if($res['ok']){
      header('location: thanks.php');
      die;

    }else{
      # set the errors to show on the page
      $errors = $res['errors'];
    }

  }else{

    # set errors to show on the page, something was wrong
    $errors = $res['errors'];
  }

}

?>
<!-- vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 -->
<!DOCTYPE html>
<html>
  <head>
    <title></title>
    <link rel="stylesheet" href="static/bootstrap.css">
    <script src="static/jquery.js"></script>
  </head>
  <body>

    <div class="container">

      <div class="col-sm-12"><h1>Best Diet Product</h1></div>

      <div class="col-sm-7">
        <img src="static/weight-loss.jpg" style="width:100%;border-radius:30px">
      </div>

      <div class="col-md-5">

      <?php
        # show errors if they existed with customer creation 
        if(!empty($errors)){
          echo "<p>";
          foreach($errors as $str){
            echo "<span style='color:crimson'>$str</span><br>";
          }
          echo "</p>";
        }
      ?>

      <form class="form well" id="form" method="post">
        <h3 class="form-signin-heading">Payment Information</h2>
        <p>Provide your payment information below.</p>
        <p>Product is a subscription charge.</p>
        <p><a href="#" id="fill">(fill form)</a></p>

        <label class="sr-only">Card number</label>
        <input name="number" id="number" type="text" class="form-control" placeholder="Card number" required autofocus>
        <br>

        <label class="sr-only">Expiration date</label>
        <div>
          <input name="month" id="month" type="text" class="form-control" style="width:50px;float:left;margin-right:10px" placeholder="MM" required>
          <input name="year" id="year" type="text" class="form-control" style="width:75px" placeholder="YYYY" required>
        </div>
        <br>

        <label class="sr-only">CVV code</label>
        <input name="verification_value" id="verification_value" type="text" class="form-control" placeholder="CVV" style="width:75px" required>
        <br>

        <label class="sr-only">Billing zipcode</label>
        <input name="zipcode" id="zipcode" type="text" class="form-control" placeholder="Billing zipcode" required>
        <br>

        <!--
        <div class="checkbox">
          <label>
            <input type="checkbox" name="addon" value="1"> Upgrade to premium membership (+$1.99/mo)
          </label>
        </div>
        <br>
        -->

        <button class="btn btn-lg btn-danger btn-block" type="submit">Complete Purchase!</button>
      </form>

    </div>
    </div>

    <script src="static/main.js"></script>

    <script>
    $(function(){
      function _rand(min,max){
        return Math.floor(Math.random()*(max-min+1)+min).toString()
      }

      $('#fill').click(function(e){
        e.preventDefault()
        $('#number').val('4470330769941000')
        $('#month').val(_rand(1,12))
        $('#year').val(_rand(16,20))
        $('#verification_value').val(_rand(111,999))
        $('#zipcode').val('90210')
      })
    })()
    </script>

  </body>
</html>


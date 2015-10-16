<?php
require_once(__DIR__.'/deps/class.billing.php');

session_start();

if(empty($_SESSION['customer'])){
  header('location: index.php');
  die;
}

$b = new Billing($config);

# get description of the first product in the offer so we can display it
# on the payment page
$product_details = $b->offer_view($config['offer']);

$product_desc = $product_details['result']['products'][0]['description'];
$product_desc = str_replace('\n',"<br>",$product_desc);

# form post
if($_SERVER['REQUEST_METHOD']=='POST'){

  // update the customer's billing information
  $res = $b->customer_update_billing($_SESSION['customer'],$_POST);

  # successfully updated billing info, purchase the product now
  if($res['ok']){

    $options = [];

    # set a custom cycle discount if checkbox was set
    if(!empty($_POST['discount'])){

      # give the customer 25% off for the first 2 cycles
      $options['discount'] = [
        'modifier' => 'percent',
        'amount' => 25,
        'cycles' => 2,
      ];
    }

    # do the purchase
    $res = $b->offer_purchase($_SESSION['customer'],$config['offer'],$options);

    # goto the thank you page, the purchase was successful
    if($res['ok']){
      header('location: thanks.php');
      die;

    }else{
      # set the errors to show on the page
      $errors = $res['errors'];
    }

  }else{

    # set errors
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
        <p><?=$product_desc?></p>
        <p>
          <a href="#" id="fill">(fill form)</a><br>
          <a href="add_credit.php" target="_blank">(add 5.00$ credit to account)</a>
        </p>

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

        <div class="checkbox">
          <label>
            <input type="checkbox" name="upsell" value="1"> Upgrade to premium membership (+$9.99/2 hours)
          </label>
        </div>
        <br>

        <div class="checkbox">
          <label>
            <input type="checkbox" name="discount" value="1"> Apply a custom discount
          </label>
        </div>
        <br>

        <!-- offer identifier -->
        <input type="hidden" name="offer" value="<?=$config['offer']?>">

        <button class="btn btn-lg btn-danger btn-block" type="submit" name="submit">Complete Purchase!</button>
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
    })
    </script>

  </body>
</html>


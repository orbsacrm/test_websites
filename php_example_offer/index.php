<?php
require_once(__DIR__.'/deps/class.billing.php');

session_start();

# page request
if($_SERVER['REQUEST_METHOD']=='GET'){
  session_unset();
  session_destroy();
}

# form post
if($_SERVER['REQUEST_METHOD']=='POST'){

  # create a new billing instance
  $b = new Billing($config);

  $form = $_POST;
  $form['same_shipping'] = true;

  # attempt to create the customer
  $res = $b->customer_create($form);

  # successfully created the customer
  if($res['ok']){

    # set the customer id into the session
    $_SESSION['customer'] = $res['result'];

    # goto the payment page
    header('location: payment.php');

  }else{

    # set errors to show on the page, something was wrong
    $errors = $res['errors'];
  }

}

// get a random us ip address
$us_ips = json_decode(file_get_contents(__dir__.'/static/random_ips.json'));
shuffle($us_ips);
$random_ip = $us_ips[0];

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

      <form class="form well" id="form" method="post" id="form">
        <h3 class="form-signin-heading">Start Burning Fat Today!</h2>
        <p>First, tell us where to send your product.</p>

        <label class="sr-only">First name</label>
        <input type="text" name="first" class="form-control" placeholder="First name" required autofocus>
        <br>

        <label class="sr-only">Last name</label>
        <input type="text" name="last" class="form-control" placeholder="Last name" required>
        <br>

        <label class="sr-only">Address</label>
        <input type="text" name="address" class="form-control" placeholder="Address" required>
        <br>

        <label class="sr-only">City</label>
        <input type="text" name="city" class="form-control" placeholder="City" required>
        <br>

        <label class="sr-only">State</label>
        <input type="text" name="state" class="form-control" placeholder="State" required>
        <br>

        <label class="sr-only">Zipcode</label>
        <input type="text" name="zipcode" class="form-control" placeholder="90210" required>
        <br>

        <label class="sr-only">Country</label>
        <input type="text" name="country" class="form-control" placeholder="Country" required>
        <br>

        <label class="sr-only">Email address</label>
        <input type="email" name="email" class="form-control" placeholder="Email addres" required>
        <br>

        <!-- optional form data -->
        <label class="sr-only">Shirt size</label>

        <select name="form[shirt_size]" class="form-control" required>
          <option value="x_small">Shirt size: Extra small</option>
          <option value="small">Shirt size: Small</option>
          <option value="medium">Shirt size: Medium</option>
          <option value="large">Shirt size: Large</option>
        </select>
        <br>

        <!-- offer identifier -->
        <input type="hidden" name="offer" value="<?=$config['offer']?>">
        <input type="hidden" name="ip" value="<?=$random_ip?>">

        <? if(!empty($_GET['affiliate'])){ ?>
          <input type="hidden" name="affiliate" value="<?=htmlspecialchars($_GET['affiliate']);?>">
        <? } ?>

        <? if(!empty($_GET['subid'])){ ?>
          <input type="hidden" name="subid" value="<?=htmlspecialchars($_GET['subid']);?>">
        <? } ?>

        <button class="btn btn-lg btn-danger btn-block" type="submit" name="submit">Rush My Order!</button>
      </form>

    </div>
    </div>

    <script src="static/main.js"></script>

  </body>
</html>


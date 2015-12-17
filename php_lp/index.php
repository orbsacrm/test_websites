<?php /* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 */
/* class.billing.php */
require_once(__DIR__.'/deps/config.php');
require_once(__DIR__.'/deps/class.billing.php');

session_start();

$b = new Billing($config);

$offer = $b->offer_view($config['offer'])['result'];
$cycle = $offer['cycle'];

# reset session on get
if($_SERVER['REQUEST_METHOD']=='GET'){
    session_unset();
    session_destroy();
}

# create customer
if($_SERVER['REQUEST_METHOD']=='POST'){
  header('content-type: text/json');

  # attempt to create the customer
  $res = $b->customer_create($_POST);

  # successfully created the customer
  if($res['ok']){

    # set the customer id into the session
    $_SESSION['customer_id'] = $res['result']['_id'];
  }

  # output the response
  echo(json_encode($res));
  die;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Landing page - Orbsa Widgets Company</title>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="public/main.css">
    <!--[if lt IE 9]>
    <script src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<div class="container">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-md-12">
                <h3 class="text-center">
                    <img src="public/symbol.svg" class="symbol-logo" title="Orbsa Widgets Company">
                </h3>
                <hr>
            </div>
        </div>
        <div class="row">
            <div class="col-md-7">
                <h3><?=$offer['name']?></h3>
                <p><?=$offer['description']?></p>
                <hr>
                <p>
                <ul class="text-muted tiny">
                  <? if($cycle['trial_seconds'] > 0 && !empty($cycle['trial_seconds_human'])){ ?> 
                    <li>Trial period: <?=$cycle['trial_seconds_human']?></li>
                  <? } ?>
                  <li>Cycle price: <?=$cycle['cycle_amount_dollars']?> every <?=$cycle['cycle_seconds_human']?></li>
                </ul>
                </p>
            </div>

            <div class="col-md-5">
                <input type="hidden" id="flash">
                <form class="form well" id="lead-form" method="post" action="">
                    <h3 class="form-signin-heading">Register now!</h3>
                    <p>Where should we send <em>your first package?</em></p>

                    <label class="sr-only">First name</label>
                    <input name="first" type="text" class="form-control" placeholder="First name" required autofocus>

                    <label class="sr-only">Last name</label>
                    <input name="last" type="text" class="form-control" placeholder="Last name" required>

                    <label class="sr-only">Address</label>
                    <input name="address" type="text" class="form-control" placeholder="Address" required>

                    <label class="sr-only">City</label>
                    <input name="city" type="text" class="form-control" placeholder="City" required>

                    <label class="sr-only">State</label>
                    <select name="state" class="form-control" placeholder="State" required></select>

                    <label class="sr-only">Country</label>
                    <select name="country" class="form-control" placeholder="Country" required></select>

                    <label class="sr-only">Zipcode</label>
                    <input name="zipcode" type="text" class="form-control" placeholder="Zipcode" required>

                    <label class="sr-only">Email address</label>
                    <input name="email" type="email" class="form-control" placeholder="Email address" required>
                    <input name="same_shipping" type="hidden" value="true">

                    <button class="btn btn-lg btn-primary btn-block" type="submit">
                        Continue
                    </button>
                </form>
                <p class="text-right">
                    <a href="#" class="tiny" id="fill-lead">(fill form)</a>
                </p>
            </div>
        </div>
    </div>
</div>
<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="<?=$config['url']?>/v1/js"></script>
<script src="public/main.js"></script>
</body>
</html>


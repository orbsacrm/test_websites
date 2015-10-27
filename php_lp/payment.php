<?php /* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 */
require_once(__DIR__.'/deps/config.php');
require_once(__DIR__.'/deps/class.billing.php');

session_start();

if(empty($_SESSION['customer'])){
  header('location: index.php');
  die;
}

$b = new Billing($config);

$offer_id = $config['offer'];
$offer = $b->offer_view($offer_id)['result'];
$cycle = $offer['cycle'];
$customer = $b->customer_view($_SESSION['customer'])['result'];

# purchase offer
if($_SERVER['REQUEST_METHOD']=='POST'){
  header('content-type: text/json');

  $billing = [
    'billing' => $_POST
  ];

  # update billing information and purchase offer
  $res = $b->offer_purchase($_SESSION['customer'],$offer_id,$billing);

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
                <h3>Last step, <?=$customer['details']['first']?></h3>
                <p>Just complete the payment details form and you'll be an official subscription member.</p>
                <p>Fill out the payment form to complete your purchase of our <strong><?=$offer['name']?></strong>.</p>
                <hr>
                <p>
                <ul class="text-muted tiny">
                    <li>Trial period: <?=$cycle['trial_seconds_human']?></li>
                    <li>Cycle price: <?=$cycle['cycle_amount_dollars']?> every <?=$cycle['cycle_seconds_human']?></li>
                </ul>
                </p>
            </div>

            <div class="col-md-5">
                <input type="hidden" id="flash">

                <form class="form well" id="payment-form" method="post" action="">
                    <img src="public/cards.png" style="width:135px;margin-top:5px;" class="pull-right">

                    <h3 class="form-payment-heading">
                        Payment details
                    </h3>

                    <hr>

                    <label>Card number</label>
                    <input name="number" type="text" class="form-control" placeholder="Valid card number" autofocus required>

                    <div class="row">
                        <div class="col-md-6">
                            <label>Expiration</label>
                            <input name="expires" type="text" class="form-control" placeholder="MM/YY" required>
                        </div>

                        <div class="col-md-6">
                            <label>CVV code</label>
                            <input name="verification_value" type="text" class="form-control" placeholder="CVV" required>
                        </div>
                    </div>

                    <button class="btn btn-lg btn-success btn-block" type="submit">
                        <i class="fa fa-lock"></i>
                        Continue
                    </button>
                </form>
                <p class="text-right">
                    <a href="#" class="tiny" id="fill-payment">(fill form)</a>
                </p>
            </div>
        </div>
    </div>
</div>
<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="<?=$config['url']?>/v1/js?fresh=1"></script>
<script src="public/main.js"></script>
</body>
</html>


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

# upsell configuration
if($config['upsell']['enabled']){
  $upsell = $b->offer_view($config['upsell']['offer'])['result'];

  # accept one-click upsell
  if($_SERVER['REQUEST_METHOD']=='POST'){
    header('content-type: text/json');

    $res = $b->offer_purchase($_SESSION['customer'],$offer_id,[]);

    echo(json_encode($res));
    die;  
  }
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
            <div class="col-md-6 col-md-offset-3 text-center">
                <h3><i class="fa fa-check"></i> Thanks <?=$customer['details']['first']?>!</h3>
                <p>
                    Your order was placed successfully.
                </p>

                <?php if($config['upsell']['enabled']) : ?>
                <div id="upsell-sorry" style="display:none">
                    <hr>
                    <h3><i class="fa fa-thumbs-down"></i> Sorry <?=$customer['details']['first']?>..</h3>
                    <p>
                        It looks like there was a problem with the payment, try contacting us to get it sorted out.
                        Anyway, thanks for the purchase and we hope you enjoy your products!
                    </p>
                </div>
                <div id="upsell-thanks" style="display:none">
                    <hr>
                    <h4><i class="fa fa-check"></i> Thanks again!</h4>
                    <p>
                        Your upsell order was placed as well, we hope you enjoy your products.
                    </p>
                </div>
                <div id="upsell-offer">
                    <br>
                    <div class="text-left well">
                        <p>
                            <?=$customer['details']['first']?>, did you also want to take advantage of this additional deal that
                            we only offer to subscription member customers?
                        </p>
                        <hr>
                        <h4><?=$upsell['name']?></h4>
                        <p><?=$upsell['description']?></p>
                        <p class="text-center">
                          <br>
                          <button class="btn btn-success btn-lg" id="accept-upsell" type="submit">
                              <i class="fa fa-plus"></i> Add it to my order <?=$upsell['price_human']?>
                          </button>

                          <button class="btn btn-lg" id="deny-upsell">
                              No thanks
                          </button>
                        </p>
                    </div>
                </div>
                <?php endif; ?>

                <hr>
                <p><a href="index.php" class="tiny">(clear session and return to start)</a></p>
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


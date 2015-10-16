<?php
require_once(__DIR__.'/deps/class.billing.php');

$b = new Billing($config);

# pull customers with active subscriptions
$active_customers = $b->customer_list([
  'active' => true
]);

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
      <div class="col-md-12">

        <h1>Basic Admin Panel</h1>
        <hr>

        <h3>Customers with active subscriptions (<?=count($active_customers['result']);?>)</h3>

        <?if(!empty($active_customers['result'])){?>
          <table class="table table-striped">
          <thead>
            <tr>
              <th>ID</th>
              <th>Last name</th>
              <th>First name</th>
              <th>Email address</th>
              <th>Created</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach($active_customers['result'] as $item){ ?>
            <tr>
              <td><?=$item['_id']?></td>
              <td><?=$item['details']['last']?></td>
              <td><?=$item['details']['first']?></td>
              <td><?=$item['details']['email']?></td>
              <td><?=date('r',$item['ctime'])?></td>
            </tr>

          <?php } ?>
        <?php } ?>
        </tbody>
        </table>

      </div>
    </div>

    <script src="static/main.js"></script>

  </body>
</html>


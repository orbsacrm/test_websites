<?php
require_once(__DIR__.'/deps/class.billing.php');

session_start();

if(empty($_SESSION['customer'])){
  header('location: index.php');
  die;
}

$b = new Billing($config);

$res = $b->customer_add_credits($_SESSION['customer'],"$5.00");

print_r($res);
die;


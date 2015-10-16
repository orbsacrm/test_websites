<?php /* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 */
/* postback.php */
$required = [
  'customer',
  'offer',
];

$allowed = true;

foreach($required as $item){
  if(empty($_GET[$item])){
    $allowed = false;
  }
}

if(!$allowed){
  echo "Missing a required field\n";
  die;
}

$file = 'postback.txt';

if(file_exists($file)){
  $current = file_get_contents($file);
}else{
  $current = "";
}

$log_content = [
  'time' => time()
];

foreach($_GET as $key=>$val){
  $log_content[$key] = $val;
}

$log_content = json_encode($log_content);

$current .= $log_content . "\n";

file_put_contents($file,$current);

echo "Thanks\n";
die;


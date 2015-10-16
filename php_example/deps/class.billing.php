<?php /* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 */
/* class.billing.php */
include_once(__DIR__.'/config.php');

class Billing {

  var 
    $conf,
    $ch
    ;

  function __construct ($conf) {
    $this->conf = $conf;
    $this->_curl_init();
  }

  # customer methods
  function customer_create ($details) {
    return $this->_send('/v1/customer/create','POST',$details);
  }

  function customer_view ($_id) {
    return $this->_send('/v1/customer/view/'.$_id,'GET');
  }

  function customer_update_billing ($_id,$details) {
    return $this->_send('/v1/customer/'.$_id.'/billing/update/','POST',$details);
  }

  function customer_update_form ($_id,$details) {
    return $this->_send('/v1/customer/update_form/'.$_id,'POST',$details);
  }

  function customer_list ($options=[]) {
    return $this->_send('/v1/customer/list','GET',$options);
  }

  # campaign methods
  function campaign_view ($_id) {
    return $this->_send('/v1/campaign/view/'.$_id,'GET');
  }

  # product methods
  function purchase_product ($customer_id,$product_id,$addons=[]) {
    $details = [
      'customer' => $customer_id,
    ];

    return $this->_send('/v1/offer/'.$product_id.'/purchase/','POST',$details);
  }
  
  # subscription methods
  function subscription_create ($details) {
    return $this->_send('/v1/subscription/create','POST',$details);
  }

  function subscription_view ($_id) {
    return $this->_send('/v1/subscription/view/'.$_id,'GET');
  }

  function subscription_update ($_id,$details) {
    return $this->_send('/v1/subscription/update/'.$_id,'POST',$details);
  }

  function subscription_cancel ($_id,$details) {
    return $this->_send('/v1/subscription/cancel/'.$_id,'POST',$details);
  }

  function subscription_reactivate ($_id,$details) {
    return $this->_send('/v1/subscription/reactivate/'.$_id,'POST',$details);
  }

  # private methods
  private function _send ($route,$method,$data=[]) {
    $query = [];

    if($this->conf['auth']){
      $query['key'] = $this->conf['key'];
    }

    if($method=='GET'){
      if(!empty($data)){
        foreach($data as $key=>$val){
          $query[$key] = $val;
        }
      }

      $get_url = $this->conf['url'].$route.'?'.http_build_query($query);

      curl_setopt($this->ch,CURLOPT_POST,0);
      curl_setopt($this->ch,CURLOPT_URL,$get_url);

      $response = curl_exec($this->ch);

    }else{

      $post_data = http_build_query($data);
      $post_url = $this->conf['url'] .$route.'?'.http_build_query($query);

      curl_setopt($this->ch,CURLOPT_POST,1);
      curl_setopt($this->ch,CURLOPT_POSTFIELDS,$post_data);
      curl_setopt($this->ch,CURLOPT_URL,$post_url);

      $response = curl_exec($this->ch);
    }

    $r = json_decode($response,1);

    if(!empty($r['error'])){
      $r['errors'] = [$r['error']];
      unset($r['error']);
    }

    return $r;
  }

  private function _curl_init () {
    $ch = curl_init();

    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
    curl_setopt($ch,CURLOPT_TIMEOUT,10);

    $this->ch = $ch;
  }

}

/*
# example
$b = new Billing($config);

# customer create
$fake_customer = [
  'first' => 'Douglas',
  'last' => 'Lauer',
  'email' => 'dlauer12@gmail.com',
  'address' => '95218 Maximus Corners',
  'city' => 'East Kayaborough',
  'zipcode' => '03142',
  'state' => 'NJ',
  'country' => 'US',
  'phone' => '424-255-7533', 
  'ip' => '76.189.188.142',
  'tags' => ['subid1','subid2'],
];

$result = $b->customer_create($fake_customer);

# get customer info by id
$result = $b->customer_view('0124kueyxxjz');

echo "Result:";
print_r($result);
*/


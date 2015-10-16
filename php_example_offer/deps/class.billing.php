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
    return $this->_send('/v1/customer/'.$_id,'GET');
  }

  function customer_update_billing ($_id,$details) {
    return $this->_send('/v1/customer/'.$_id.'/billing/update','POST',$details);
  }

  function customer_update_shipping ($_id,$details) {
    return $this->_send('/v1/customer/'.$_id.'/shipping/update','POST',$details);
  }

  function customer_update_form ($_id,$details) {
    return $this->_send('/v1/customer/'.$_id.'/form/update','POST',$details);
  }

  function customer_append_form ($_id,$details) {
    return $this->_send('/v1/customer/'.$_id.'/form/append','POST',$details);
  }

  function customer_list ($options=[]) {
    return $this->_send('/v1/customer/list','GET',$options);
  }

  function customer_search ($options=[]) {
    return $this->_send('/v1/customer/search','GET',$options);
  }

  # customer credit methods
  function customer_add_credits ($_id,$amount) {
    return $this->_send('/v1/customer/'.$_id.'/credit/update','POST',[
      'method' => 'add',
      'amount' => $amount
    ]);
  }

  function customer_subtract_credits ($_id,$amount) {
    return $this->_send('/v1/customer/'.$_id.'/credit/update','POST',[
      'method' => 'subtract',
      'amount' => $amount
    ]);
  }

  function customer_set_credits ($_id,$amount) {
    return $this->_send('/v1/customer/'.$_id.'/credit/update','POST',[
      'method' => 'set',
      'amount' => $amount
    ]);
  }

  function customer_clear_credits ($_id,$amount) {
    return $this->_send('/v1/customer/'.$_id.'/credit/update','POST',[
      'method' => 'clear'
    ]);
  }

  # offer methods
  function offer_view ($_id) {
    return $this->_send('/v1/offer/'.$_id,'GET');
  }

  function offer_purchase ($customer_id,$offer_id,$options=[]) {
    $details = [
      'customer' => $customer_id,
      'offer' => $offer_id
    ];

    # apply additional options
    if(!empty($options)) {
      foreach($options as $key=>$value){
        $details[$key] = $value;
      }
    }

    return $this->_send('/v1/offer/'.$offer_id.'/purchase','POST',$details);
  }

  # product methods
  function product_search ($query) {
    $query = [
      'q' => $query,
    ];
    return $this->_send('/v1/product/search/','GET',$query);
  }
  
  # subscription methods
  function subscription_create ($details) {
    return $this->_send('/v1/subscription/create','POST',$details);
  }

  function subscription_view ($_id) {
    return $this->_send('/v1/subscription/'.$_id,'GET');
  }

  function subscription_update ($_id,$details) {
    return $this->_send('/v1/subscription/'.$_id.'/update','POST',$details);
  }

  function subscription_cancel ($_id,$details) {
    return $this->_send('/v1/subscription/'.$_id.'/cancel','POST',$details);
  }

  function subscription_renew ($_id,$details) {
    return $this->_send('/v1/subscription/'.$_id.'/renew','POST',$details);
  }

  # misc methods
  function userdata ($ip_address) {
    $query = [
      'ip' => $ip_address,
    ];
    return $this->_send('/v1/userdata','GET',$query);
  }

  function card_info ($card_number) {
    $query = [
      'number' => $card_number,
    ];
    return $this->_send('/v1/card_info','GET',$query);
  }

  # private methods
  private function _send ($route,$method,$data=[]) {
    $query = [];

    if($this->conf['auth']){
      curl_setopt($this->ch, CURLOPT_USERPWD, "token:" . $this->conf['key']);
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
    curl_setopt($ch,CURLOPT_TIMEOUT,30);

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


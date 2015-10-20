<?php /* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 */

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
  /*
   * Customer properties:
   * @param first: Required. String. The customer first name
   * @param last: Required. String. The customer last name
   * @param address: Required. String. The customer address
   * @param city: Required. String. The customer city
   * @param state: Required. String. The customer state
   * @param country: Required. String. The customer country
   * @param zipcode: Required. Numeric. 5 digits. The customer zip code
   * @param email: Required. String. The customer email
   */
  # Creates a customer
  function customer_create ($customer) {
    return $this->_send('/v1/customer/create','POST',$customer);
  }
  # Find a customer by id
  function customer_view ($_id) {
    return $this->_send('/v1/customer/'.$_id,'GET');
  }
  #updates a customer billing credit card information
  /*
   * Accepted details fields
   * @param cvv or cvc or ccv or verification_code or verification_value: Required. Numeric. Accept any of this fields
   * as customer's card verification code
   * @param expiration or expires or mmdd or (month and year) : Required. String. The month and year of credit card expiration
   * @param number: Required: String (only numbers). The credit card number
   * @param zipcode: Required. Numeric. 5 digits. The customer's credit card zip code
   * @param gateway: Optional. String. Id of gateway to use
   * @param load_balancer: Optional. String. Id of load balancer to use
   */
  function customer_update_billing ($_id,$details) {
    return $this->_send('/v1/customer/'.$_id.'/billing/update','POST',$details);
  }
  #updates a customer shipping information
  /*
   * Accepted details fields
   * @param first: Required. String. The customer first name
   * @param last: Required. String. The customer last name
   * @param address: Required. String. The customer address
   * @param city: Required. String. The customer city
   * @param state: Required. String. The customer state
   * @param country: Required. String. The customer country
   * @param zipcode: Required. Numeric. 5 digits. The customer zip code
   */
  function customer_update_shipping ($_id,$details) {
    return $this->_send('/v1/customer/'.$_id.'/shipping/update','POST',$details);
  }
  #updates form data on customer
  function customer_update_form ($_id,$details) {
    return $this->_send('/v1/customer/'.$_id.'/form/update','POST',$details);
  }
  #updates extra details on customer
  function customer_update_details ($_id,$details) {
    return $this->_send('/customer/'.$_id.'/details/update','POST',$details);
  }
  #appends form data to customer
  function customer_append_form ($_id,$details) {
    return $this->_send('/v1/customer/'.$_id.'/form/append','POST',$details);
  }
  #clear customer form data
  function customer_clear_form ($_id,$details) {
    return $this->_send('/v1/customer/'.$_id.'/form/clear','POST',$details);
  }
  #list customers
  /*
   * Query options include any of the fields above
   * _id
   * details.first
   * details.last
   * details.email
   * ctime
   * traffic_source
   * traffic_source_sub
   * form
   *
   * active = 1 (list only active users)
   */
  function customer_list ($options=[]) {
    return $this->_send('/v1/customer/list','GET',$options);
  }
  #returns a list of a customer's active membership products
  function customer_get_memberships ($_id) {
    return $this->_send('/v1/customer/'.$_id.'/memberships','GET');
  }
  #adds a occurrence to customers history
  function customer_add_history ($_id,$details) {
    return $this->_send('/v1/customer/'.$_id.'/history','POST',$details);
  }
  #returns a list with customer's history
  function customer_get_history ($_id) {
    return $this->_send('/v1/customer/'.$_id.'/history','GET');
  }
  # charge or schedule a charge to this customer
  /* Available fields in charge
   * @param amount . Required. Numeric. The amount to charge the customer
   * @param future_date. Optional. Numeric (timestamp). When to charge the customer (omit to charge user now)
   * @param disable_credit. Optional. Boolean. Enable or disable the use of credit on this charge
   * @param description. Optional. String. Some extra description if needed
   */
  function customer_charge ($_id,$charge) {
    return $this->_send('/v1/customer/'.$_id.'/charge','POST',$charge);
  }

  # customer credit methods
  # Add credits to customer
  function customer_add_credits ($_id,$amount) {
    return $this->_send('/v1/customer/'.$_id.'/credit/update','POST',[
      'method' => 'add',
      'amount' => $amount
    ]);
  }
  # Subtract credits from customer
  function customer_subtract_credits ($_id,$amount) {
    return $this->_send('/v1/customer/'.$_id.'/credit/update','POST',[
      'method' => 'subtract',
      'amount' => $amount
    ]);
  }
  # Set credits of customer
  function customer_set_credits ($_id,$amount) {
    return $this->_send('/v1/customer/'.$_id.'/credit/update','POST',[
      'method' => 'set',
      'amount' => $amount
    ]);
  }
  # Clear customer credits
  function customer_clear_credits ($_id) {
    return $this->_send('/v1/customer/'.$_id.'/credit/update','POST',[
      'method' => 'clear'
    ]);
  }

  #charge methods
  /*
   * Charge properties:
   * @param status: Optional. String. Default: pending. All options are pending , processing , success , failure
   * @param error: Optional. String. An error message if any error occurred
   * @param raw: Optional. String.
   * @param token: Optional. String.
   * @param description: Optional. String. Some description of the charge if needed
   * @param context: Optional. String. All options are subscription_cycle_recapture , subscription_cycle_charge ,
   * customer_charge , initial_subscription_charge , flat_offer_charge , subscription_renewal_charge
   * @param amount: Required. Numeric. The amount to charge
   * @param customer: Required. String. The id of the customer being billed
   * @param charged_at: Optional. Date. When the customer was charged
   * @param scheduled_to: Optional. Date. When to execute the charge
   * @param allow_credit: Optional. Boolean. Default false. If user credit can be used in this charge
   * @param processing_attempts: List. Processing attempts info. Contains fields started_at (date), error (String), raw (String),
   * worker_pid (String)
   * @param extra: Optional. Object. Any extra info needed
   */
  #List charges
  function charge_list ($query) {
    return $this->_send('/v1/charges','GET',$query);
  }
  #Find a charge by id
  function charge_get ($_id) {
    return $this->_send('/v1/charges/'.$_id,'GET');
  }
  #Updates a charge
  function charge_update ($_id,$charge) {
    return $this->_send('/v1/charges/'.$_id,'PUT',$charge);
  }
  #Deletes the charge with this id
  function charge_delete ($_id) {
    return $this->_send('/v1/charges/'.$_id,'DELETE');
  }

  # offer methods
  /*
   * Offer properties:
   * @param active: Optional. Boolean. Default true. True if this offer is active
   * @param name: Required. String. The offer name
   * @param description: Optional. String. Any description to this offer if any needed
   * @param type: Required. String. Available values are 'flat' or 'cycle'
   * @param price: Required. String. The offer price
   * @param products: List. String. A list of product ids of this offer
   * @param cycle: Optional. Object. Info about the offer cycle
   * @param retry_cycle. Optional. Object. Info about the cycle of retries for this offer
   * @param restrictions. List. Restrictions for this offer if any
   */
  #Find an offer by his id
  function offer_view ($_id) {
    return $this->_send('/v1/offer/'.$_id,'GET');
  }
  #Search for offers matching the example offer criterias
  function offer_search ($offer) {
    return $this->_send('/v1/offer/search','GET',$offer);
  }
  # Purchase an offer by a customer
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
  /*
   * Product properties:
   * @param active: Optional. Boolean. Default true. True if the product is active
   * @param sku: Required. String. The product sku
   * @param name: Required. String. The product name
   * @param description: Optional. String. THe product description if any
   * @param photos: List. Product photos
   * @param variations: List. Variations of this product if any exists
   * @param shipping: Required. Object. How to ship this product
   *    Shipping properties
   *    @param method: Required. String. Available values are none , physical_shipping , digital_shipping , digital_membership
   *    @param declared_value: Optional. String. The declared value of the product.
   *    @param weight: Optional. Numeric. The weight of the product
   *    @param integration: Optional. String. The integration id of this shipping if any
   */
  #Search for a product containing `$query` value
  function product_search ($query) {
    $query = [
      'q' => $query,
    ];
    return $this->_send('/v1/product/search','GET',$query);
  }
  # List all products containing `$query` fields
  function product_list ($query) {
    return $this->_send('/v1/product','GET',$query);
  }
  #Find a product by id
  function product_get ($_id) {
    return $this->_send('/v1/product/'.$_id,'GET');
  }
  #Create a new product
  function product_create ($product) {
    return $this->_send('/v1/product','POST',$product);
  }
  #Updates a product
  function product_update ($_id,$product) {
    return $this->_send('/v1/product/'.$_id,'POST',$product);
  }
  #Deletes a product by id
  function product_delete ($_id) {
    return $this->_send('/v1/product/'.$_id,'DELETE');
  }

  # shipment methods
  /*
   * Shipment properties:
   * @param order_id: Required (auto generated on save). String. Identifier of the order
   * @param status: Required. String. Default unposted. The status of this shipment
   * @param approved: Required. Boolean. Default false. Indicates if the shipment was approved
   * @param time_approved: Optional. Number. The timestamp of when the shipment was approved
   * @param time_posted: Optional. Number. The timestamp of when the shipment was posted
   * @param time_cancelled: Optional. Number. The timestamp of when the shipment was cancelled
   * @param time_settled: Optional. Number. The timestamp of when the shipment was settled
   * @param auto_approved: Optional. Boolean. Indicates if the shipment was automatically approved or amnually approved
   * @param auto_approve_time: Optional. Number. The timestamp of when to auto approve a shipment
   * @param address: Required. Address. Where to deliver this shipment
   * @param customer: Required. String. Id of the customer of this shipment
   * @param ptime: Optional. Number
   * @param provider_history: Array. Object. Integration communication history
   * @param provider_details: Optional. Object. Extra provider details
   * @param line_items: Array. LineItem. Items to be delivered
   * @param objections: Array. Objections if any
   * @param integration: Optional. String. Identifier of the integration to use
   *
   *
   * Address properties:
   * @param name: Required. String. The receiver name
   * @param address: Required. String. The receiver address
   * @param address_2: Optional. String. The receiver address extra info
   * @param city: Required. String. The receiver city
   * @param state: Required. String. The receiver state
   * @param country: Required. String. The receiver country
   * @param zipcode: Required. Numeric. 5 digits. The receiver zip code
   *
   * LineItem properties:
   * @param product: Required. String. The product identifier
   * @param variation: Optional. String. The variation of the product, if it is a variation
   * @param is_variation: Required. Boolean. Default false. Indicates if this is a variation of the product
   * @param sku: Required. String. The product sku
   * @param details: Required. Object. Extra details
   */
  #creates a shipment
  function shipment_create ($shipment) {
    return $this->_send('/v1/shipment','POST',$shipment);
  }
  #find a shipment by id
  function shipment_get ($_id) {
    return $this->_send('/v1/shipment/'.$_id,'GET');
  }
  # add a line item to a shipment
  function shipment_add_item ($_id,$item) {
    return $this->_send('/v1/shipment/'.$_id.'/item/add','POST',$item);
  }
  # remove a item from a shipment by item id
  function shipment_remove_item_by_id ($_id,$_id_item) {
    return $this->_send('/v1/shipment/'.$_id.'/item/'.$_id_item.'/remove','POST');
  }
  # approves a shipment
  function shipment_approve ($_id) {
    return $this->_send('/v1/shipment/'.$_id.'/approve','POST');
  }
  # post a shipment to it's fulfillment provider for processing
  function shipment_post ($_id) {
    return $this->_send('/v1/shipment/'.$_id.'/post','POST');
  }
  #cancels a shipment
  function shipment_cancel ($_id) {
    return $this->_send('/v1/shipment/'.$_id.'/cancel','POST');
  }
  # approves an array of shipments (receives an array of shipment ids)
  function shipment_approve_bulk ($items) {
    $options = [
        'items' => $items,
    ];
    return $this->_send('/v1/shipment/approve_bulk','POST',$options);
  }
  #cancel an array of shipments (receives an array of shipment ids)
  function shipment_cancel_bulk ($items) {
    $options = [
        'items' => $items,
    ];
    return $this->_send('/v1/shipment/cancel_bulk','POST',$options);
  }

  # subscription methods
  /*
   * Subscription properties:
   * @param active: Required. Boolean. Default true. Indicates if this subscription is active
   * @param paused: Required. Boolean. Default false. Indicates if this subscription is paused
   * @param expired: Required. Boolean. Default false. Indicates if this subscription is expired
   * @param cancelled: Required. Boolean. Default false. Indicates if this subscription is cancelled
   * @param terminated: Required. Boolean. Default false. Indicates if this subscription is terminated
   * @param last_deactivation: Optional. Number. Indicates the timestamp of last deactivation (if any)
   * @param last_success: Optional. Number. Indicates the timestamp of the last success on this subscription
   * @param last_fail: Optional. Number. Indicates the timestamp of the last fail on this subscription
   * @param last_retry: Optional. Number. Indicates the timestamp of the last retry on this subscription
   * @param outstanding_transaction: Optional. String
   * @param customer: Required. String. Id of the customer of this subscription
   * @param customer_cday: Optional. Number
   * @param discount: Optional. String. Identifier of the discount on this subscription (if any)
   * @param gateway: Optional. String. The identifier of the payment gateway used on this subscription.
   * @param load_balancer: Optional. String. The load balancer used on this subscription
   * @param traffic_source: Optional. String
   * @param traffic_source_sub: Optional. String. Traffic source of this subscription
   * @param ltv_cents: Required. Number. The long time value of this subscription up to this moment (in cents)
   * @param cycle: Optional. Object. Cycle info of this subscription
   * @param retry_cycle: Optional. Object. Retry cycle info of this subscription
   * @param userdata: Optional. Object.
   * @param tags: Array. String. Some tags to identify this subscription
   * @param products: Array. The products of this subscription
   * @param offer: Optional. String. The identifier of the offer of this subscription
   * @param time_paused. Optional. Number. The last time this subscriptions was paused
   * @param time_unpaused. Optional. Number. The last time this subscriptions was unpaused
   * @param time_scheduled_unpaused. Optional. Number. The time to unpause this subscription
   * @param cycles_attempted. Required. Number. Default 0 . The number of cycles attempted
   * @param cycles_succeeded. Required. Number. Default 0 . The number of cycles succeeded
   * @param retry_cycles_attempted. Required. Number. Default 0 . The number of retry cycles attempted
   * @param skip_ranges. Array SkipRange. Info about when to skip
   * @param cday. Required. Number.
   * @param details_merged. Required. Boolean. Default false. Indicates if any detail was merged
   *
   * SkipRange properties:
   * @param min. Required. Number
   * @param max. Required. Number
   * @param reason. Required. String
   *
   */
  #creates a subscription
  function subscription_create ($details) {
    return $this->_send('/v1/subscription/create','POST',$details);
  }
  #find a subscription by id
  function subscription_view ($_id) {
    return $this->_send('/v1/subscription/'.$_id,'GET');
  }
  #update a subscription
  function subscription_update ($_id,$details) {
    return $this->_send('/v1/subscription/'.$_id.'/update','POST',$details);
  }
  #cancel a subscription
  function subscription_cancel ($_id) {
    return $this->_send('/v1/subscription/'.$_id.'/cancel','POST');
  }
  #renew a subscription (send a json with property renewal_charge on $details  to require a renewal
  # charge before allow the subscription to be reactivated)
  function subscription_renew ($_id,$details) {
    return $this->_send('/v1/subscription/'.$_id.'/renew','POST',$details);
  }
  #pause a subscription (send a json with property unpause_time to schedule when to unpause this subscription)
  function subscription_pause ($_id,$details) {
    return $this->_send('/v1/subscription/'.$_id.'/pause','POST',$details);
  }
  #unpause a subscription
  function subscription_unpause ($_id) {
    return $this->_send('/v1/subscription/'.$_id.'/unpause','POST');
  }
  #schedule when to unpause a subscription
  function subscription_set_scheduled_unpause ($_id,$details) {
    return $this->_send('/v1/subscription/'.$_id.'/pause','POST',$details);
  }
  #forgive outstanding of a subscription
  function subscription_forgive_outstanding ($_id) {
    return $this->_send('/v1/subscription/'.$_id.'/forgive_outstanding','POST');
  }
  #get subscription schedule
  function subscription_get_schedule ($_id,$limit = 10) {
    $options = [
        'limit' => $limit,
    ];
    return $this->_send('/v1/subscription/'.$_id.'/schedule','GET',$options);
  }
  #get the discount of this subscription
  function subscription_get_discount ($_id) {
    return $this->_send('/v1/subscription/'.$_id.'/discount','GET');
  }
  #adds a discount to a subscription
  function subscription_add_discount ($_id,$discount) {
    return $this->_send('/v1/subscription/'.$_id.'/discount/create','POST',$discount);
  }

  # transaction methods
  #get a transaction by id
  function transaction_get ($_id) {
    return $this->_send('/v1/transaction/'.$_id,'GET');
  }
  #mark a transaction as chargeback (send a json with _extra property on options to add extra info)
  function transaction_mark_chargeback ($_id,$options) {
    return $this->_send('/v1/transaction/'.$_id.'/mark_chargeback','POST',$options);
  }
  #mark an array of transactions as chargeback
  function transaction_mark_chargeback_bulk ($_ids) {
    $options=[
      'items' =>$_ids
    ];
    return $this->_send('/v1/transaction/mark_chargeback_bulk','POST',$options);
  }
  #unmark a transactions as chargeback  (send a json with _extra property on options to add extra info)
  function transaction_unmark_chargeback ($_id,$options) {
    return $this->_send('/v1/transaction/'.$_id.'/unmark_chargeback','POST',$options);
  }
  #refund a transaction
  function transaction_refund ($_id,$options) {
    return $this->_send('/v1/transaction/'.$_id.'/refund','POST',$options);
  }
  #refunds an array of transactions
  function transaction_refund_bulk ($_ids) {
    $options=[
      'items' =>$_ids
    ];
    return $this->_send('/v1/transaction/refund_bulk','POST',$options);
  }

  # report methods
  function get_transaction_report ($options) {
    return $this->_send('/v1/report/transactions','POST',$options);
  }

  # misc methods
  function userdata ($ip_address) {
    $query = [
      'ip' => $ip_address,
    ];
    return $this->_send('/v1/userdata','GET',$query);
  }
  #get card info about this card number
  function card_info ($card_number) {
    $query = [
      'number' => $card_number,
    ];
    return $this->_send('/v1/card_info','POST',$query);
  }

  # session methods
  #get session info by session hash
  function session_get ($hash) {
    return $this->_send('/v1/session/'.$hash,'GET');
  }
  #creates a session with this hash
  function session_create ($hash) {
    return $this->_send('/v1/session/'.$hash,'POST');
  }
  #destroy the session with this hash
  function session_destroy ($hash) {
    return $this->_send('/v1/session/'.$hash.'/destroy' ,'POST');
  }

  #email methods
  /*
   * Brand properties:
   * @param active. Required. Boolean. Default true. Indicates if is active
   * @param primary_time. Optional. Number.
   * @param name. Required. String. The name of this brand
   * @param domain. Required. String. Valid domain of this brand
   * @param website. Required. String. Must start with http:// or https://
   * @param image. Optional. BrandImage. Logo of this brand
   *
   * BrandImage properties:
   * @param url. Required. String. url of the  logo
   * @param width. Required. Number
   * @param height. Required. Number
   * @param file_type. Required. String. Possible values are bmp, jpg, jpeg, gif, png, svg
   */
  #list brands
  function email_brand_list ($query) {
    return $this->_send('/v1/email/brand/','GET', $query);
  }
  #find brand by id
  function email_brand_get ($_id) {
    return $this->_send('/v1/email/brand/'.$_id,'GET');
  }
  #create brand
  function email_brand_create ($brand) {
    return $this->_send('/v1/email/brand/' ,'POST', $brand);
  }
  #update brand
  function email_brand_update ($_id,$brand) {
    return $this->_send('/v1/email/brand/'.$_id ,'POST', $brand);
  }
  #delete brand by id
  function email_brand_delete ($_id) {
    return $this->_send('/v1/email/brand/'.$_id ,'DELETE');
  }

  #email methods
  /*
   * EmailTemplate properties:
   * @param active. Required. Boolean. Default true. Indicates if is active
   * @param name. Required. String. The name of this template
   * @param slug. Required. String. Unique
   * @param from. Required. String. Sender of this email template
   * @param subject. Required. String. Subject of this email template
   * @param html. Optional. String. The html content of the email
   * @param plain. Optional. String. If not using html, the content of email
   */
  #list email templates
  function email_template_list ($query) {
    return $this->_send('/v1/email/template/','GET', $query);
  }
  #find email template by id
  function email_template_get ($_id) {
    return $this->_send('/v1/email/template/'.$_id,'GET');
  }
  #create email template
  function email_template_create ($template) {
    return $this->_send('/v1/email/template/' ,'POST', $template);
  }
  #update email template
  function email_template_update ($_id,$template) {
    return $this->_send('/v1/email/template/'.$_id ,'POST', $template);
  }
  #delete email template
  function email_template_delete ($_id) {
    return $this->_send('/v1/email/template/'.$_id ,'DELETE');
  }
  #send email template
  function email_template_send ($_id,$data) {
    return $this->_send('/v1/email/template/'.$_id.'/send' ,'POST', $data);
  }

  #email methods
  /*
   * EmailCampaign properties:
   * @param active. Required. Boolean. Default true. Indicates if is active
   * @param name. Required. String. The name of this campaign
   * @param list. Required. String. The id of the email list of this campaign
   * @param template. Required. String. The id of email template of this campaign
   * @param brand. Required. String. The id of the brand used in this campaign
   * @param status. Required. String. Default idle. Possible values are 'idle' and 'queuing'
   * @param time_queued. Optional. Number
   *
   */
  #list email campaigns
  function email_campaign_list ($query) {
    return $this->_send('/v1/email/campaign/','GET', $query);
  }
  #find email campaign by id
  function email_campaign_get ($_id) {
    return $this->_send('/v1/email/campaign/'.$_id,'GET');
  }
  #create email campaign
  function email_campaign_create ($campaign) {
    return $this->_send('/v1/email/campaign/' ,'POST', $campaign);
  }
  #update email campaign
  function email_campaign_update ($_id,$campaign) {
    return $this->_send('/v1/email/campaign/'.$_id ,'POST', $campaign);
  }
  #delete email campaign
  function email_campaign_delete ($_id) {
    return $this->_send('/v1/email/campaign/'.$_id ,'DELETE');
  }
  #send test email from campaign
  function email_campaign_send_test_email ($email,$template,$brand) {
    $data = [
        'email' => $email,
        'template' => $template,
        'brand' => $brand
    ];
    return $this->_send('/v1/email/campaign/test_email' ,'POST', $data);
  }
  #enqueue email campaign to send
  function email_campaign_enqueue_to_send($_id) {
    return $this->_send('/v1/email/campaign/'.$_id.'/queue' ,'DELETE');
  }

  #email methods
  /*
   * EmailCampaign properties:
   * @param active. Required. Boolean. Default true. Indicates if is active
   * @param name. Required. String. The name of this campaign
   * @param approximate_count. Required. Number. Number of approximate customer's email oh this list
   * @param constraints. Array. String. Constraints of this list
   * @param last_count_update. Optional. Number
   */
  #list email lists
  function user_email_list_list ($query) {
    return $this->_send('/v1/email/list/','GET', $query);
  }
  #find email list by id
  function user_email_list_get ($_id) {
    return $this->_send('/v1/email/list/'.$_id,'GET');
  }
  #create email list
  function user_email_list_create ($data) {
    return $this->_send('/v1/email/list/' ,'POST', $data);
  }
  #update email list
  function user_email_list_update ($_id,$data) {
    return $this->_send('/v1/email/list/'.$_id ,'POST', $data);
  }
  #delete email list
  function user_email_list_delete ($_id) {
    return $this->_send('/v1/email/list/'.$_id ,'DELETE');
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


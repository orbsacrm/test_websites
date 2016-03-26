Orbsa
========
<img src="http://orbsa.com/static/logo/default_trim_white.svg" align="right" width="300px" />

PHP api description of Orbsa (using the [lib](https://github.com/orbsacrm/taky-billing/blob/feature/php-lib/test_websites/php_example_offer/deps/class.billing.php))

Table of contents
========

- [Customer](#customer)
- [Charge](#charge)
- [Offer](#offer)
- [Product](#product)
- [Shipment](#shipment)
- [Subscription](#subscription)
- [Transaction](#transaction)
- [Others](#others)
- [Session](#session)
- [Brand](#brand)
- [Template](#template)
- [Campaign](#campaign)
- [Usage](#usage)
- [One Click Upsell](#One Click Upsell)

Customer
========
```
* Customer properties:
@param first: Required. String. The customer first name
@param last: Required. String. The customer last name
@param address: Required. String. The customer address
@param city: Required. String. The customer city
@param state: Required. String. The customer state
@param country: Required. String. The customer country
@param zipcode: Required. Numeric. 5 digits. The customer zip code
@param email: Required. String. The customer email
```

* customer_create ($customer) 
* customer_view ($_id) 
* customer_update_billing ($_id,$details)
```
 * Accepted details fields
 @param cvv or cvc or ccv or verification_code or verification_value: Required. Numeric. Accept any of this fields
 * as customer's card verification code
 @param expiration or expires or mmdd or (month and year) : Required. String. The month and year of credit card expiration
 @param number: Required: String (only numbers). The credit card number
 @param zipcode: Required. Numeric. 5 digits. The customer's credit card zip code
 @param gateway: Optional. String. Id of gateway to use
 @param load_balancer: Optional. String. Id of load balancer to use
 ```
 
* customer_update_shipping ($_id,$details)
```
 * Accepted details fields
 @param first: Required. String. The customer first name
 @param last: Required. String. The customer last name
 @param address: Required. String. The customer address
 @param city: Required. String. The customer city
 @param state: Required. String. The customer state
 @param country: Required. String. The customer country
 @param zipcode: Required. Numeric. 5 digits. The customer zip code
 ```
 
* customer_update_form ($_id,$details) 
* customer_update_details ($_id,$details) 
* customer_append_form ($_id,$details)
* customer_clear_form ($_id,$details) 
* customer_list ($options=[])
```
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
 ```
 
* customer_get_memberships ($_id)
* customer_add_history ($_id,$details)
* customer_get_history ($_id)
* customer_charge ($_id,$charge) 
``` 
Available fields in charge
 @param amount . Required. Numeric. The amount to charge the customer
 @param future_date. Optional. Numeric (timestamp). When to charge the customer (omit to charge user now)
 @param disable_credit. Optional. Boolean. Enable or disable the use of credit on this charge
 @param description. Optional. String. Some extra description if needed
```

* customer_add_credits ($_id,$amount) 
* customer_subtract_credits ($_id,$amount)
* customer_set_credits ($_id,$amount) 
* customer_clear_credits ($_id)

Charge
========
```
 * Charge properties:
 @param status: Optional. String. Default: pending. All options are pending , processing , success , failure
 @param error: Optional. String. An error message if any error occurred
 @param raw: Optional. String.
 @param token: Optional. String.
 @param description: Optional. String. Some description of the charge if needed
 @param context: Optional. String. All options are subscription_cycle_recapture , subscription_cycle_charge ,
 * customer_charge , initial_subscription_charge , flat_offer_charge , subscription_renewal_charge
 @param amount: Required. Numeric. The amount to charge
 @param customer: Required. String. The id of the customer being billed
 @param charged_at: Optional. Date. When the customer was charged
 @param scheduled_to: Optional. Date. When to execute the charge
 @param allow_credit: Optional. Boolean. Default false. If user credit can be used in this charge
 @param processing_attempts: List. Processing attempts info. Contains fields started_at (date), error (String), raw (String),
 * worker_pid (String)
 @param extra: Optional. Object. Any extra info needed
 ```
* charge_list ($query)
* charge_get ($_id)
* charge_update ($_id,$charge)
* charge_delete ($_id)

Offer
========
```
 * Offer properties:
 @param active: Optional. Boolean. Default true. True if this offer is active
 @param name: Required. String. The offer name
 @param description: Optional. String. Any description to this offer if any needed
 @param type: Required. String. Available values are 'flat' or 'cycle'
 @param price: Required. String. The offer price
 @param products: List. String. A list of product ids of this offer
 @param cycle: Optional. Object. Info about the offer cycle
 @param retry_cycle. Optional. Object. Info about the cycle of retries for this offer
 @param restrictions. List. Restrictions for this offer if any
 ```

* offer_view ($_id)
* offer_search ($offer)
* offer_purchase ($customer_id,$offer_id,$options=[])

Product
========
```
 * Product properties:
 @param active: Optional. Boolean. Default true. True if the product is active
 @param sku: Required. String. The product sku
 @param name: Required. String. The product name
 @param description: Optional. String. THe product description if any
 @param photos: List. Product photos
 @param variations: List. Variations of this product if any exists
 @param shipping: Required. Object. How to ship this product
 *    Shipping properties
 *    @param method: Required. String. Available values are none , physical_shipping , digital_shipping , digital_membership
 *    @param declared_value: Optional. String. The declared value of the product.
 *    @param weight: Optional. Numeric. The weight of the product
 *    @param integration: Optional. String. The integration id of this shipping if any
 ```
 
* product_search ($query)
* product_list ($query)
* product_get ($_id)
* product_create ($product)
* product_update ($_id,$product)
* product_delete ($_id)

Shipment
========
```
 * Shipment properties:
 @param order_id: Required (auto generated on save). String. Identifier of the order
 @param status: Required. String. Default unposted. The status of this shipment
 @param approved: Required. Boolean. Default false. Indicates if the shipment was approved
 @param time_approved: Optional. Number. The timestamp of when the shipment was approved
 @param time_posted: Optional. Number. The timestamp of when the shipment was posted
 @param time_cancelled: Optional. Number. The timestamp of when the shipment was cancelled
 @param time_settled: Optional. Number. The timestamp of when the shipment was settled
 @param auto_approved: Optional. Boolean. Indicates if the shipment was automatically approved or amnually approved
 @param auto_approve_time: Optional. Number. The timestamp of when to auto approve a shipment
 @param address: Required. Address. Where to deliver this shipment
 @param customer: Required. String. Id of the customer of this shipment
 @param ptime: Optional. Number
 @param provider_history: Array. Object. Integration communication history
 @param provider_details: Optional. Object. Extra provider details
 @param line_items: Array. LineItem. Items to be delivered
 @param objections: Array. Objections if any
 @param integration: Optional. String. Identifier of the integration to use
 *
 *
 * Address properties:
 @param name: Required. String. The receiver name
 @param address: Required. String. The receiver address
 @param address_2: Optional. String. The receiver address extra info
 @param city: Required. String. The receiver city
 @param state: Required. String. The receiver state
 @param country: Required. String. The receiver country
 @param zipcode: Required. Numeric. 5 digits. The receiver zip code
 *
 * LineItem properties:
 @param product: Required. String. The product identifier
 @param variation: Optional. String. The variation of the product, if it is a variation
 @param is_variation: Required. Boolean. Default false. Indicates if this is a variation of the product
 @param sku: Required. String. The product sku
 @param details: Required. Object. Extra details
 ```
 
* shipment_create ($shipment)
* shipment_get ($_id)
* shipment_add_item ($_id,$item)
* shipment_remove_item_by_id ($_id,$_id_item)
* shipment_approve ($_id)
* shipment_post ($_id)
* shipment_cancel ($_id)
* shipment_approve_bulk ($items)
* shipment_cancel_bulk ($items)

Subscription
========
```
 * Subscription properties:
 @param active: Required. Boolean. Default true. Indicates if this subscription is active
 @param paused: Required. Boolean. Default false. Indicates if this subscription is paused
 @param expired: Required. Boolean. Default false. Indicates if this subscription is expired
 @param cancelled: Required. Boolean. Default false. Indicates if this subscription is cancelled
 @param terminated: Required. Boolean. Default false. Indicates if this subscription is terminated
 @param last_deactivation: Optional. Number. Indicates the timestamp of last deactivation (if any)
 @param last_success: Optional. Number. Indicates the timestamp of the last success on this subscription
 @param last_fail: Optional. Number. Indicates the timestamp of the last fail on this subscription
 @param last_retry: Optional. Number. Indicates the timestamp of the last retry on this subscription
 @param outstanding_transaction: Optional. String
 @param customer: Required. String. Id of the customer of this subscription
 @param customer_cday: Optional. Number
 @param discount: Optional. String. Identifier of the discount on this subscription (if any)
 @param gateway: Optional. String. The identifier of the payment gateway used on this subscription.
 @param load_balancer: Optional. String. The load balancer used on this subscription
 @param traffic_source: Optional. String
 @param traffic_source_sub: Optional. String. Traffic source of this subscription
 @param ltv_cents: Required. Number. The long time value of this subscription up to this moment (in cents)
 @param cycle: Optional. Object. Cycle info of this subscription
 @param retry_cycle: Optional. Object. Retry cycle info of this subscription
 @param userdata: Optional. Object.
 @param tags: Array. String. Some tags to identify this subscription
 @param products: Array. The products of this subscription
 @param offer: Optional. String. The identifier of the offer of this subscription
 @param time_paused. Optional. Number. The last time this subscriptions was paused
 @param time_unpaused. Optional. Number. The last time this subscriptions was unpaused
 @param time_scheduled_unpaused. Optional. Number. The time to unpause this subscription
 @param cycles_attempted. Required. Number. Default 0 . The number of cycles attempted
 @param cycles_succeeded. Required. Number. Default 0 . The number of cycles succeeded
 @param retry_cycles_attempted. Required. Number. Default 0 . The number of retry cycles attempted
 @param skip_ranges. Array SkipRange. Info about when to skip
 @param cday. Required. Number.
 @param details_merged. Required. Boolean. Default false. Indicates if any detail was merged
 *
 * SkipRange properties:
 @param min. Required. Number
 @param max. Required. Number
 @param reason. Required. String
 *
 ```
 
* subscription_create ($details)
* subscription_view ($_id)
* subscription_update ($_id,$details)
* subscription_cancel ($_id)
* subscription_renew ($_id,$details)
* subscription_pause ($_id,$details)
* subscription_unpause ($_id)
* subscription_set_scheduled_unpause ($_id,$details)
* subscription_forgive_outstanding ($_id)
* subscription_get_schedule ($_id,$limit = 10)
* subscription_get_discount ($_id)
* subscription_add_discount ($_id,$discount)
* subscription_remove_discount ($_id)

Transaction
========

* transaction_get ($_id)
* transaction_mark_chargeback
* transaction_mark_chargeback_bulk ($_ids)
* transaction_unmark_chargeback ($_id,$options)
```unmark a transactions as chargeback  (send a json with _extra property on options to add extra info)```

* transaction_refund ($_id,$options)
* transaction_refund_bulk ($_ids)
* get_transaction_report ($options)

Session
========
* session_get ($hash)
* session_create ($hash)
* session_destroy ($hash)

Brand
========

```
 * Brand properties:
 @param active. Required. Boolean. Default true. Indicates if is active
 @param primary_time. Optional. Number.
 @param name. Required. String. The name of this brand
 @param domain. Required. String. Valid domain of this brand
 @param website. Required. String. Must start with http:// or https://
 @param image. Optional. BrandImage. Logo of this brand
 *
 * BrandImage properties:
 @param url. Required. String. url of the  logo
 @param width. Required. Number
 @param height. Required. Number
 @param file_type. Required. String. Possible values are bmp, jpg, jpeg, gif, png, svg
 ```
 
* email_brand_list ($query)
* email_brand_get ($_id)
* email_brand_create ($brand)
* email_brand_update ($_id,$brand)
* email_brand_delete ($_id)

Template
========
```
 * EmailTemplate properties:
 @param active. Required. Boolean. Default true. Indicates if is active
 @param name. Required. String. The name of this template
 @param slug. Required. String. Unique
 @param from. Required. String. Sender of this email template
 @param subject. Required. String. Subject of this email template
 @param html. Optional. String. The html content of the email
 @param plain. Optional. String. If not using html, the content of email
 ```
 
* email_template_list ($query)
* email_template_get ($_id)
* email_template_create ($template)
* email_template_update ($_id,$template)
* email_template_delete ($_id)
* email_template_send ($_id,$data)

Campaign
========
```
 * EmailCampaign properties:
 @param active. Required. Boolean. Default true. Indicates if is active
 @param name. Required. String. The name of this campaign
 @param list. Required. String. The id of the email list of this campaign
 @param template. Required. String. The id of email template of this campaign
 @param brand. Required. String. The id of the brand used in this campaign
 @param status. Required. String. Default idle. Possible values are 'idle' and 'queuing'
 @param time_queued. Optional. Number
 *
 ```
 
* email_campaign_list ($query)
* email_campaign_get ($_id)
* email_campaign_create ($campaign)
* email_campaign_update ($_id,$campaign)
* email_campaign_delete ($_id)
* email_campaign_send_test_email ($email,$template,$brand)
* email_campaign_enqueue_to_send($_id)

One Click Upsell
========

* email_oneclick_create ($info)
* email_oneclick_list ()
* email_oneclick_get ($_id)
* email_oneclick_update ($id,$info)
* email_oneclick_delete ($_id)
* email_oneclick_create_token ($id,$customer_id)
* email_oneclick_token_get ($token_id)
* email_oneclick_redeem_token ($token_id)
* email_oneclick_delete_token ($token_id)

List
========
```
 * EmailList properties:
 @param active. Required. Boolean. Default true. Indicates if is active
 @param name. Required. String. The name of this campaign
 @param approximate_count. Required. Number. Number of approximate customer's email oh this list
 @param constraints. Array. String. Constraints of this list
 @param last_count_update. Optional. Number
 ```
* user_email_list_list ($query)
* user_email_list_get ($_id)
* user_email_list_create ($data)
* user_email_list_update ($_id,$data)
* user_email_list_delete ($_id)

Others
========
* userdata ($ip_address)
* card_info ($card_number)

Usage
========
```
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
```


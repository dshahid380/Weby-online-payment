<?php
  require_once('vendor/autoload.php');
  require_once('config/db.php');
  require_once('lib/pdo_db.php');
  require_once('models/Customer.php');
  require_once('models/Transaction.php');

  \Stripe\Stripe::setApiKey('sk_YOURSERVERKEY');

 
 $POST = filter_var_array($_POST, FILTER_SANITIZE_STRING);

 $first_name = $POST['first_name'];
 $last_name = $POST['last_name'];
 $email = $POST['email'];
 $token = $POST['stripeToken'];


$customer = \Stripe\Customer::create(array(
  "email" => $email,
  "source" => $token
));


$charge = \Stripe\Charge::create(array(
  "amount" => 15000,
  "currency" => "usd",
  "description" => "Music Event Registration ",
  "customer" => $customer->id
));


$customerData = [
  'id' => $charge->customer,
  'first_name' => $first_name,
  'last_name' => $last_name,
  'email' => $email
];


$customer = new Customer();


$customer->addCustomer($customerData);



$transactionData = [
  'id' => $charge->id,
  'customer_id' => $charge->customer,
  'product' => $charge->description,
  'amount' => $charge->amount,
  'currency' => $charge->currency,
  'status' => $charge->status,
];


$transaction = new Transaction();
$transaction->addTransaction($transactionData);
header('Location: success.php?tid='.$charge->id.'&product='.$charge->description);
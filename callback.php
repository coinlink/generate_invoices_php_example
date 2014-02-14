<?php

include 'include.php';

function callback_is_valid($signature, $key, $secret, $nonce, $client_id) {
    $good_sign = strtoupper(hash_hmac('sha256', $nonce.$client_id.$key, $secret));
    return ($signature == $good_sign);
}

if(!isset($_SERVER['HTTP_SIGNATURE']) || !isset($_SERVER['HTTP_KEY']) || !callback_is_valid($_SERVER['HTTP_SIGNATURE'], $_SERVER['HTTP_KEY'], $secret, $_GET['nonce'], $_GET['client_id'])) die;

mysql_connect($mysql_host, $mysql_username, $mysql_password) or die(__LINE__ . ' Invalid connect: ' . mysql_error());

mysql_select_db($mysql_database) or die( "Unable to select database. Run setup first.");

$invoice_id = $_GET['nonce'];
$transaction_hash = $_GET['transaction_hash'];
$timereceived = $_GET['time_received'];
$input_address = isset($_GET['input_address'])?$_GET['input_address']:"";
$value_in_btc = $_GET['value'];
$transaction_status = $_GET['transaction_status'];

if ($transaction_status == 'CONFIRMED') {
  //Add the invoice to the database
  $result = mysql_query("INSERT INTO invoice_payments (invoice_id, transaction_hash, value) values($invoice_id, '$transaction_hash', $value_in_btc)");

  //Delete from pending
  mysql_query("delete from pending_invoice_payments where invoice_id = $invoice_id limit 1");

} else if($transaction_status == 'PENDING') {
   //Waiting for confirmations
   //create a pending payment entry
   mysql_query("INSERT INTO pending_invoice_payments (invoice_id, transaction_hash, value) values($invoice_id, '$transaction_hash', $value_in_btc)");

   echo "Waiting for confirmations";
}

?>

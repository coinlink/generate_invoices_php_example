<?php
$coinlink_root = "http://www.coinlink.net/";
//API Authentication
$client_id = "YOUR_COINLINK_CUSTOMER_ID";
$key = "KEY_GENERATED_IN_ACCOUNT_SECURITY_APIACCESS";
$secret = "SECRET_GENERATED_IN_ACCOUNT_SECURITY_APIACCESS";

$address = "YOUR_BITCOIN_ADDRESS";
$callback = "http://yoursite.com/callback.php";

//if instant_only value is set to 1, user will be forced to have an account in COINLINK to be able to instantly pay in bitcoins
$instant_only = 0;

//You can specify the number of confirmations you think it is secure between 1 and 6 based on the amount of the generated invoice. The status of the transaction is returned in your callback script either CONFIRMED if requested required confirmations is equal to its number of confirmations or PENDING if its number of confirmations is still not achieved as per your request.
$required_confirmations = 3;

//User will be redirected to the success and failure urls when he uses his COINLINK account to instantly pay in bitcoins.
$return_failure = "http://yoursite.com/failure.php";
$return_success = "http://yoursite.com/success.php";

//Database
$mysql_host = 'localhost';
$mysql_username = 'root';
$mysql_password = 'root';
$mysql_database = 'mysite';
?>

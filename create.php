<?php
include 'include.php';

//If your amount is in USD currency//
$amount_usd = 100;

//If your amount is already in BTC currency//
$amount_btc = 0;

//If your amount is in USD currency. This amount needs to be converted to BTC currency//
if(!empty($amount_usd) && $amount_usd > 0) {
	$amount_arr = json_decode(file_get_contents($coinlink_root . "apis/tobtc?currency=USD&value=".$amount_usd));
	$amount = $amount_arr->value_btc;
}
else if(!empty($amount_btc) && $amount_btc > 0) {
	$amount = $amount_btc;
}
$request_arr = array();
$request_arr['amount'] = $amount;
$request_arr['address'] = $address;
$request_arr['callback'] = urlencode($callback);
$request_arr['required_confirmations'] = $required_confirmations;
$request_arr['instant_only'] = $instant_only;
$request_arr['return_failure'] = urlencode($return_failure);
$request_arr['return_success'] = urlencode($return_success);

mysql_connect($mysql_host, $mysql_username, $mysql_password) or die(__LINE__ . ' Invalid connect: ' . mysql_error());

mysql_select_db($mysql_database) or die( "Unable to select database. Run setup first.");

//Add the invoice to the database
$result = mysql_query("INSERT INTO invoices (invoice_id, price_in_usd, price_in_btc) values($invoice_id, $amount_usd, $amount_btc)");
    
if (!$result) {
    die(__LINE__ . ' Invalid query: ' . mysql_error());
}

$invoice_id = mysql_insert_id();

$result = json_decode(send_request($key, $secret, $coinlink_root.'apis/receivep?mode=create', $invoice_id, $client_id, $request_arr));

if(!empty($result->error)) {
	echo $result->error;
} else if(!empty($result->transaction) && !empty($result->payment_url)) {
	header('location:'.	$result->payment_url);
}
function send_request($key, $secret, $path, $nonce='', $client_id, array $req = array()) {
 	if(empty($nonce)) {
	// generate a nonce as microtime, with as-string handling to avoid problems with 32bits systems
	$mt = explode(' ', microtime());
		$req['nonce'] = $mt[1].substr($mt[0], 2, 6);
	} else {
		$req['nonce'] = $nonce;
	}
	// generate the POST data string
	$post_data = http_build_query($req, '', '&');
 
	// generate the extra headers
	$headers = array(
		'Key: '.$key,
		'Signature: '.strtoupper(hash_hmac('sha256', $nonce.$client_id.$key, $secret)),
	);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	curl_setopt($ch, CURLOPT_URL, $path.'&'.$post_data);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
 
	// run the query
	$res = curl_exec($ch);
	return $res;
}
?>

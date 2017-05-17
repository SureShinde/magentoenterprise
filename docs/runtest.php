<?php


$nonce = substr(md5(uniqid('nonce_', true)), 0, 16);

$product1 = array('sku' => 'Designer Red Chair 12lbs', 'qty' => '10');
$testProducts = array();
$testProducts[] = $product1;
$testAddress = array('City' => 'Dallas', 'State' => 'CA', 'Zip' => '90210', 'Country' => 'US');
$testData = array('Products' => $testProducts, 'Address' => $testAddress);

$encoded = http_build_query($testData);
$temprealm = "http://localhost/70/Apishipping/api/rest/apishipping/" . $encoded;
$realm = urlencode($temprealm);
$oauth_version = "1.0";
$oauth_signature_method = "HMAC-SHA1";
$oauth_consumer_key = "77whvasgf9889c3fw69qammjr612gufn";
$oauth_access_token = "fhywbgoda4dfdvkb0ga8iwzmaxnp1fck";
$oauth_method = "GET";
$oauth_timestamp = time();
$algo = "sha1";
$key = "ww0dk5dj9ycun643turdk6pke2erxi5e&sw156n3qo1yldkqty0p8litbqtgk22jj"; //consumer secret & token secret //Both are used in generate signature
$data = "oauth_consumer_key=" . $oauth_consumer_key . "&oauth_nonce=" . $nonce . "&oauth_signature_method=" . $oauth_signature_method . "&oauth_timestamp=" . $oauth_timestamp . "&oauth_token=" . $oauth_access_token . "&oauth_version=" . $oauth_version;

$send_data = $oauth_method . "&" . $realm . "&" . urlencode($data);
$sign = hash_hmac($algo, $send_data, $key, 1); // consumer key and token secrat used here
$fin_sign = base64_encode($sign);
$curl = curl_init();


curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization : OAuth realm=' . $realm . ', oauth_version="1.0", oauth_signature_method="HMAC-SHA1", oauth_nonce="' . $nonce . '", oauth_timestamp="' . $oauth_timestamp . '", oauth_consumer_key=' . $oauth_consumer_key . ', oauth_token=' . $oauth_access_token . ', oauth_signature="' . $fin_sign . '"'));

curl_setopt($curl, CURLOPT_URL, $temprealm);
$xml = curl_exec($curl);

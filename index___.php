<?php
error_reporting(0);
session_start();

$config['ApiKey']     = 'a87e79a6b77228382a97f534ed72d03e'; // get in https://antibot.pw/developers
$config['blocktype']  = 3;


function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP')){
        $ipaddress = getenv('HTTP_CLIENT_IP');
    }
    if(getenv('HTTP_X_FORWARDED_FOR')){
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    }
    if(getenv('HTTP_X_FORWARDED')){
        $ipaddress = getenv('HTTP_X_FORWARDED');
    }
    if(getenv('HTTP_FORWARDED_FOR')){
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    }
    if(getenv('HTTP_FORWARDED')){
       $ipaddress = getenv('HTTP_FORWARDED');
    }
    if(getenv('REMOTE_ADDR')){
        $ipaddress = getenv('REMOTE_ADDR');
    }
    $ipaddress = explode(",",  $ipaddress);
    return $ipaddress[0];
}

if($_SESSION['check'] == false){

  $ipNe = get_client_ip();

    if($ipNe == '::1' || $ipNe == '127.0.0.1'){
      $ipNe = '8.8.8.8';
    }

  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://antibot.pw/api/check-visitor.php?ip=".$ipNe."&block=".$config['blocktype']."&apikey=".$config['ApiKey'],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_HTTPHEADER => array(
      "content-type: application/x-www-form-urlencoded",
    ),
  ));

  $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);

  $json = json_decode($response,true);
  if($json['is_bot'] == 1){
    $_SESSION['check']  = true;
    $_SESSION['is_bot'] = true;
    die( header("HTTP/1.1 401 Unauthorized") );
      exit();
  }
}

if($_SESSION['check'] == true && $_SESSION['is_bot'] == true){
  die( header("HTTP/1.1 401 Unauthorized") );
    exit();
}
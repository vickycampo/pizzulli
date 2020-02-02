<?php
//phpinfo();
function getLocationByIP($ip){
 if($ip!=""){$ipip=$ip;}else{$ipip=getip();}

 //$tags = get_meta_tags('http://www.geobytes.com/IpLocator.htm?GetLocation&template=php3.txt&IpAddress='.$ipip);
 
 $geoPlugin_array = unserialize(@file_get_contents('http://www.geoplugin.net/php.gp?ip=' . $ipip) );
 $country = $geoPlugin_array["geoplugin_countryName"];
 $city =    $geoPlugin_array["geoplugin_city"];


 //$ip_info = file_get_contents("http://freegeoip.net/json/".$ipip);
 //var_dump($geoPlugin_array);
 //$ip_obj = json_decode($ip_info);
 //return $ip_obj->city.", ".$ip_obj->region_name.", ".$ip_obj->country_name;

 if($country=="Anonymous Proxy"){
   return "at an unknown location (from IP address ".$ipip.")";
 }else if($city=="" && $country==""){
   return "at an unknown location (from IP address ".$ipip.")";
 }else if($city==""){
   return "in ".$country." (from IP address ".$ipip.")";
 }else{
   return "in ".$city.", ".$country." (from IP address ".$ipip.")";
 }
 // return $ip_info;
}

function getLocationByIPnoIP($ip){
 if($ip!=""){$ipip=$ip;}else{$ipip=getip();}
 
 //$tags = get_meta_tags('http://www.geobytes.com/IpLocator.htm?GetLocation&template=php3.txt&IpAddress='.$ipip);
 $geoPlugin_array = unserialize( file_get_contents('http://www.geoplugin.net/php.gp?ip=' . $ipip) );
 $country = $geoPlugin_array["geoplugin_countryName"];
 $city = $geoPlugin_array["geoplugin_city"];
 $code = $geoPlugin_array["geoplugin_countryCode"];
 if($code=="CA" || $code=="US"){
    $code = $geoPlugin_array["geoplugin_regionCode"];
 }
 //var_dump($geoPlugin_array);

 $result = [];
 //$ip_info = file_get_contents("http://freegeoip.net/json/".$ipip);

// $ip_obj = json_decode($ip_info);
 if($country=="Anonymous Proxy"){
  $result['city'] = 'Anonymous';
  $result['code'] = $code;
  return $result; 
 }else if($city=="" && $country==""){
  $result['city'] = 'Anonymous';
  $result['code'] = $code;
  return $result; 
 }else if($city==""){
  $result['city'] = $country;
  $result['code'] = $code;
  return $result; 
 }else{
  $result['city'] = $city;
  $result['code'] = $code;
  return $result; 
 }
}


function validip($ip) {
 
if (!empty($ip) && ip2long($ip)!=-1) {
 
$reserved_ips = array (
 
array('0.0.0.0','2.255.255.255'),
 
array('10.0.0.0','10.255.255.255'),
 
array('127.0.0.0','127.255.255.255'),
 
array('169.254.0.0','169.254.255.255'),
 
array('172.16.0.0','172.31.255.255'),
 
array('192.0.2.0','192.0.2.255'),
 
array('192.168.0.0','192.168.255.255'),
 
array('255.255.255.0','255.255.255.255')
 
);
 
 
foreach ($reserved_ips as $r) {
 
$min = ip2long($r[0]);
 
$max = ip2long($r[1]);
 
if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;
 
}
 
return true;
 
} else {
 
return false;
 
}
 }
 
 function getip() {
 
if (validip($_SERVER["HTTP_CLIENT_IP"])) {
 
return $_SERVER["HTTP_CLIENT_IP"];
 
}
 
foreach (explode(",",$_SERVER["HTTP_X_FORWARDED_FOR"]) as $ip) {
 
if (validip(trim($ip))) {
 
return $ip;
 
}
 
}
 
if (validip($_SERVER["HTTP_X_FORWARDED"])) {
 
return $_SERVER["HTTP_X_FORWARDED"];
 
} elseif (validip($_SERVER["HTTP_FORWARDED_FOR"])) {
 
return $_SERVER["HTTP_FORWARDED_FOR"];
 
} elseif (validip($_SERVER["HTTP_FORWARDED"])) {
 
return $_SERVER["HTTP_FORWARDED"];
 
} elseif (validip($_SERVER["HTTP_X_FORWARDED"])) {
 
return $_SERVER["HTTP_X_FORWARDED"];
 
} else {
 
return $_SERVER["REMOTE_ADDR"];
 
}
 }

function getBrowserGeo() 
{ 
    $u_agent = $_SERVER['HTTP_USER_AGENT']; 
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version= "";

    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    }
    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    }
    elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
    }
    
    // Next get the name of the useragent yes seperately and for good reason
    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) 
    { 
        $bname = 'Internet Explorer'; 
        $ub = "MSIE"; 
    } 
    elseif(preg_match('/Firefox/i',$u_agent)) 
    { 
        $bname = 'Mozilla Firefox'; 
        $ub = "Firefox"; 
    } 
    elseif(preg_match('/Chrome/i',$u_agent)) 
    { 
        $bname = 'Google Chrome'; 
        $ub = "Chrome"; 
    } 
    elseif(preg_match('/Safari/i',$u_agent)) 
    { 
        $bname = 'Apple Safari'; 
        $ub = "Safari"; 
    } 
    elseif(preg_match('/Opera/i',$u_agent)) 
    { 
        $bname = 'Opera'; 
        $ub = "Opera"; 
    } 
    elseif(preg_match('/Netscape/i',$u_agent)) 
    { 
        $bname = 'Netscape'; 
        $ub = "Netscape"; 
    } 
    
    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }
    
    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
            $version= $matches['version'][0];
        }
        else {
            $version= $matches['version'][1];
        }
    }
    else {
        $version= $matches['version'][0];
    }
    
    // check if we have a number
    if ($version==null || $version=="") {$version="?";}
    /*
    return array(
        'userAgent' => $u_agent,
        'name'      => $bname,
        'version'   => $version,
        'platform'  => $platform,
        'pattern'    => $pattern
    );
    */
    return $ub;
} 
 
?>
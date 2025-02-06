<?php

error_reporting(0);

include("allfunc.php");

$uri = $_SERVER["REQUEST_URI"];
$lstslg = explode("/", explode("?", $uri)[0])[count(explode("/", explode("?", $uri)[0])) - 1];

if(explode(".", $lstslg)[1] == "mpd"){

$mpdbdy = curl_resp("https://linearjitp-playback.astro.com.my/dash-wv/dashiso/2504/default_primary.mpd", "RBD");
$cntnttpe = curl_resp("https://linearjitp-playback.astro.com.my/dash-wv/dashiso/2504/default_primary.mpd", "RHDR", "", "", "", "content-type");

header("Content-Type: ".$cntnttpe);
header("access-control-allow-origin: *");
echo add_mpdbaseurl($mpdbdy, "dash/");

}else{
$slg = explode("dash/", $uri)[1];

if(!empty($slg)){

$surl = "https://linearjitp-playback.astro.com.my/dash-wv/dashiso/2504/".$slg;

$cntnttpe = curl_resp($url, "RHDR", "", "", "", "content-type");

header("Content-Type: ".$cntnttpe);
header("access-control-allow-origin: *");

echo curl_resp($url, "RBD");

}else{
http_response_code(404);
}
}

?>
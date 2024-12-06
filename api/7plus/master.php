<?php

error_reporting(0);

include("allfunc.php");

ob_start();
$stream_id = $_REQUEST["stream_id"];
$sgmnts = $_REQUEST["sgmnts"];
$uri = $_SERVER["REQUEST_URI"];
ob_end_clean();

$lstslg = explode("/", explode("?", $uri)[0])[count(explode("/", explode("?", $uri)[0])) - 1];

if(explode(".", $lstslg)[1] == "m3u8" && !empty($stream_id)){

$ch_data = explode("&deliveryId", explode("accountId=", json_decode(curl_resp("https://component-cdn.swm.digital/content/live-tv?channel-id=".$stream_id."&platform-id=web&market-id=4&platform-version=1.0.99267&api-version=4.9&signedup=true", "RBD"), true)["items"][0]["videoUrl"])[1])[0];

$hlsUrl = curl_resp(json_decode(curl_resp("https://videoservice.swm.digital/playback?appId=7plus&deviceType=web&platformType=web&accountId=".$ch_data."&deliveryId=csai&videoType=live", "RBD"), true)["media"]["sources"][0]["src"], "RHDR", "", "", "", "location");

if(!empty($hlsUrl)){

$bdy = curl_resp($hlsUrl, "RBD")."\n";

$cntnttpe = curl_resp($hlsUrl, "RHDR", "", "", "", "content-type");

foreach(find_all_values($bdy, "\nhttps://", "\n") as $strngi=>$strngv){

$qury = explode("?", $strngv)[1];

$strmurl = "https://".explode(explode("/", explode("?", $strngv)[0])[count(explode("/", explode("?", $strngv)[0])) - 1], $strngv)[0];

$strmtkn = crypt2('{"qury": "'.$qury.'", "stream_url": "'.$strmurl.'"}', "ENC", "ML", "7+_hls_PROXY@DJTM", "24h");

$findi[] = "https://".$strngv;
$rplci[] = explode("/", explode("?", $strngv)[0])[count(explode("/", explode("?", $strngv)[0])) - 1]."?sgmnts=".$strmtkn;
}

foreach(find_all_values($bdy, 'URI="https://', '"') as $strngi=>$strngv){

$qury = explode("?", $strngv)[1];

$strmurl = "https://".explode(explode("/", explode("?", $strngv)[0])[count(explode("/", explode("?", $strngv)[0])) - 1], $strngv)[0];

$strmtkn = crypt2('{"qury": "'.$qury.'", "stream_url": "'.$strmurl.'"}', "ENC", "ML", "7+_hls_PROXY@DJTM", "24h");

$findii[] = "https://".$strngv;
$rplcii[] = explode("/", explode("?", $strngv)[0])[count(explode("/", explode("?", $strngv)[0])) - 1]."?sgmnts=".$strmtkn;
}

foreach(find_all_values($bdy, "\nhttp://", "\n") as $strngi=>$strngv){

$qury = explode("?", $strngv)[1];

$strmurl = "http://".explode(explode("/", explode("?", $strngv)[0])[count(explode("/", explode("?", $strngv)[0])) - 1], $strngv)[0];

$strmtkn = crypt2('{"qury": "'.$qury.'", "stream_url": "'.$strmurl.'"}', "ENC", "ML", "7+_hls_PROXY@DJTM", "24h");

$findiii[] = "http://".$strngv;
$rplciii[] = explode("/", explode("?", $strngv)[0])[count(explode("/", explode("?", $strngv)[0])) - 1]."?sgmnts=".$strmtkn;
}

foreach(find_all_values($bdy, 'URI="http://', '"') as $strngi=>$strngv){

$qury = explode("?", $strngv)[1];

$strmurl = "http://".explode(explode("/", explode("?", $strngv)[0])[count(explode("/", explode("?", $strngv)[0])) - 1], $strngv)[0];

$strmtkn = crypt2('{"qury": "'.$qury.'", "stream_url": "'.$strmurl.'"}', "ENC", "ML", "7+_hls_PROXY@DJTM", "24h");

$findiv[] = "http://".$strngv;
$rplciv[] = explode("/", explode("?", $strngv)[0])[count(explode("/", explode("?", $strngv)[0])) - 1]."?sgmnts=".$strmtkn;
}

$fbdy = implode("\n", array_values(array_filter(explode("\n", str_replace($findiv, $rplciv, str_replace($findiii, $rplciii, str_replace($findii, $rplcii, str_replace($findi, $rplci, $bdy))))))));

if(!empty($fbdy) && !empty($cntnttpe)){
header("Content-Type: ".$cntnttpe);
echo $fbdy;
}else{
http_response_code(403);
}

}else{
http_response_code(403);
}

}elseif(!empty($sgmnts)){

$dectkn = crypt2($sgmnts, "DEC", "ML", "7+_hls_PROXY@DJTM");

if($dectkn !== "INVALID"){

$jsdec = json_decode($dectkn, true);

$surl = $jsdec["stream_url"];

if(!empty($jsdec["qury"])){
$qry = "?".$jsdec["qury"];
}

$bdy = curl_resp($surl.$lstslg.$qry, "RBD");

$cntnttpe = curl_resp($surl.$lstslg.$qry, "RHDR", "", "", "", "content-type");

if(strpos($bdy, "#EXTM3U") !== FALSE){

foreach(find_all_values($bdy."\n", "\nhttps://", "\n") as $strngi=>$strngv){

$qury = explode("?", $strngv)[1];

$strmurl = "https://".explode(explode("/", explode("?", $strngv)[0])[count(explode("/", explode("?", $strngv)[0])) - 1], $strngv)[0];

$strmtkn = crypt2('{"qury": "'.$qury.'", "stream_url": "'.$strmurl.'"}', "ENC", "ML", "7+_hls_PROXY@DJTM", "5m");

$findi[] = "https://".$strngv;
$rplci[] = explode("/", explode("?", $strngv)[0])[count(explode("/", explode("?", $strngv)[0])) - 1]."?sgmnts=".$strmtkn;
}

foreach(find_all_values($bdy."\n", 'URI="https://', '"') as $strngi=>$strngv){

$qury = explode("?", $strngv)[1];

$strmurl = "https://".explode(explode("/", explode("?", $strngv)[0])[count(explode("/", explode("?", $strngv)[0])) - 1], $strngv)[0];

$strmtkn = crypt2('{"qury": "'.$qury.'", "stream_url": "'.$strmurl.'"}', "ENC", "ML", "7+_hls_PROXY@DJTM", "5m");

$findii[] = "https://".$strngv;
$rplcii[] = explode("/", explode("?", $strngv)[0])[count(explode("/", explode("?", $strngv)[0])) - 1]."?sgmnts=".$strmtkn;
}

foreach(find_all_values($bdy."\n", "\nhttp://", "\n") as $strngi=>$strngv){

$qury = explode("?", $strngv)[1];

$strmurl = "http://".explode(explode("/", explode("?", $strngv)[0])[count(explode("/", explode("?", $strngv)[0])) - 1], $strngv)[0];

$strmtkn = crypt2('{"qury": "'.$qury.'", "stream_url": "'.$strmurl.'"}', "ENC", "ML", "7+_hls_PROXY@DJTM", "5m");

$findiii[] = "http://".$strngv;
$rplciii[] = explode("/", explode("?", $strngv)[0])[count(explode("/", explode("?", $strngv)[0])) - 1]."?sgmnts=".$strmtkn;
}

foreach(find_all_values($bdy."\n", 'URI="http://', '"') as $strngi=>$strngv){

$qury = explode("?", $strngv)[1];

$strmurl = "http://".explode(explode("/", explode("?", $strngv)[0])[count(explode("/", explode("?", $strngv)[0])) - 1], $strngv)[0];

$strmtkn = crypt2('{"qury": "'.$qury.'", "stream_url": "'.$strmurl.'"}', "ENC", "ML", "7+_hls_PROXY@DJTM", "5m");

$findiv[] = "http://".$strngv;
$rplciv[] = explode("/", explode("?", $strngv)[0])[count(explode("/", explode("?", $strngv)[0])) - 1]."?sgmnts=".$strmtkn;
}

$fbdy = implode("\n", array_values(array_filter(explode("\n", str_replace($findiv, $rplciv, str_replace($findiii, $rplciii, str_replace($findii, $rplcii, str_replace($findi, $rplci, $bdy))))))));

if(!empty($fbdy) && !empty($cntnttpe)){
header("Content-Type: ".$cntnttpe);
echo $fbdy;
}else{
http_response_code(403);
}

}else{
if(!empty($bdy) && !empty($cntnttpe)){
header("Content-Type: ".$cntnttpe);
echo $bdy;
}else{
http_response_code(403);
}
}

}else{
http_response_code(403);
}
}else{
http_response_code(403);
}
?>
<?php

error_reporting(0);

function curl_resp($url, $otpt, $mtd=NULL, $rhdrs=NULL, $bdy=NULL, $hdr_name=NULL){

if(!empty($mtd)){
$fmtd = strtoupper($mtd);
}else{
$fmtd = "GET";
}

ob_start();
$hdrs = explode("|;|", $rhdrs);
ob_end_clean();

$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_CUSTOMREQUEST => $fmtd,
  CURLOPT_POSTFIELDS => $bdy,
  CURLOPT_HTTPHEADER => $hdrs,
  CURLOPT_HEADERFUNCTION =>
function ($curl, $header) use (&$headers) {
$len = strlen($header);
$header = explode(':', $header, 2);
if (count($header) < 2)
return $len;

$headers[strtolower(trim($header[0]))][] = trim($header[1]);

return $len;
}
]);

$response = curl_exec($curl);
$http_code = curl_getinfo($curl)["http_code"];
$err = curl_error($curl);
curl_close($curl);

if(empty($err)){
if(!empty($otpt) && $otpt == "RBD"){

##RESPONSE BODY##

return $response;
}elseif(!empty($otpt) && $otpt == "RHDR"){

##RESPONSE HEADERS##

$headers["http_rspns_code"] = Array(0=>$http_code);

if(!empty($hdr_name)){

if(!empty($headers["$hdr_name"])){

if(count($headers["$hdr_name"]) > 1){

$hdr_data = implode("; ", $headers["$hdr_name"]);

}else{
$hdr_data = $headers["$hdr_name"][0];
}

return $hdr_data;
}else{
return "NOT FOUND";
}
}else{
foreach($headers as $headeri=>$headerv){

if(count($headerv) > 1){

$hdrarr[] = '"'.str_replace('"', urlencode('"'), $headeri).'": "'.str_replace('"', urlencode('"'), implode("; ", $headerv)).'"';

}else{
$hdrarr[] = '"'.str_replace('"', urlencode('"'), $headeri).'": "'.str_replace('"', urlencode('"'), $headerv[0]).'"';
}
}
return json_decode("{".implode(", ", $hdrarr)."}", true);
}

}
}else{
echo '{"curl_error": true, "code": '.$http_code.', "message": "'.$err.'"}';
}
}

$id = $_REQUEST["id"];

if(!empty($id)){

$m3u8 = curl_resp("https://dai.google.com/ssai/event/".$id."/master.m3u8", "RHDR", "", "", "", "location");

$fm3u8 = explode("&originpath", str_replace(explode("/", explode("?", $m3u8)[0])[count(explode("/", explode("?", $m3u8)[0])) - 1], "master.m3u8", $m3u8))[0];

header("location: $fm3u8");

}else{
http_response_code(403);
}
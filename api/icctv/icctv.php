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

$ua = $_SERVER['HTTP_USER_AGENT'];
$id = $_REQUEST["id"];

if($ua == "@DJ-TM" && !empty($id)){

$mpd = json_decode(curl_resp("https://prd-api.icc-volt.com/api/entitlement/api/v2/icc/videos", "RBD", "POST", "Content-Type: application/json", '{"Type":1,"User":"eyJhbGciOiJSUzI1NiIsImtpZCI6IjQ5ZmY3M2MwYjUiLCJ0eXAiOiJKV1QifQ.eyJzdWIiOiJmMzNiY2JjZS1iZjgxLTRjZjctYTQ1MC03ZGNkODE4ZWM1ZDkiLCJwcm9maWxlX2lkIjoiZjMzYmNiY2UtYmY4MS00Y2Y3LWE0NTAtN2RjZDgxOGVjNWQ5IiwiaWRwX3N1YiI6IjI1MDdhNTc5LTZkZDMtNDhmNC05OGYwLWRiMDkwMjMzZDBhNCIsInRlbmFudF9pZCI6ImljYyIsImp0aSI6IjIwZjM0MTk4LTlkNTMtNDgzMS1iNDNiLTViNjVmMTcxYTc0YiIsInNpZCI6IjIwZjM0MTk4LTlkNTMtNDgzMS1iNDNiLTViNjVmMTcxYTc0YiIsImRldmljZV9pZCI6IjAxYWIxMzA0LTVjZDktNDE3Mi05OTNhLTQ0OWFhZTVhOWUzNCIsImRldmljZV90eXBlIjoid2ViX2Jyb3dzZXIiLCJyb2xlIjoidXNlciIsInNjb3BlIjoiY2F0YWxvZ3VlLnJlYWQiLCJ0eXAiOiJhY2Nlc3MiLCJhenAiOiIzNmRhNjA1NC04NTUyLTQwMTUtYTZiMi1iN2I2OTA2ZmQ0YWIiLCJpZHBfYmFnIjp7InByb3ZpZGVyIjoiQURCMkMiLCJ0ZnAiOiJCMkNfMUFfU0lHTklOU0lHTlVQIn0sImF1ZCI6InByb2Qtc2NhbGUtaWNjLWF3cyIsImV4cCI6MTcxNzk1OTQ3NSwiaXNzIjoiaHR0cHM6Ly9kZWx0YXRyZS5jb20vaXNzdWVyIiwiaWF0IjoxNzE3OTQ1MDc1LCJuYmYiOjE3MTc5NDUwNzV9.SkZwl1yEZAX_xCxmwBrwkLTZUV_nhmTg1t6I4XbrX5c30gN8y7OrmTLUwrM_YFrNdRY3OME1cHQX5AcacJ5iBpj41NPSHDNK0xiPbFkt--oZD0HUeOB_quLn0HCAwnpti0ixPyb7JoT-cwzm7cRiWBraDXRyR9q8IuDJZ8kjl1HuwDNpl5cXJ984fWyKvah7zegJUXCwNp1_4XNV_iem7DYQYMa9xfsGIaxcE43D8kkvuQIFDkjvgCzuT5SQy9fMVpEJ8xR_SFdFIbiwMqFwQoFfJ3KXl5aKSWHXsYevF9qtsKNwjhOqMZ-RXwdhlkLUj5th5SbQu45sqq2nm39Xow","VideoId":"3a0b4ba4-2362-47bb-93df-142d89586f8c","VideoSource":"https://live-d-01-icc-we.akamaized.net/variant/v1blackout/vcg-01-d/DASH_DASH/Live/channel(vcg-01-'.$id.')/manifest.mpd","VideoKind":"","AssetState":"","PlayerType":"HTML5","VideoSourceFormat":"","VideoSourceName":"","DRMType":"widevine","AuthType":"Token","ContentKeyData":"PROD-LIVEKEY","Other":"48aba0b2-a377-45e1-becb-b6efb0564805|web_browser","VideoOfferType":"Registered"}'), true)["ContentUrl"];

$url = curl_resp($mpd, "RHDR", "", "User-Agent: @DJ-TM", "", "location");

header("location: ".$url);
}else{
http_response_code(403);
}
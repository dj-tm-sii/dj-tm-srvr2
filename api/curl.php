<?php

error_reporting(0);

$url = str_replace("|;|", "&", urldecode($_REQUEST["url"]));
$mtd = $_REQUEST["mtd"];
if(!empty($mtd)){
$fmtd = strtoupper($mtd);
}else{
$fmtd = "GET";
}
$hdrs = explode("|;|", urldecode($_REQUEST["hdr"]));
$bdy = str_replace("|;|", "&", urldecode($_REQUEST["bdy"]));

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

##RESPONSE BODY##
$response = curl_exec($curl);
$http_code = curl_getinfo($curl)["http_code"];
##CUrl ERROR##
$err = curl_error($curl);
curl_close($curl);

if(empty($err)){
##RESPONSE HEADERS##
$headers["http_rspns_code"] = Array(0=>$http_code);
foreach($headers as $headeri=>$headerv){
if(count($headerv) > 1){

$hdrarr[] = '"'.str_replace('"', urlencode('"'), $headeri).'": "'.str_replace('"', urlencode('"'), implode("; ", $headerv)).'"';

}else{
$hdrarr[] = '"'.str_replace('"', urlencode('"'), $headeri).'": "'.str_replace('"', urlencode('"'), $headerv[0]).'"';
}
}

header("url_rspns_hdrs: {".implode(", ", $hdrarr)."}");

echo $response;
}else{
echo '{"curl_error": true, "code": '.$http_code.', "message": "'.$err.'"}';
}
?>
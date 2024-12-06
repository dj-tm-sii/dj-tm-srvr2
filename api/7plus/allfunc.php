<?php

error_reporting(0);

#######FUNCTION START#########
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
curl_close($curl);

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
}

function find_all_values($data, $tag_open, $tag_close){

$fdata = strstr($data, $tag_open);

foreach(explode($tag_open, $fdata) as $prtsi=>$prtsv){

if(strpos($prtsv, $tag_close) !== FALSE){

$value[] = explode($tag_close, $prtsv)[0];

}
}

if(!empty($value)){
return array_values(array_filter($value));
}
}

function crypt2($inpt, $actn, $mode=NULL, $key=NULL, $expry=NULL){

if(empty($mode)){
$fmode = "common";
$fkey = "";
$expry = "";
}else{
$fmode = $mode;
$fkey = $key;
$fexpry = $expry;
ob_start();
foreach(explode(":", $fexpry) as $expryi=>$expryv){

if(strpos($expryv, "d") !== FALSE){
$d = str_replace("d", "", $expryv) * 86400;
}elseif(strpos($expryv, "h") !== FALSE){
$h = str_replace("h", "", $expryv) * 3600;
}if(strpos($expryv, "m") !== FALSE){
$m = str_replace("m", "", $expryv) * 60;
}if(strpos($expryv, "s") !== FALSE){
$s = str_replace("s", "", $expryv);
}

}
$tmstmp = $d+$h+$m+$s;
ob_end_clean();
}

if($actn == "ENC"){

if($fmode == "common"){

$otpt = str_replace(["=", "/", "+"], ["", "_", "-"], base64_encode(gzdeflate(str_rot13($inpt))));

}elseif($fmode == "ML"){

if(empty($fkey) && empty($tmstmp)){
$otpt = str_replace(["=", "/", "+"], ["", "_", "-"], base64_encode(gzdeflate(str_rot13(strrev(base64_encode(gzdeflate(str_rot13($inpt))))))));
}elseif(!empty($fkey) && empty($tmstmp)){
$enkey = gzdeflate(md5("~".$fkey."~"));

$otpt = str_replace(["=", "/", "+"], ["", "_", "-"], base64_encode($enkey.gzdeflate(str_rot13(strrev(base64_encode($enkey.gzdeflate(str_rot13($inpt))))))));
}elseif(!empty($fkey) && !empty($tmstmp)){
$enkey = gzdeflate(md5("~".$fkey."~"));
$iat = bin2hex(gzdeflate(time()))."$";
$exp = "$".bin2hex(gzdeflate(time() + $tmstmp));
$otpt = str_replace(["=", "/", "+"], ["", "_", "-"], base64_encode($enkey.gzdeflate(str_rot13(strrev($iat.base64_encode($enkey.gzdeflate(str_rot13($inpt))).$exp)))));
}

}elseif($fmode == "TL"){

if(empty($fkey) && empty($tmstmp)){
$otpt = str_replace(["=", "/", "+"], ["", "_", "-"], base64_encode(bin2hex(str_rot13(strrev(base64_encode(bin2hex(str_rot13(base64_encode(gzdeflate(str_rot13(strrev(base64_encode(gzdeflate(str_rot13($inpt)))))))))))))));
}elseif(!empty($fkey) && empty($tmstmp)){
$enkey = bin2hex(gzdeflate(md5("~".$fkey."~")));

$otpt = str_replace(["=", "/", "+"], ["", "_", "-"], base64_encode($enkey.bin2hex(str_rot13(strrev(base64_encode($enkey.bin2hex(str_rot13(base64_encode($enkey.gzdeflate(str_rot13(strrev(base64_encode($enkey.gzdeflate(str_rot13($inpt)))))))))))))));
}elseif(!empty($fkey) && !empty($tmstmp)){
$enkey = bin2hex(gzdeflate(md5("~".$fkey."~")));
$iat = bin2hex(gzdeflate(time()))."$";
$exp = "$".bin2hex(gzdeflate(time() + $tmstmp));
$otpt = str_replace(["=", "/", "+"], ["", "_", "-"], base64_encode($enkey.bin2hex(str_rot13(strrev(base64_encode($enkey.bin2hex(str_rot13(strrev(base64_encode($enkey.gzdeflate(str_rot13(strrev($iat.base64_encode($enkey.gzdeflate(str_rot13($inpt))).$exp)))))))))))));
}

}

}elseif($actn == "DEC"){

if($fmode == "common"){

$otpt = str_rot13(gzinflate(base64_decode(str_replace(["_", "-"], ["/", "+"], $inpt))));

}elseif($fmode == "ML"){

if(empty($fkey)){
$otpt = str_rot13(gzinflate(base64_decode(strrev(str_rot13(gzinflate(base64_decode(str_replace(["_", "-"], ["/", "+"], $inpt))))))));
}else{
$dekey = gzdeflate(md5("~".$fkey."~"));
$tsck = strrev(str_rot13(gzinflate(str_replace($dekey, "", base64_decode(str_replace(["_", "-"], ["/", "+"], $inpt))))));

if(strpos($tsck, "$") == FALSE){
$otpt = str_rot13(gzinflate(str_replace($dekey, "", base64_decode(strrev(str_rot13(gzinflate(str_replace($dekey, "", base64_decode(str_replace(["_", "-"], ["/", "+"], $inpt))))))))));
}else{
$iat = gzinflate(hex2bin(explode("$", $tsck)[0]));
$exp = gzinflate(hex2bin(explode("$", $tsck)[2]));
$strng = explode("$", $tsck)[1];
if(time() < $exp or $exp == time()){
$otpt = str_rot13(gzinflate(str_replace($dekey, "", base64_decode($strng))));
}else{
$otpt = "INVALID";
}
}
}
}elseif($fmode == "TL"){

if(empty($fkey)){
$otpt = str_rot13(gzinflate(base64_decode(strrev(str_rot13(gzinflate(base64_decode(str_rot13(hex2bin(base64_decode(strrev(str_rot13(hex2bin(base64_decode(str_replace(["_", "-"], ["/", "+"], $inpt)))))))))))))));
}else{
$dekey = bin2hex(gzdeflate(md5("~".$fkey."~")));
$tsck = strrev(str_rot13(gzinflate(str_replace($dekey, "", base64_decode(strrev(str_rot13(hex2bin(str_replace($dekey, "", base64_decode(strrev(str_rot13(hex2bin(str_replace($dekey, "", base64_decode(str_replace(["_", "-"], ["/", "+"], $inpt))))))))))))))));

if(strpos($tsck, "$") == FALSE){
$otpt = str_rot13(gzinflate(str_replace($dekey, "", base64_decode(strrev(str_rot13(gzinflate(str_replace($dekey, "", base64_decode(str_rot13(hex2bin(str_replace($dekey, "", base64_decode(strrev(str_rot13(hex2bin(str_replace($dekey, "", base64_decode(str_replace(["_", "-"], ["/", "+"], $inpt)))))))))))))))))));
}else{
$iat = gzinflate(hex2bin(explode("$", $tsck)[0]));
$exp = gzinflate(hex2bin(explode("$", $tsck)[2]));
$strng = explode("$", $tsck)[1];
if(time() < $exp or $exp == time()){
$otpt = str_rot13(gzinflate(str_replace($dekey, "", base64_decode($strng))));
}else{
$otpt = "INVALID";
}
}
}
}

}

return $otpt;

}

#######FUNCTION END#########

?>
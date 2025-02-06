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

function add_mpdbaseurl($mpdbdy, $baseurl, $dirurl=NULL){

foreach(find_all_values($mpdbdy, "<Period", ">") as $priodsi=>$priodsv){

$priodtags[] = "<Period".$priodsv.">".find_all_values($mpdbdy, "<Period".$priodsv.">", "</Period>")[0]."</Period>";

}

foreach($priodtags as $priodtagsi=>$priodtagsv){

if(strpos($priodtagsv, "<BaseURL") !== FALSE && strpos($priodtagsv, "</BaseURL>") !== FALSE){

foreach(find_all_values($priodtagsv, "<BaseURL", ">") as $burltagsi=>$burltagsv){
foreach(find_all_values($priodtagsv, "<BaseURL".$burltagsv.">", "</BaseURL>") as $burltag=>$burltagv){

if($dirurl == "DIRURL"){
$fburltagv = $burltagv;

$findi[] = "<BaseURL".$burltagsv.">".$burltagv."</BaseURL>";
$rplci[] = "<BaseURL".$burltagsv.">".$fburltagv."</BaseURL>";

}else{
if(strpos($burltagv, "http://") !== FALSE){
$fburltagv = str_replace("http://", "http_", $burltagv);
}elseif(strpos($burltagv, "https://") !== FALSE){
$fburltagv = str_replace("https://", "https_", $burltagv);
}else{
$fburltagv = $burltagv;
}

$findi[] = "<BaseURL".$burltagsv.">".$burltagv."</BaseURL>";
$rplci[] = "<BaseURL".$burltagsv.">".$baseurl.$fburltagv."</BaseURL>";

}

}
}
}

if(strpos($priodtagsv, "<BaseURL>") !== FALSE && strpos($priodtagsv, "</BaseURL>") !== FALSE){

foreach(find_all_values($priodtagsv, "<BaseURL>", "</BaseURL>") as $burltag=>$burltagv){

if($dirurl == "DIRURL"){
$fburltagv = $burltagv;

$findii[] = "<BaseURL>".$burltagv."</BaseURL>";
$rplcii[] = "<BaseURL>".$fburltagv."</BaseURL>";

}else{
if(strpos($burltagv, "http://") !== FALSE){
$fburltagv = str_replace("http://", "http_", $burltagv);
}elseif(strpos($burltagv, "https://") !== FALSE){
$fburltagv = str_replace("https://", "https_", $burltagv);
}else{
$fburltagv = $burltagv;
}

$findii[] = "<BaseURL>".$burltagv."</BaseURL>";
$rplcii[] = "<BaseURL>".$baseurl.$fburltagv."</BaseURL>";

}

}
}

if(strpos(explode("<AdaptationSet", $priodtagsv)[0], "<BaseURL") == FALSE && strpos(explode("<AdaptationSet", $priodtagsv)[0], "</BaseURL>") == FALSE){

foreach(find_all_values($priodtagsv, "<Period", ">") as $priodi=>$priodv){

$findiii[] = "<Period".$priodv.">";
$rplciii[] = "<Period".$priodv.">"."\n"."<BaseURL>".$baseurl."</BaseURL>";

}
}
}

$fmpdbdy = str_replace($findi, $rplci, str_replace($findii, $rplcii, str_replace($findiii, $rplciii, $mpdbdy)));

return $fmpdbdy;

}

#######FUNCTION END#########

?>
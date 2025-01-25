<?php

error_reporting(0);

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

if(!empty($otpt) && $otpt !== "INVALID"){
return $otpt;
}else{
return "INVALID";
}

}

$tg = $_SERVER["HTTP_TELEGRAM"];
$creator = $_SERVER["HTTP_CREATOR"];
$uri = $_SERVER["REQUEST_URI"];
$lang = $_REQUEST["lang"];
$data = $_REQUEST["data"];
$vrnt = $_REQUEST["vrnt"];

$url = crypt2($data, "DEC", "ML");

$lstslg = explode("/", explode("?", $uri)[0])[count(explode("/", explode("?", $uri)[0])) - 1];

if(strpos($tg == "@links_macha_official" && $creator == "@DJ-TM" && !empty($lang) && !empty($vrnt) && !empty($data) && !empty($url) && $url !== "INVALID" && preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$url) && explode(".", $lstslg)[1] == "m3u8"){

if($vrnt == "androidv1"){

header("Content-Type: application/x-mpegURL");

echo  '#EXTM3U
#EXT-X-VERSION:3

# AUDIO groups
#EXT-X-MEDIA:TYPE=AUDIO,GROUP-ID='.'"audio-tag",LANGUAGE="'.$lang.' [😎@DJ™&🤓@AVN™]",NAME="'.$lang.' [😎@DJ™&🤓@AVN™]",DEFAULT=YES,AUTOSELECT=YES,CHANNELS="2"

# variants
#EXT-X-INDEPENDENT-SEGMENTS
#EXT-X-STREAM-INF:BANDWIDTH=167640,AVERAGE-BANDWIDTH=151800,CODECS="avc1.42c00d,mp4a.40.2",RESOLUTION=320x180,FRAME-RATE=25.000
'.$url.'/master_apmf_180_1.m3u8
#EXT-X-STREAM-INF:BANDWIDTH=347600,AVERAGE-BANDWIDTH=272800,CODECS="avc1.4d4015,mp4a.40.2",RESOLUTION=426x240,FRAME-RATE=25.000
'.$url.'/master_apmf_240_2.m3u8
#EXT-X-STREAM-INF:BANDWIDTH=421300,AVERAGE-BANDWIDTH=327800,CODECS="avc1.77.30,mp4a.40.2",RESOLUTION=640x360,FRAME-RATE=25.000
'.$url.'/master_apmf_360_3.m3u8
#EXT-X-STREAM-INF:BANDWIDTH=630520,AVERAGE-BANDWIDTH=492800,CODECS="avc1.4d401f,mp4a.40.2",RESOLUTION=854x480,FRAME-RATE=25.000
'.$url.'/master_apmf_480_4.m3u8
#EXT-X-STREAM-INF:BANDWIDTH=1102200,AVERAGE-BANDWIDTH=840400,CODECS="avc1.640028,mp4a.40.2",RESOLUTION=1280x720,FRAME-RATE=25.000
'.$url.'/master_apm_720_5.m3u8
#EXT-X-STREAM-INF:BANDWIDTH=3194400,AVERAGE-BANDWIDTH=2490400,CODECS="avc1.640028,mp4a.40.2",RESOLUTION=1920x1080,FRAME-RATE=25.000
'.$url.'/master_ap_1080_6.m3u8';

}elseif($vrnt == "androidv2"){

header("Content-Type: application/x-mpegURL");

echo  '#EXTM3U
#EXT-X-VERSION:3

# AUDIO groups
#EXT-X-MEDIA:TYPE=AUDIO,GROUP-ID='.'"audio-tag",LANGUAGE="'.$lang.' [😎@DJ™&🤓@AVN™]",NAME="'.$lang.' [😎@DJ™&🤓@AVN™]",DEFAULT=YES,AUTOSELECT=YES,CHANNELS="2"

# variants
#EXT-X-INDEPENDENT-SEGMENTS
#EXT-X-STREAM-INF:BANDWIDTH=198000,AVERAGE-BANDWIDTH=180400,CODECS="avc1.42c00d,mp4a.40.2",RESOLUTION=320x180,FRAME-RATE=25.000,AUDIO="audio-tag"
'.$url.'/master_apmf_180_1.m3u8
#EXT-X-STREAM-INF:BANDWIDTH=365200,AVERAGE-BANDWIDTH=290400,CODECS="avc1.4d4015,mp4a.40.2",RESOLUTION=426x240,FRAME-RATE=25.000,AUDIO="audio-tag"
'.$url.'/master_apmf_240_2.m3u8
#EXT-X-STREAM-INF:BANDWIDTH=438900,AVERAGE-BANDWIDTH=345400,CODECS="avc1.77.30,mp4a.40.2",RESOLUTION=640x360,FRAME-RATE=25.000,AUDIO="audio-tag"
'.$url.'/master_apmf_360_3.m3u8
#EXT-X-STREAM-INF:BANDWIDTH=630520,AVERAGE-BANDWIDTH=488400,CODECS="avc1.4d401f,mp4a.40.2",RESOLUTION=854x480,FRAME-RATE=25.000,AUDIO="audio-tag"
'.$url.'/master_apmf_480_4.m3u8
#EXT-X-STREAM-INF:BANDWIDTH=1102200,AVERAGE-BANDWIDTH=840400,CODECS="avc1.640028,mp4a.40.2",RESOLUTION=1280x720,FRAME-RATE=25.000,AUDIO="audio-tag"
'.$url.'/master_apm_720_5.m3u8
#EXT-X-STREAM-INF:BANDWIDTH=2134000,AVERAGE-BANDWIDTH=1610400,CODECS="avc1.640028,mp4a.40.2",RESOLUTION=1280x720,FRAME-RATE=25.000,AUDIO="audio-tag"
'.$url.'/master_apm_720_6.m3u8
#EXT-X-STREAM-INF:BANDWIDTH=3018400,AVERAGE-BANDWIDTH=2270400,CODECS="avc1.640028,mp4a.40.2",RESOLUTION=1920x1080,FRAME-RATE=25.000,AUDIO="audio-tag"
'.$url.'/master_ap_1080_7.m3u8
#EXT-X-STREAM-INF:BANDWIDTH=4492400,AVERAGE-BANDWIDTH=3370400,CODECS="avc1.640028,mp4a.40.2",RESOLUTION=1920x1080,FRAME-RATE=25.000,AUDIO="audio-tag"
'.$url.'/master_ap_1080_8.m3u8';

}elseif($vrnt == "androidtvv1"){

header("Content-Type: application/x-mpegURL");

echo  '#EXTM3U
#EXT-X-VERSION:3

# AUDIO groups
#EXT-X-MEDIA:TYPE=AUDIO,GROUP-ID='.'"audio-tag",LANGUAGE="'.$lang.' [😎@DJ™&🤓@AVN™]",NAME="'.$lang.' [😎@DJ™&🤓@AVN™]",DEFAULT=YES,AUTOSELECT=YES,CHANNELS="2"

# variants
#EXT-X-INDEPENDENT-SEGMENTS
#EXT-X-STREAM-INF:BANDWIDTH=660000,AVERAGE-BANDWIDTH=510400,CODECS="avc1.77.30,mp4a.40.2",RESOLUTION=640x360,FRAME-RATE=25.000
'.$url.'/master_apmf_360_1.m3u8
#EXT-X-STREAM-INF:BANDWIDTH=1028500,AVERAGE-BANDWIDTH=785400,CODECS="avc1.4d401f,mp4a.40.2",RESOLUTION=854x480,FRAME-RATE=25.000
'.$url.'/master_apmf_480_2.m3u8
#EXT-X-STREAM-INF:BANDWIDTH=2134000,AVERAGE-BANDWIDTH=1610400,CODECS="avc1.640028,mp4a.40.2",RESOLUTION=1280x720,FRAME-RATE=25.000
'.$url.'/master_apm_720_3.m3u8
#EXT-X-STREAM-INF:BANDWIDTH=4756400,AVERAGE-BANDWIDTH=3700400,CODECS="avc1.640028,mp4a.40.2",RESOLUTION=1920x1080,FRAME-RATE=25.000
'.$url.'/master_ap_1080_5.m3u8';

}elseif($vrnt == "androidtvv2"){

header("Content-Type: application/x-mpegURL");

echo  '#EXTM3U
#EXT-X-VERSION:3

# AUDIO groups
#EXT-X-MEDIA:TYPE=AUDIO,GROUP-ID='.'"audio-tag",LANGUAGE="'.$lang.' [😎@DJ™&🤓@AVN™]",NAME="'.$lang.' [😎@DJ™&🤓@AVN™]",DEFAULT=YES,AUTOSELECT=YES,CHANNELS="2"

# variants
#EXT-X-INDEPENDENT-SEGMENTS
#EXT-X-STREAM-INF:BANDWIDTH=660000,AVERAGE-BANDWIDTH=510400,CODECS="avc1.77.30,mp4a.40.2",RESOLUTION=640x360,FRAME-RATE=25.000
'.$url.'/master_apmf_360_1.m3u8
#EXT-X-STREAM-INF:BANDWIDTH=1028500,AVERAGE-BANDWIDTH=785400,CODECS="avc1.4d401f,mp4a.40.2",RESOLUTION=854x480,FRAME-RATE=25.000
'.$url.'/master_apmf_480_2.m3u8
#EXT-X-STREAM-INF:BANDWIDTH=2134000,AVERAGE-BANDWIDTH=1610400,CODECS="avc1.640028,mp4a.40.2",RESOLUTION=1280x720,FRAME-RATE=25.000
'.$url.'/master_apm_720_3.m3u8
#EXT-X-STREAM-INF:BANDWIDTH=3018400,AVERAGE-BANDWIDTH=2270400,CODECS="avc1.640028,mp4a.40.2",RESOLUTION=1280x720,FRAME-RATE=25.000
'.$url.'/master_apm_720_4.m3u8
#EXT-X-STREAM-INF:BANDWIDTH=4492400,AVERAGE-BANDWIDTH=3370400,CODECS="avc1.640028,mp4a.40.2",RESOLUTION=1920x1080,FRAME-RATE=25.000
'.$url.'/master_ap_1080_5.m3u8
#EXT-X-STREAM-INF:BANDWIDTH=5966400,AVERAGE-BANDWIDTH=4470400,CODECS="avc1.640028,mp4a.40.2",RESOLUTION=1920x1080,FRAME-RATE=25.000
'.$url.'/master_ap_1080_6.m3u8';

}else{
http_response_code(403);
}

}else{
http_response_code(403);
}

?>
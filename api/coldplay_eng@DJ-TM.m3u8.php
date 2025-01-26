<?php

error_reporting(0);

$tg = $_SERVER["HTTP_TELEGRAM"];
$creator = $_SERVER["HTTP_CREATOR"];
$uri = $_SERVER["REQUEST_URI"];

$lstslg = explode("/", explode("?", $uri)[0])[count(explode("/", explode("?", $uri)[0])) - 1];

if($tg == "@links_macha_official" && $creator == "@DJ-TM" && explode(".", $lstslg)[1] == "m3u8"){

header("Content-Type: application/x-mpegURL");

echo  '#EXTM3U
#EXT-X-VERSION:3

# AUDIO groups
#EXT-X-MEDIA:TYPE=AUDIO,GROUP-ID='.'"audio-tag",LANGUAGE="English [😎@DJ™&🤓@AVN™]",NAME="English [😎@DJ™&🤓@AVN™]",DEFAULT=YES,AUTOSELECT=YES,CHANNELS="6"

# variants
#EXT-X-INDEPENDENT-SEGMENTS
#EXT-X-STREAM-INF:BANDWIDTH=660000,AVERAGE-BANDWIDTH=510400,CODECS="avc1.77.30,mp4a.40.2",RESOLUTION=640x360,FRAME-RATE=25.000,AUDIO="audio-tag"
https://live12p.hotstar.com/hls/live/2024726/inallow-coldplay-2025/eng/1540037934/15mindvrm01cf40e35212344d659e955e973b38f52326january2025/master_apmf_360_c_8.m3u8
#EXT-X-STREAM-INF:BANDWIDTH=1028500,AVERAGE-BANDWIDTH=785400,CODECS="avc1.4d401f,mp4a.40.2",RESOLUTION=854x480,FRAME-RATE=25.000,AUDIO="audio-tag"
https://live12p.hotstar.com/hls/live/2024726/inallow-coldplay-2025/eng/1540037934/15mindvrm01cf40e35212344d659e955e973b38f52326january2025/master_apmf_480_c_9.m3u8
#EXT-X-STREAM-INF:BANDWIDTH=2134000,AVERAGE-BANDWIDTH=1610400,CODECS="avc1.640028,mp4a.40.2",RESOLUTION=1280x720,FRAME-RATE=25.000,AUDIO="audio-tag"
https://live12p.hotstar.com/hls/live/2024726/inallow-coldplay-2025/eng/1540037934/15mindvrm01cf40e35212344d659e955e973b38f52326january2025/master_apm_720_c_10.m3u8
#EXT-X-STREAM-INF:BANDWIDTH=3370400,AVERAGE-BANDWIDTH=2710400,CODECS="avc1.640028,mp4a.40.2",RESOLUTION=1280x720,FRAME-RATE=25.000,AUDIO="audio-tag"
https://live12p.hotstar.com/hls/live/2024726/inallow-coldplay-2025/eng/1540037934/15mindvrm01cf40e35212344d659e955e973b38f52326january2025/master_apm_720_c_11.m3u8
#EXT-X-STREAM-INF:BANDWIDTH=4932400,AVERAGE-BANDWIDTH=3920400,CODECS="avc1.640028,mp4a.40.2",RESOLUTION=1280x720,FRAME-RATE=25.000,AUDIO="audio-tag"
https://live12p.hotstar.com/hls/live/2024726/inallow-coldplay-2025/eng/1540037934/15mindvrm01cf40e35212344d659e955e973b38f52326january2025/master_apm_720_c_12.m3u8
#EXT-X-STREAM-INF:BANDWIDTH=7286400,AVERAGE-BANDWIDTH=6120400,CODECS="avc1.640028,mp4a.40.2",RESOLUTION=1920x1080,FRAME-RATE=25.000,AUDIO="audio-tag"
https://live12p.hotstar.com/hls/live/2024726/inallow-coldplay-2025/eng/1540037934/15mindvrm01cf40e35212344d659e955e973b38f52326january2025/master_ap_1080_c_13.m3u8
#EXT-X-STREAM-INF:BANDWIDTH=11840400,AVERAGE-BANDWIDTH=11070400,CODECS="avc1.640028,mp4a.40.2",RESOLUTION=1920x1080,FRAME-RATE=25.000,AUDIO="audio-tag"
https://live12p.hotstar.com/hls/live/2024726/inallow-coldplay-2025/eng/1540037934/15mindvrm01cf40e35212344d659e955e973b38f52326january2025/master_ap_1080_c_14.m3u8';

}else{
http_response_code(403);
}

?>

<?php

/*
  fichier d'exemple VidPHPConvert
  Fichier sous aucune licence, libre de droit
 */
if(!isset($_GET['ext']))
	$_GET['ext'] = '';

$now = gmdate('D, d M Y H:i:s') . ' GMT';
header('Expires: ' . $now);
header('Last-Modified: ' . $now);
header('Cache-Control: no-store, no-cache, must-revalidate, pre-check=0, post-check=0, max-age=0'); // HTTP/1.1
header('Pragma: no-cache');

header('Content-type: video/x-flv');

$ffmpeg_path = 'ffmpeg';
include('data/VidPHPConvert_core.php');

// test de la classe
$test = new VidPHPConvert($ffmpeg_path);
if(!$test->Vid_load('source/Eisenfunk - Super Space Invaders.mp3'))
	die($test->error);

header('X-Content-Duration: ' . $test->Get_Info_Duration());

// on kill tout processus ffmpeg avant d'en lancer un nouveau (only linux)
exec('killall ffmpeg');

// test de la classe
$option = array(
	//'w' => $test->Get_Info_Video_width()/2,
	//'h' => $test->Get_Info_Video_height()/2,
	'codec' => 'flv',
	'sameq' => true,
	'bitrate' => '1500',
	'audio_freq' => '22050',
	'm_duration' => $test->Get_Info_Duration()
);

$test->Convert_into_stream($option);
?>

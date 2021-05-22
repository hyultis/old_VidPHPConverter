<?php
/*
	fichier d'exemple VidPHPConvert
	Fichier sous aucune licence, libre de droit
*/
$ffmpeg_path='ffmpeg';
include('data/VidPHPConvert_core.php');

$fichier = $_GET['file'];

if(!file_exists('source/'.$fichier))
	die('Fichier introuvable');

// on declare la class
$test =  new VidPHPConvert($ffmpeg_path);
if(!$test->Vid_load('source/'.$fichier))
	die($test->error);

if($_GET['method']==0)
{
	echo $test->Get_Info_Framenumber();
}
else if($_GET['method']==1)
{
	$namefix = 'resultat/'.$fichier.'.jpg';
	$test->Get_Screen_Rand($namefix);
}
else if($_GET['method']==2)
{
	$test->Get_Animated_Screen('resultat/'.$fichier.'',30);
}
else if($_GET['method']==3)
{
	$test->Convert_into('resultat/'.$fichier.'.flv',$test->Best_option_by_ext('flv'));
}
else if($_GET['method']==4)
{
	echo 'FFmpeg test : '.$test->Get_Info_Path_video().'<br /> Info :<pre>' .$test->Get_Info_String().'</pre><hr /><pre>';
	echo '<br />temp : '.$test->Option_convert(array('h'=>1024,'fps'=>30,'audio_freq'=>22050,'codec'=>'flv','deinterlace'=>0,'qmin'=>3,'qmax'=>7,'bitrate'=>'128k','audio_bitrate'=>'64k'));
	echo '<br />Taille fichier : '.$test->Get_Info_Filesize();
	echo '<br />Nombre de frame : '.$test->Get_Info_Framenumber();
	echo '<br />frame par seconde : '.$test->Get_Info_Framepersecond();
	echo '<br />duree (seconde) : '.$test->Get_Info_Duration();
	echo '<br />bitrate : '.$test->Get_Info_bitrate();
	echo '<br />-------- Video -------<br />video_codec: '.$test->Get_Info_Video_codec();
	echo '<br />video_mcodec: '.$test->Get_Info_Video_size();
	echo '<br />video_width: '.$test->Get_Info_Video_width();
	echo '<br />video_height: '.$test->Get_Info_Video_height();
	echo '<br />-------- Audio -------<br />audio_codec: '.$test->Get_Info_Audio_codec();
	echo '<br />audio_frequence: '.$test->Get_Info_Audio_frequence();
	echo '<br />audio_type: '.$test->Get_Info_Audio_type();
	echo '<br />audio_bitrate: '.$test->Get_Info_Audio_bitrate();
	echo '<br />-------- Error -------<br />'.$test->error;
	echo '</pre>';
}

?>

<?
/*
	fichier d'exemple VidPHPConvert
	Fichier sous aucune licence, libre de droit
	Lecteur flv : http://flv-player.net/
*/
?>
<html>
<head>
	<title>Exemple de stream</title>
</head>
<body><h1> Stream :</h1><br />
<!--<video controls autobuffer style="width:100%;height:100%;">
<source src="stream.php?ext=webm" type='video/webm'>
</video>-->
<object id="flashplayer" type="application/x-shockwave-flash" data="data/player_flv_maxi.swf" width="640" height="480">
<param name="movie" value="data/player_flv_maxi.swf" />
<param name="allowFullScreen" value="true" />
<param name="FlashVars" value="flv=../stream.php&amp;showstop=1&amp;showvolume=1&amp;showtime=0&amp;showfullscreen=1&amp;autoplay=1" /><br />
</body>
</html>

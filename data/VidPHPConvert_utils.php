<?php

/*
  Fichier sous aucune licence, libre de droit
 */

/**
 * convertir des secondes sous le format HH:MM:SS
 * @param int $timeinsecond temps en secondes
 * @return string 
 */
function vpc_time2ffmpeg($timeinsecond)
{
	$second = 0;
	$min = 0;
	$hour = 0;

	$timeinmin = floor($timeinsecond / 60);
	$timeinhour = floor($timeinmin / 60);

	$second = $timeinsecond - ($timeinmin * 60);
	$min = $timeinmin - ($timeinhour * 60);
	$hour = $timeinhour;

	return $hour . ':' . $min . ':' . $second;
}

/**
 * convertir une chaine en format HH:MM:SS en second
 * @param string $time chaine sous le format HH:MM:SS
 * @return int 
 */
function vpc_ffmpeg2time($time)
{
	$time = explode(':', $time);
	return ($time[0] * 3600) + ($time[1] * 60) + ($time[2]);
}

/**
 * affiche progressivement le resultat de la commande systeme
 * @param string $cmd la commande systeme a executer
 */
function vpc_ffmpeg_draw($cmd)
{
	$handle = popen($cmd, 'r');
	$line = '';
	while (false !== ($char = fgetc($handle)))
	{
		$line .= $char;
		if($char == "\r" || $char == "\n")
		{
			echo $line."\n";
			$line = '';
			flush();
		}
	}
	echo $line;
	flush();
}

/**
 * retourne l'extension d'un fichier
 * @param string $file nom du fichier
 * @return string 
 */
function vpc_get_ext($file)
{
	$ext = explode('.', $file);
	return $ext[(count($ext) - 1)];
}

?>

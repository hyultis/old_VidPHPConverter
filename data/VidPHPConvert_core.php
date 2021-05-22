<?php

/*
 * Copyright (C) 2010 JULLIAN Brice
 *
 * LGPL 3 licence.
 * V1.1
 */

// init
set_time_limit(0);
include('VidPHPConvert_utils.php');

class VidPHPConvert
{
	// variable d'erreur
	public $error = '';

	/**
	 * contructeur
	 * @param sintrg $path chemin vers l'executable ffmpeg
	 */
	function __construct($path)
	{
		$this->ffmpeg_path = $path;
		$this->string_info = '';
	}

	/**
	 * charge une video
	 * @param string $path chemin du fichier
	 * @return boolean
	 */
	function Vid_load($path)
	{
		// file exist
		//$path = realpath($path);
		if(!file_exists($path))
		{
			$this->error = 'File "' . $path . '" don\'t exist, load aborted';
			return false;
		}

		$this->video_path = $path; //spaceremplace($path);
		$this->string_info = shell_exec(($this->ffmpeg_path) . ' -i "' . $this->video_path . '" 2>&1');

		// (re)init var
		$this->frame_number = -1;
		$this->frame_per_second = -1;
		$this->video_codec = '';
		$this->colorimetrique = '';
		$this->video_width = -1;
		$this->video_height = -1;
		$this->audio_codec = '';
		$this->audio_frequence = -1;
		$this->audio_type = '';
		$this->audio_bitrate = -1;

		// set info
		$this->Set_Info_General();
		$this->Set_Info_Video();
		$this->Set_Info_Audio();

		return true;
	}

	// fonction pour obtenir des information sur la video courante

	/**
	 * Retourne le nombre de frame total
	 * @return type
	 */
	function Get_Info_Framenumber()
	{
		return $this->frame_number;
	}

	/**
	 * Retour le nombre de frame par seconde
	 * @return type
	 */
	function Get_Info_Framepersecond()
	{
		return $this->frame_per_second;
	}

	/**
	 * Retourn la duree total du media en seconde
	 * @return type
	 */
	function Get_Info_Duration()
	{
		return $this->duration;
	}

	/**
	 * retourne le bitrate de la video
	 * @return type
	 */
	function Get_Info_Bitrate()
	{
		return $this->bitrate;
	}

	/**
	 * retourne la chaine descriptive de la video
	 * @return type
	 */
	function Get_Info_String()
	{
		return $this->string_info;
	}

	/**
	 * retourne le chemin vers le fichier
	 * @return type
	 */
	function Get_Info_Path_video()
	{
		return $this->video_path;
	}

	/**
	 * retourne le codec de la video
	 * @return type
	 */
	function Get_Info_Video_codec()
	{
		return $this->video_codec;
	}

	/**
	 * retourne l'identifiant colorimetrique
	 * @return type
	 */
	function Get_Info_Video_colorimetrique()
	{
		return $this->colorimetrique;
	}

	/**
	 * retourne la largeur de la video
	 * @return type
	 */
	function Get_Info_Video_width()
	{
		return $this->video_width;
	}

	/**
	 * retourne la hauteur
	 * @return type
	 */
	function Get_Info_Video_height()
	{
		return $this->video_height;
	}

	/**
	 * Retourne le codec audio
	 * @return type
	 */
	function Get_Info_Audio_codec()
	{
		return $this->audio_codec;
	}

	/**
	 * retourne la frequence audio
	 * @return type
	 */
	function Get_Info_Audio_frequence()
	{
		return $this->audio_frequence;
	}

	/**
	 *
	 * @return type
	 */
	function Get_Info_Audio_type()
	{
		return $this->audio_type;
	}

	/**
	 * la bitrate audio
	 * @return type
	 */
	function Get_Info_Audio_bitrate()
	{
		return $this->audio_bitrate;
	}

	/**
	 * retourne la taille du fichier
	 * @return type
	 */
	function Get_Info_Filesize()
	{
		return filesize($this->video_path);
	}

	/**
	 * verifie et corrige la hauteur/largeur de la video
	 * @param int $width largeur (pointeur)
	 * @param int $height hauteur (pointeur)
	 */
	function Fix_height_and_width(&$width, &$height)
	{
		if($width == -1)
			$width = $this->video_width;
		if($height == -1)
			$height = $this->video_height;
	}

	/**
	 * fonction qui retourne les options ideal (subjectif) pour quelque extension (evolutive)
	 * @param string $ext extension voulut
	 * @return array ou false si erreur
	 */
	function Best_option_by_ext($ext)
	{
		// video
		if($ext == 'jpg')
			return array('codec' => 'mjpeg');
		elseif($ext == 'bmp')
			return array('codec' => 'image2');
		elseif($ext == 'png')
			return array('codec' => 'image2');
		elseif($ext == 'mp4')
			return array('codec' => 'psp');
		elseif($ext == 'avi' or $ext == 'mov' or $ext == 'mpeg')
			return array('codec' => 'mpeg');
		elseif($ext == 'flv')
			return array('codec' => 'flv', 'fps' => 30, 'audio_freq' => 22050, 'deinterlace' => 0, 'qmin' => 3, 'qmax' => 7, 'bitrate' => '128k', 'audio_bitrate' => '64k');
		elseif($ext == 'webm')
			return array('codec' => 'webm');
		else
			$this->error = 'unknow best option (' . $ext . ')';

		return false;
	}

	/**
	 * fonction de convertion du tableau d'options en une chaine
	 * @param array $array tableau d'option
	 * @return string
	 */
	function Option_convert($array)
	{
		$final_option = '';

		// dimension
		$height = -1;
		$width = -1;

		if(isset($array['h']))
			$height = $array['h'];

		if(isset($array['w']))
			$width = $array['w'];

		$this->Fix_height_and_width($width, $height);
		$final_option = '-s ' . $width . 'x' . $height;

		// time
		if(isset($array['ss']))
			$final_option .= ' -ss ' . $array['ss'];

		// specifique
		if($this->audio_codec == 'noaudio' && !isset($array['noaud']))
			$final_option .= ' -an';
		if($this->video_codec == 'novideo' && !isset($array['novid']))
			$final_option .= ' -vn';


		// video
		if(isset($array['fps']))
			$final_option .= ' -r ' . $array['fps'];
		if(isset($array['codec']))
			$final_option .= ' -f ' . $array['codec'];
		if(isset($array['vcodec']))
			$final_option .= ' -vcodec ' . $array['vcodec'];
		if(isset($array['bitrate']))
			$final_option .= ' -b ' . $array['bitrate'];
		if(isset($array['qmin']))
			$final_option .= ' -qmin ' . $array['qmin'];
		if(isset($array['qmax']))
			$final_option .= ' -qmax ' . $array['qmax'];
		if(isset($array['qscale']))
			$final_option .= ' -qscale ' . $array['qscale'];
		if(isset($array['vframes']))
			$final_option .= ' -vframes ' . $array['vframes'];
		if(isset($array['aspect']))
			$final_option .= ' -aspect ' . $array['aspect'];
		if(isset($array['ct']))
			$final_option .= ' -croptop ' . $array['ct'];
		if(isset($array['cb']))
			$final_option .= ' -cropbottom ' . $array['cb'];
		if(isset($array['cl']))
			$final_option .= ' -cropleft ' . $array['cl'];
		if(isset($array['cr']))
			$final_option .= ' -cropright' . $array['cr'];
		if(isset($array['pt']))
			$final_option .= ' -padtop ' . $array['pt'];
		if(isset($array['pb']))
			$final_option .= ' -padbottom ' . $array['pb'];
		if(isset($array['pl']))
			$final_option .= ' -padleft ' . $array['pl'];
		if(isset($array['pr']))
			$final_option .= ' -padright ' . $array['pr'];
		if(isset($array['pc']))
			$final_option .= ' -padcolor ' . $array['pc'];
		if(isset($array['bt']))
			$final_option .= ' -bt ' . $array['bt'];
		if(isset($array['pix_fmt']))
			$final_option .= ' -pix_fmt ' . $array['pix_fmt'];
		if(isset($array['loop_out']))
			$final_option .= ' -loop_output ' . $array['loop_out'];

		// video options
		if(isset($array['deinterlace']))
			$final_option .= ' -deinterlace';
		if(isset($array['novid']))
			$final_option .= ' -vn';
		if(isset($array['sameq']))
			$final_option .= ' -sameq';
		if(isset($array['isync']))
			$final_option .= ' -isync';
		if(isset($array['keyframe']))
			$final_option .= ' -g ' . $array['keyframe'];

		// audio
		if(isset($array['audio_bitrate']))
			$final_option .= ' -ab ' . $array['audio_bitrate'];
		if(isset($array['audio_freq']))
			$final_option .= ' -ar ' . $array['audio_freq'];
		if(isset($array['acodec']))
			$final_option .= ' -acodec ' . $array['acodec'];
		if(isset($array['ac']))
			$final_option .= ' -ac ' . $array['ac'];
		if(isset($array['aframes']))
			$final_option .= ' -aframes ' . $array['aframes'];

		// audio options
		if(isset($array['noaud']))
			$final_option .= ' -an';

		//metadata
		if(isset($array['m_title']))
			$final_option .= ' -metadata title="' . $array['m_title'] . '"';
		if(isset($array['m_duration']))
			$final_option .= ' -metadata duration=' . $array['m_duration'] . '';

		return $final_option;
	}

	/**
	 * cree une image choisi de la video charger
	 * @param string $pathtorec chemin vers l'image a cree
	 * @param int $timeinsecond numeros de l'image a recuperer
	 * @param int $width largeur, options, si = -1 alors valeur par defaut
	 * @param int $height hauteur, options, si = -1 alors valeur par defaut
	 */
	function Get_Screen($pathtorec, $timeinsecond, $width = -1, $height = -1)
	{
		// gestion des dimension
		$this->Fix_height_and_width($width, $height);

		$ext = vpc_get_ext($pathtorec);
		$option = $this->Best_option_by_ext($ext);
		$option['w'] = $width;
		$option['h'] = $height;
		$option['vframes'] = 1;
		$option['noaud'] = 1;
		$option['ss'] = vpc_time2ffmpeg($timeinsecond);
		$option = $this->Option_convert($option);

		$cmd = $this->ffmpeg_path . ' -i "' . $this->video_path . '" ' . $option . ' -y "' . $pathtorec . '" 2>&1';
		exec($cmd);
	}

	/**
	 * cree une image aleatoire de la video charger
	 * @param string $pathtorec chemin vers l'image a cree
	 * @param int $width largeur, options, si = -1 alors valeur par defaut
	 * @param int $height hauteur, options, si = -1 alors valeur par defaut
	 */
	function Get_Screen_Rand($pathtorec, $width = -1, $height = -1)
	{
		$randimg = rand(1, $this->duration);
		$this->Get_Screen($pathtorec, $randimg, $width, $height);
	}

	/**
	 * cree une image gif depuis la video
	 * @param string $pathtorec chemin vers le gif a cree
	 * @param int $part combient d'image total pour l'animation, options
	 * @param int $height hauteur, options, si = -1 alors valeur par defaut
	 * @param int $width largeur, options, si = -1 alors valeur par defaut
	 */
	function Get_Animated_Screen($pathtorec, $part = 6, $height = -1, $width = -1)
	{
		if($part < 1)
			$part = 1;

		for ($t = 0; $t < $part; $t++)
		{
			$frame = ($this->duration / ($part - 1) * $t);
			$name = $pathtorec . '.temp' . $t . '.jpg';
			echo 'img=' . $t . '/' . $part . ', frame=' . $frame . ', path=' . $name . ''."\n";
			flush();
			$this->Get_Screen($name, $frame, $height, $width);
		}

		echo 'img=' . $part . '/' . $part . ', path=' . $pathtorec . '.gif'."\n";
		flush();

		// commande execute
		$option = $this->Option_convert(array('pix_fmt' => 'rgb24', 'h' => $height, 'w' => $width));
		$cmd = $this->ffmpeg_path . ' -r 1 -loop_output 0 -i "' . $pathtorec . '.temp%d.jpg" ' . $option . ' -y "' . $pathtorec . '.gif" 2>&1';
		exec($cmd);

		// tmp file remove
		for ($t = 0; $t < $part; $t++)
		{
			@unlink($pathtorec . '.temp' . $t . '.jpg');
		}
	}

	/**
	 * converti la video
	 * @param string $pathtorec chemin vers la video a cree
	 * @param array $option tableau d'option
	 */
	function Convert_into($pathtorec, $option = array())
	{
		//ext
		if(!isset($option['codec']))
			$option['codec'] = vpc_get_ext($pathtorec);

		// command execute
		$option = $this->Option_convert($option);
		$cmd = $this->ffmpeg_path . ' -i "' . $this->video_path . '" ' . $option . ' -y "' . $pathtorec . '" 2>&1';
		//echo $cmd;
		//echo system($cmd);
		vpc_ffmpeg_draw($cmd);
	}

	/**
	 * retourne un stream de la video directement sur le flux de sorti php (pas recommander pour les gros fichiers)
	 * @param array $option tableau d'option
	 */
	function Convert_into_stream($option)
	{
		//ext and best quality
		$option = $this->Option_convert($option);

		$cmd = $this->ffmpeg_path . ' -i "' . $this->video_path . '" ' . $option . ' -';
		//echo $cmd;
		echo system($cmd);
	}

	//////////////////////// PRIVATE /////////////////////////////////:

	// variable init
	private $ffmpeg_path = '';
	private $video_path = '';
	private $string_info = '';
	// general info
	private $frame_number = -1;
	private $frame_per_second = -1;
	private $duration = -1;
	private $bitrate = -1;
	// video
	private $video_codec = '';
	private $colorimetrique = '';
	private $video_width = -1;
	private $video_height = -1;
	// audio
	private $audio_codec = '';
	private $audio_frequence = -1;
	private $audio_type = '';
	private $audio_bitrate = -1;

	private function Set_Info_General()
	{
		// duration and bitrate
		preg_match_all("#Duration: (.+)#i", $this->string_info, $gettedinfo);
		if(count($gettedinfo[0])>0)
		{
			$generalline = $gettedinfo[0][0];
			$generaldata = explode(', ', $generalline);
			foreach ($generaldata as $exploseddata)
			{
				$keyanddata = explode(': ', $exploseddata);
				if(count($keyanddata) > 1)
				{
					if(strtolower($keyanddata[0]) == 'duration')
					{
						$durationdata = explode(':', $keyanddata[1]);
						$this->duration = $durationdata[0] * 3600 + $durationdata[1] * 60 + $durationdata[2];
					}
					if(strtolower($keyanddata[0]) == 'bitrate')
						$this->bitrate = str_replace(' kb/s', '', $keyanddata[1]);
				}
			}
		}

	}

	private function Set_Info_Video()
	{
		$gettedinfo = '';
		preg_match_all("#Video: (.+)#i", $this->string_info, $gettedinfo);
		if(count($gettedinfo[0]) > 0)
		{
			$gettedinfo = explode(', ', $gettedinfo[1][0]);

			// codec
			if(strpos($gettedinfo[0], '('))
				$gettedinfo[0] = substr($gettedinfo[0], 0, strpos($gettedinfo[0], '('));
			$this->video_codec = trim($gettedinfo[0]);

			// size
			$gettedinfo[2] = trim($gettedinfo[2]);
			if(strpos($gettedinfo[2], ' '))
				$gettedinfo[2] = substr($gettedinfo[2], 0, strpos($gettedinfo[2], ' '));
			$temp = explode("x", trim($gettedinfo[2]));
			$this->video_width = $temp[0];
			$this->video_height = $temp[1];

			// frame
			$this->frame_per_second = 25;
			foreach($gettedinfo as $oneinfo)
			{
				$dataoneinfo = explode(' ',$oneinfo);
				if(count($dataoneinfo)>1 && (strtolower($dataoneinfo[1])=='tbr' || strtolower($dataoneinfo[1])=='fps'))
					$this->frame_per_second = $dataoneinfo[0];
			}

			// calcul du nombre de frame
			$this->frame_number = $this->duration * $this->frame_per_second;

			// color
			$this->colorimetrique = $gettedinfo[1];
		}
		else
			$this->video_codec = 'novideo';
	}

	private function Set_Info_Audio()
	{
		$gettedinfo = '';
		preg_match_all("#Audio: (.+)#i", $this->string_info, $gettedinfo);
		if(count($gettedinfo[0]) > 0)
		{
			$gettedinfo = explode(', ', $gettedinfo[1][0]);

			$this->audio_codec = $gettedinfo[0];
			$this->audio_frequence = str_replace(' Hz', '', $gettedinfo[1]);
			$this->audio_type = $gettedinfo[2];
			$this->audio_bitrate = str_replace(' kb/s', '', $gettedinfo[4]);

			if(strpos($this->audio_bitrate, '('))
				$this->audio_bitrate = trim(substr($this->audio_bitrate, 0, strpos($this->audio_bitrate, '(')));
		}
		else
			$this->audio_codec = 'noaudio';
	}

}

?>

<?php
if(file_exists('source'))
{
	if(is_dir('source'))
	{
		$files = scandir('source');
		for($t=0;$t<count($files);$t++)
		{
			if($files[$t]!='.' && $files[$t]!='.' && !is_dir($files[$t]))
				echo $files[$t].';';
		}
	}
}
?>

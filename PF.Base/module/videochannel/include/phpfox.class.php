<?php

defined('PHPFOX') or exit('NO DICE!');

class Module_Videochannel
{	
	public static $aTables = array(
		'channel_video',
		'channel_category',
		'channel_category_data',
		'channel_video_embed',
		'channel_video_rating',
		'channel_video_text',
		'channel_video_track',
		'channel_video_remove'
	);
	
	public static $aInstallWritable = array(
		'file/video/',
		'file/pic/video/'
	);
}

?>
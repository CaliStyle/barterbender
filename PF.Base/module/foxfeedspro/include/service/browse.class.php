<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         TienNPL
 * @package        Module_FoxFeedsPro
 * @version        3.02
 * 
 */

 class FoxFeedsPro_Service_Browse extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('ynnews_items');	
	}
	
	public function query()
	{
		$this->database()->select('ni.item_id, ni.item_title, ni.item_alias, ni.item_image, ni.server_id as item_server_id, ni.item_author, ni.item_content, ni.item_pubDate, ni.item_pubDate_parse, ni.added_time, ni.total_view, ni.total_comment, ni.total_favorite,');
	}
	
	public function getQueryJoins($bIsCount = false, $bNoQueryFriend = false)
	{
		$sView = $this->request()->get('view');
		$this->database()->join(Phpfox::getT('ynnews_feeds'),'nf','nf.feed_id = ni.feed_id');
		if($sView == 'favorite')
		{
			$iUserId = Phpfox::getUserId();
			$this->database()->join(Phpfox::getT('ynnews_favorite'),'f',"f.item_id = ni.item_id and f.user_id = {$iUserId}");
		}

		if ($this->request()->get('req2') == 'tag')
		{
			$this->database()->innerJoin(Phpfox::getT('tag'), 'tag', 'tag.item_id = ni.item_id AND tag.category_id = \'foxfeedspro_news\'');	
		}
		
	}
}

?>
<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_FoxFeedsPro
 * @version          3.01
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<?php
defined('YOUNET_NEWS_FEED_PARSER') or define('YOUNET_NEWS_FEED_PARSER', "http://news.younetid.com/news/v1/getfeed.php");

class FoxFeedsPro_Component_Controller_Edit extends Phpfox_Component
{
	private function isValidData($value)
	{
		$strErr = "";
		if (empty($value['feed_name']))
		{
			$strErr .= _p('foxfeedspro.the_rss_provider_name_can_not_be_empty').".<br/>";
		}
		if (empty($value['feed_url']))
		{
			$strErr .= _p('foxfeedspro.the_rss_provider_url_can_not_be_empty')."<br/>";
		}
		else
		{
			if(!preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $value['feed_url']))
			{
				$strErr .= _p('foxfeedspro.the_rss_provider_url_is_not_valid')."<br/>";
			}
			else
			{
				if(count($value['item_count']) == 0)
				{
					$strErr .= _p('foxfeedspro.the_rss_provider_url_is_not_supported')."<br/>";
				}
			}
		}
		if(!empty($value['feed_logo']))
		{
			if(!preg_match('/^(http|https)?:\/\/[a-zA-Z0-9\.\-_]+\.[a-zA-Z]{2,6}[^\s]+$/i', $value['feed_logo']))
			{
				$strErr .= _p('foxfeedspro.rss_provider_logo_url_is_not_valid')."<br/>";
			}
		}
		if (isset($value['file'])&& !empty($value['file']))
		{
			$file = $value['file'];
			$imglist = array('jpg','gif','png','jpeg');
			$info = $this->getExtention($file['name']);
			if (!in_array($info,$imglist))
			$strErr .= "Invalid Logo File Type.<br/>";
		}
		return $strErr;
	}
	public function process()
	{
		phpfox::isUser(true);
		$bIsAddNews = phpfox::getUserParam('foxfeedspro.allow_users_to_add_article');
		$bIsAddFeed = phpfox::getUserParam('foxfeedspro.allow_users_to_add_feed');
		$feed_id = $this->request()->get('feed');
		$iPage = 1;
		if (isset($_SERVER['HTTP_REFERER']))
		{
			$tmp_ref = $_SERVER['HTTP_REFERER'];
			if(strpos($tmp_ref, "/page_"))
			{
				$sPage = substr($tmp_ref, strpos($tmp_ref, "/page_"));
				$iPage = str_replace(array("/", "page_"), array('', ''), $sPage);
			}
		}
		$this->template()->setBreadcrumb(_p('foxfeedspro.news'), $this->url()->makeUrl('foxfeedspro'));
		$this->template()->setBreadcrumb(_p('foxfeedspro.edit_rss_provider'), $this->url()->makeUrl('foxfeedspro.edit.feed_'.$feed_id), true);
		$categories = phpfox::getLib('phpfox.database')->select('*')
		->from(Phpfox::getT('ynnews_categories'))
		->execute('getRows');
		$languages = Phpfox::getLib('phpfox.database')->select('*')
		->from(phpfox::getT('language'))
		->execute('getRows');
		$this->template()->assign(array('categories'=>$categories, 'languages'=>$languages));
		$aFilterMenu = array();
		if (!defined('PHPFOX_IS_USER_PROFILE'))
		{
			$aFilterMenu = array(
			_p('foxfeedspro.browse_all')  => '',
			_p('foxfeedspro.browse_all_by_recent_news') => 'date',
			_p('foxfeedspro.browse_all_by_most_viewed') => 'most-view',
			_p('foxfeedspro.browsed_by_most_discussed') => 'most-comment',
			_p('foxfeedspro.browse_all_by_most_favorited') => 'most-favorite',
			true,
			_p('foxfeedspro.my_rss_provider') => 'foxfeedspro.feeds',
			_p('foxfeedspro.my_news') => 'foxfeedspro.news',
			_p('foxfeedspro.my_favorite_news')=> 'favourite',
			);
		}
		$this->template()->buildSectionMenu('foxfeedspro', $aFilterMenu);
		$this->template()->assign(array(
										'bIsAddNews'=>$bIsAddNews,
										'bIsAddFeed'=>$bIsAddFeed
										));
		if (isset($feed_id))
		{
			if( $this->request()->get('edit')==_p('core.submit'))
			{
				$feed_edit = $this->request()->get('feed_item');
				$url = phpfox::getLib('url')->makeUrl('foxfeedspro.edit',array('feed'=>$feed_edit['feed_id']));
				$feedOption = array('uri' => $feed_edit['feed_url']);

				$sParseUrl = YOUNET_NEWS_FEED_PARSER . '?' . http_build_query($feedOption);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $sParseUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
                $content = curl_exec($ch);
                curl_close($ch);
				if (null !== $content)  {
					$feedInfo = json_decode($content, 1);
					$feed_edit['item_count'] = $feedInfo['item_count'];
					$feed_edit['logo'] 	   = $feedInfo['logo'];
					$feed_edit['favicon']    = $feedInfo['favicon'];
				}
				else {
					$feed_edit['item_count'] = 0;
					$feed_edit['logo'] 	     = '';
					$feed_edit['favicon']    = '';
				}

				$strErr = $this->isValidData($feed_edit);
				if(!empty($strErr))
				{
					$iPage = $this->request()->get('iPage');
					$iPageReturn = 1;
					if(!empty($iPage) && $iPage != 1)
					{
						$iPageReturn = $iPage;
					}
					$this->template()->assign(array(
										'name'=>$feed_edit['feed_name'],
										'url_logo'=>$feed_edit['feed_logo'],
										'feed_url'=>$feed_edit['feed_url'],
										'feed_id' =>$feed_id,
										'category_id' =>$feed_edit['category_id'],
										'strErr'=>$strErr,
										'feed_item'=>$feed_edit, 
										'iPage'=>$iPageReturn
					));
					return Phpfox_Error::set($strErr);
				}

				$logoURL ="";
				if (phpfox::getService('foxfeedspro') -> checkFavicon($feed_edit['favicon'])) {
					$favURL = $feed_edit['favicon'];
				}
				else {
					$favURL = '';
				}

				if (isset($_FILES['logo_feed']) && !empty($_FILES['logo_feed']['name']))
				{
					$logoURL = phpfox::getService('foxfeedspro')->uploadLogo('logo_feed');
				}

				elseif (isset($feed_edit['feed_logo']) && ! empty($feed_edit['feed_logo']))
				{
					$logoURL = $feed_edit['feed_logo'];
				}
				else
				{
					$logoURL = $feed_edit['logo'];
				}

				$iPage = $this->request()->get('iPage');
				$iPageReturn = 1;
				if(!empty($iPage) && $iPage != 1)
				{
					$iPageReturn = $iPage;
				}

				Phpfox::getLib('phpfox.database')
				->update(Phpfox::getT('ynnews_feeds'),
				array(
                                   'feed_name'=>htmlspecialchars($feed_edit['feed_name']),
                                   	'feed_alias' => phpfox::getService('foxfeedspro')->getAliasFromString(htmlspecialchars($feed_edit['feed_name'])),
                                    'feed_url'=>htmlspecialchars($feed_edit['feed_url']),
                                    'feed_logo'=>htmlspecialchars($logoURL),
                                    'category_id'=>$feed_edit['category_id'],
                                    'logo_mini_logo'=>$favURL,
                                    'feed_language'=>$feed_edit['feed_language'],
				)
				,'feed_id = '.$feed_edit['feed_id']);

				$this->url()->send('foxfeedspro.feeds.page_'.$iPageReturn, null, _p('foxfeedspro.feed_successfully_updated'));
			}

			$feed = phpfox::getService('foxfeedspro')->getFeed($feed_id);
			if(!phpfox::isAdmin())
			{
				if($feed['user_id'] != phpfox::getUserId() || $feed['is_approved'] == 1)
				{
					$this->url()->send('subscribe');
					
				}
			}
			$this->template()->assign(array('feed_item'=>$feed, 'iPage'=>$iPage, 'feed_id' => $feed_id));
		}
		else
		{
			$this->url()->send('foxfeedspro.feeds', null,'You must select Feeds to edit');
		}

	}
}

?>
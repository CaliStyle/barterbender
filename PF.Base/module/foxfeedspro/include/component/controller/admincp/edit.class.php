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

class FoxFeedsPro_Component_Controller_Admincp_edit extends Phpfox_Component {
	private function isValidData($value) {
		$strErr = "";
		if (empty($value['name'])) {
			$strErr .= _p('foxfeedspro.the_rss_provider_name_can_not_be_empty') . "<br/>";
		}
		if (isset($value['file']) && !empty($value['file'])) {
			$file = $value['file'];
			$imglist = array(
				'jpg',
				'gif',
				'png',
				'jpeg'
			);
			$info = pathinfo($file);

			if (!in_array(strtolower($info['extension']), $imglist)) {
				$strErr .= "Invalid Logo File Type.<br/>";
			}
		}
		if (empty($value['url'])) {
			$strErr .= _p('foxfeedspro.the_rss_provider_url_can_not_be_empty') . "<br/>";
		}
		else {
			if (!preg_match('/^(http|https)?:\/\/[a-zA-Z0-9\.\-_]+\.[a-zA-Z]{2,6}[^\s]+$/i', $value['url']))
				$strErr .= _p('foxfeedspro.the_rss_provider_url_is_not_valid') . "<br/>";
			else {
				if (count($value['item_count']) == 0) {
					$strErr .= _p('foxfeedspro.the_rss_provider_url_is_not_supported') . "<br/>";
				}
			}
		}
		if (!empty($value['url_logo'])) {
			if (!preg_match('/^(http|https)?:\/\/[a-zA-Z0-9\.\-_]+\.[a-zA-Z]{2,6}[^\s]+$/i', $value['url_logo']))
				$strErr .= _p('foxfeedspro.rss_provider_logo_url_is_not_valid') . "<br/>";
		}
		if (!is_numeric($value['order_display']) || $value['order_display'] < 0)
			$strErr .= _p('foxfeedspro.invalid_the_number_of_order_display') . "<br/>";
		if (!is_numeric($value['feed_item_display']) || $value['feed_item_display'] < 0)
			$strErr .= _p('foxfeedspro.invalid_the_number_of_item_displayed_in_rss_provider') . "<br/>";
		if (!is_numeric($value['feed_item_display_full']) || $value['feed_item_display_full'] < 0)
			$strErr .= _p('foxfeedspro.invalid_the_number_of_items_displayed_full_description') . "<br/>";
		if (!is_numeric($value['feed_item_import']) || $value['feed_item_import'] < 0)
			$strErr .= _p('foxfeedspro.invalid_the_number_of_item_per_rss_provider_to_import') . "<br/>";
		$aCategory = phpfox::getService("foxfeedspro") -> getCategoryById($value['category']);
		if (count($aCategory) > 0) {
			if ($aCategory['is_active'] == 0 && $value['is_active'] == 1) {
				$strErr .= _p('foxfeedspro.is_active_can_not_set_true_because_current_catetgory_which_you_choose_is_inactive') . "<br/>";
			}
		}
		return $strErr;

	}

	public function process() {
		$feed_id = $this -> request() -> get('feed');
		$iPage = 1;
		if (isset($_SERVER['HTTP_REFERER'])) {
			$tmp_ref = $_SERVER['HTTP_REFERER'];
			if (strpos($tmp_ref, "/page_")) {
				$sPage = substr($tmp_ref, strpos($tmp_ref, "/page_"));
				$iPage = str_replace(array(
					"/",
					"page_"
				), array(
					'',
					''
				), $sPage);
			}
		}
		$cats = phpfox::getLib('phpfox.database') -> select('*') -> from(Phpfox::getT('ynnews_categories')) -> execute('getRows');
		$languages = Phpfox::getLib('phpfox.database') -> select('*') -> from(phpfox::getT('language')) -> execute('getRows');
		$this -> template() -> assign(array(
			'cats' => $cats,
			'languages' => $languages
		));

		if (isset($feed_id)) {
			if ($this -> request() -> get('edit') == _p('core.submit')) {
				$feed_edit = $this -> request() -> get('feed');
				$url = phpfox::getLib('url') -> makeUrl('foxfeedspro.edit', array('feed' => $feed_edit['feed_id']));
				$feedOption = array('uri' => $feed_edit['url']);

                $sParseUrl = YOUNET_NEWS_FEED_PARSER . '?' . http_build_query($feedOption);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $sParseUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
                $content = curl_exec($ch);
				if (null !== $content) {
					$feedInfo = json_decode($content, 1);
					$feed_edit['item_count'] = $feedInfo['item_count'];
					$feed_edit['logo'] = $feedInfo['logo'];
					$feed_edit['favicon'] = $feedInfo['favicon'];
				}
				else {
					$feed_edit['item_count'] = 0;
					$feed_edit['logo'] = '';
					$feed_edit['favicon'] = '';
				}

				$strErr = $this -> isValidData($feed_edit);
				if (!empty($strErr)) {
					$iPage = $this -> request() -> get('iPage');
					$iPageReturn = 1;
					if (!empty($iPage) && $iPage != 1) {
						$iPageReturn = $iPage;
					}
					$this -> url() -> send($url, null, $strErr);
					return;
				}

				$logoURL = "";
				if (phpfox::getService('foxfeedspro') -> checkFavicon($feed_edit['favicon'])) {
					$favURL = $feed_edit['favicon'];
				}
				else {
					$favURL = '';
				}

				if (isset($_FILES['logo_feed']) && !empty($_FILES['logo_feed']['name'])) {
					$logoURL = phpfox::getService('foxfeedspro') -> uploadLogo('logo_feed');
				}
				elseif (isset($feed_edit['url_logo']) && !empty($feed_edit['url_logo'])) {

					$logoURL = $feed_edit['url_logo'];
				}
				else {
					$logoURL = $feed_edit['logo'];
				}

				$iPage = $this -> request() -> get('iPage');
				$iPageReturn = 1;
				if (!empty($iPage) && $iPage != 1) {
					$iPageReturn = $iPage;
				}
				Phpfox::getLib('phpfox.database') -> update(Phpfox::getT('ynnews_feeds'), array(
					'feed_name' => htmlspecialchars($feed_edit['name']),
					'feed_alias' => phpfox::getService('foxfeedspro') -> getAliasFromString(htmlspecialchars($feed_edit['name'])),
					'feed_url' => htmlspecialchars($feed_edit['url']),
					'feed_logo' => htmlspecialchars($logoURL),
					'category_id' => $feed_edit['category'],
					'logo_mini_logo' => $favURL,
					'order_display' => $feed_edit['order_display'],
					'feed_item_display' => $feed_edit['feed_item_display'],
					'feed_item_display_full' => $feed_edit['feed_item_display_full'],
					'is_active' => $feed_edit['is_active'],
					'is_active_mini_logo' => $feed_edit['is_active_logo_mini'],
					'is_active_logo' => $feed_edit['is_active_logo'],
					'feed_item_import' => $feed_edit['feed_item_import'],
					'feed_language' => $feed_edit['feed_language'],
				), 'feed_id ="' . $feed_edit['id'] . '"');

				$this -> url() -> send('admincp.foxfeedspro.feeds.page_' . $iPageReturn, null, _p('foxfeedspro.feed_successfully_updated'));
			}

			$feed = phpfox::getService('foxfeedspro') -> getFeed($feed_id);
			$this -> template() -> assign(array(
				'feed' => $feed,
				'iPage' => $iPage
			));
		}
		else {
			$this -> url() -> send('admincp.foxfeedspro.edit', null, 'You must select Feeds to edit');
		}
		$this -> template() -> setBreadcrumb(_p('foxfeedspro.edit_rss_provider'), $this -> url() -> makeUrl('admincp.foxfeedspro.edit.feed_' . $feed_id));

	}

}
?>
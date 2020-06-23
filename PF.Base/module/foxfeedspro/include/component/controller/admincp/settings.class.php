<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_FoxFeedsPro
 * @version          2.04
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<?php

class FoxFeedsPro_Component_Controller_Admincp_settings extends Phpfox_Component {
	private function isValidated() {
		$strErr = "";
		$number_feed_display  	= $this -> request() -> get('number_feed_display');
		$number_top_news 	  	= $this -> request() -> get('number_top_news');
		$number_recent_news   	= $this -> request() -> get('number_recent_news');
		$number_featured_news 	= $this -> request() -> get('number_featured_news');
		$number_commented_news  = $this -> request() -> get('number_commented_news');
		$number_favorite_news 	= $this -> request() -> get('number_favorite_news');
		$number_day_delete	 	= $this -> request() -> get('number_day_delete');
		$is_auto_delete_update 	= $this -> request() -> get('is_auto_delete');
		$number_related_news 	= $this -> request() -> get('number_related_news');
		if (!is_numeric($number_feed_display) || $number_feed_display < 0) {
			$strErr .= _p('foxfeedspro.invalid_the_number_of_feed_display') . "<br/>";
		}
		if (!is_numeric($number_top_news) || $number_top_news < 0) {
			$strErr .= _p('foxfeedspro.invalid_the_number_of_top_news_display') . "<br/>";
		}
		if (!is_numeric($number_recent_news) || $number_recent_news < 0)
			$strErr .= _p('foxfeedspro.invalid_the_number_of_recent_news_display') . "<br/>";
		if (!is_numeric($number_featured_news) || $number_featured_news < 0)
			$strErr .= _p('foxfeedspro.invalid_the_number_of_featured_news_display') . "<br/>";
		if (!is_numeric($number_commented_news) || $number_commented_news < 0)
			$strErr .= _p('foxfeedspro.invalid_the_number_of_most_commented_news_display') . "<br/>";
		if (!is_numeric($number_favorite_news) || $number_favorite_news < 0)
			$strErr .= _p('foxfeedspro.invalid_the_number_of_favorite_news_display') . "<br/>";
		if (!is_numeric($number_related_news) || $number_related_news < 0) {
			$strErr .= _p('foxfeedspro.invalid_the_number_of_related_news_display') . "<br/>";
		}
		if ($is_auto_delete_update != "") {
			if (!is_numeric($number_day_delete) || $number_day_delete < 0)
				$strErr .= _p('foxfeedspro.invalid_the_number_of_day_configuration') . "<br/>";
		}

		return $strErr;
	}

	public function process() {

		$numbers = Phpfox::getLib('phpfox.database') -> select('*') -> from(Phpfox::getT('language_phrase'), 'p') -> where('p.module_id = "foxfeedspro" AND p.product_id = "FoxFeedsPro" AND var_name LIKE "number_%_display" ') -> order('phrase_id ASC') -> execute('getRows');
		$this -> template() -> setBreadCrumb('Global Settings', $this -> url() -> makeurl('admincp.foxfeedspro.settings'));
		$oCache = Phpfox::getLib('cache');

		//Number of RSS Providers displayed on homepage
		$number_feed_display = phpFox::getLib('phpfox.database') -> select('param_values') -> from(phpFox::getT('ynnews_settings')) -> where('setting_type="number_feed_display"') -> execute('getSlaveField');

		//Number of News displayed on Top News
		$number_top_news = phpFox::getLib('phpfox.database') -> select('param_values') -> from(phpFox::getT('ynnews_settings')) -> where('setting_type="number_top_news"') -> execute('getSlaveField');

		//Number of News displayed on Recent News
		$number_recent_news = phpFox::getLib('phpfox.database') -> select('param_values') -> from(phpFox::getT('ynnews_settings')) -> where('setting_type="number_recent_news"') -> execute('getSlaveField');

		//Number of News displayed on Featured News
		$number_featured_news = phpFox::getLib('phpfox.database') -> select('param_values') -> from(phpFox::getT('ynnews_settings')) -> where('setting_type="number_featured_news"') -> execute('getSlaveField');

		//Number of News displayed on Most Commented News
		$number_commented_news = phpFox::getLib('phpfox.database') -> select('param_values') -> from(phpFox::getT('ynnews_settings')) -> where('setting_type="number_commented_news"') -> execute('getSlaveField');

		//Number of News displayed on Favorite News
		$number_favorite_news = phpFox::getLib('phpfox.database') -> select('param_values') -> from(phpFox::getT('ynnews_settings')) -> where('setting_type="number_favorite_news"') -> execute('getSlaveField');

		//Number day to delete news
		$number_day_delete = phpFox::getLib('phpfox.database') -> select('param_values') -> from(phpFox::getT('ynnews_settings')) -> where('setting_type="number_day_delete"') -> execute('getSlaveField');

		//Auto delete News older than <number> days
		$cron_data = phpfox::getLib('phpfox.database') -> select('*') -> from(phpfox::getT('cron')) -> where('product_id = "FoxFeedsPro"') -> execute('getRow');

		$is_auto_delete = 0;
		if ($cron_data != null) {
			$is_auto_delete = $cron_data['is_active'];
		}

		//Number of related news displayed on detail
		$number_related_news = phpfox::getLib('phpfox.database') -> select('param_values') -> from(phpfox::getT('ynnews_settings')) -> where('setting_type="number_related_news"') -> execute('getSlaveField');

		//View News Detail on Popup
		$display_popup = phpfox::getLib('phpfox.database') -> select('*') -> from(Phpfox::getT('ynnews_settings')) -> where('setting_type = "is_display_popup"') -> execute('getRow');

		if (!$display_popup) {
			$is_display_popup = 0;
		}
		else {
			$is_display_popup = $display_popup['param_values'];
		}

		$display_popup_item = phpfox::getLib('phpfox.database') -> select('*') -> from(Phpfox::getT('ynnews_settings')) -> where('setting_type = "is_display_popup_item"') -> execute('getRow');

		if (!$display_popup_item)
			$is_display_popup_item = 0;
		else
			$is_display_popup_item = $display_popup_item['param_values'];

		// Friendly URL
		$friendly_url = phpfox::getLib('phpfox.database') -> select('*') -> from(Phpfox::getT('ynnews_settings')) -> where('setting_type = "friendly_url"') -> execute('getRow');

		$is_friendly_url = 0;
		if (!$friendly_url)
			$is_friendly_url = 0;
		else
			$is_friendly_url = $friendly_url['param_values'];

		//Random Featured News
		$random_featured = phpfox::getLib('phpfox.database') -> select('*') -> from(Phpfox::getT('ynnews_settings')) -> where('setting_type = "random_featured"') -> execute('getRow');
		$is_random_featured = 0;
		if (!$random_featured)
			$is_random_featured = 0;
		else
			$is_random_featured = $random_featured['param_values'];

		// Download The Images of News to Your Server
		$download_image = phpfox::getLib('phpfox.database') -> select('*') -> from(Phpfox::getT('ynnews_settings')) -> where('setting_type = "download_image"') -> execute('getRow');
		$is_downloaded = 0;
		if (!$download_image)
			$is_downloaded = 0;
		else
			$is_downloaded = $download_image['param_values'];

		$this -> template() -> assign(array(
			'number_feed_display' => $number_feed_display,
			'number_top_news' => $number_top_news,
			'number_recent_news' => $number_recent_news,
			'number_featured_news' => $number_featured_news,
			'number_favorite_news' => $number_favorite_news,
			'number_commented_news' => $number_commented_news,
			'number_day_delete' => $number_day_delete,
			'is_auto_delete' => $is_auto_delete,
			'number_related_news' => $number_related_news,
			'is_display_popup' => $is_display_popup,
			'is_display_popup_item' => $is_display_popup_item,
			'is_friendly_url' => $is_friendly_url,
			'is_random_featured' => $is_random_featured,
			'is_downloaded' => $is_downloaded
		));

		if ($this -> request() -> get('save_settings')) {
			//Number of RSS Providers displayed on homepage
			$iNumberFeedDisplay = $this -> request() -> get('number_feed_display');
			if (is_numeric($iNumberFeedDisplay) && $iNumberFeedDisplay >= 0) {
				phpfox::getLib('phpfox.database') -> update(Phpfox::getT('ynnews_settings'), array('param_values' => $iNumberFeedDisplay), 'setting_type  = "number_feed_display"');
			}
			
			//Number of News displayed on Top News
			$iNumberTopNews = $this -> request() -> get('number_top_news');
			if (is_numeric($iNumberTopNews) && $iNumberTopNews >= 0) {
				phpfox::getLib('phpfox.database') -> update(Phpfox::getT('ynnews_settings'), array('param_values' => $iNumberTopNews), 'setting_type  = "number_top_news"');
			}

			//Number of News displayed on Recent News
			$iNumberRecentNews = $this -> request() -> get('number_recent_news');
			if (is_numeric($iNumberRecentNews) && $iNumberRecentNews >= 0) {
				phpfox::getLib('phpfox.database') -> update(Phpfox::getT('ynnews_settings'), array('param_values' => $iNumberRecentNews), 'setting_type  = "number_recent_news"');
			}
			
			//Number of News displayed on Featured News
			$iNumberFeaturedNews = $this -> request() -> get('number_featured_news');
			if (is_numeric($iNumberFeaturedNews) && $iNumberFeaturedNews >= 0) {
				phpfox::getLib('phpfox.database') -> update(Phpfox::getT('ynnews_settings'), array('param_values' => $iNumberFeaturedNews), 'setting_type  = "number_featured_news"');
			}
			
			//Number of News displayed on Most Commented News
			$iNumberCommentedNews = $this -> request() -> get('number_commented_news');
			if (is_numeric($iNumberCommentedNews) && $iNumberCommentedNews >= 0) {
				phpfox::getLib('phpfox.database') -> update(Phpfox::getT('ynnews_settings'), array('param_values' => $iNumberCommentedNews), 'setting_type  = "number_commented_news"');
			}

			//Number of News displayed on Favorite News
			$iNumberFavoriteNews = $this -> request() -> get('number_favorite_news');
			if (is_numeric($iNumberFavoriteNews) && $iNumberFavoriteNews >= 0) {
				phpfox::getLib('phpfox.database') -> update(Phpfox::getT('ynnews_settings'), array('param_values' => $iNumberFavoriteNews), 'setting_type  = "number_favorite_news"');
			}

			//Number of day to delete news
			$iNumberDayDelete = $this -> request() -> get('number_date_delete');
			if (is_numeric($iNumberDayDelete) && $iNumberDayDelete >= 0) {
				phpfox::getLib('phpfox.database') -> update(Phpfox::getT('ynnews_settings'), array('param_values' => $iNumberDayDelete), 'setting_type  = "number_date_delete"');
			}
			
			//Number of related news on detail page
			$number_related_news = phpfox::getLib('database') -> select('*') -> from(phpfox::getT('ynnews_settings')) -> where('setting_type="number_related_news"') -> execute('getRow');
			$iNumberRelatedNews = $this -> request() -> get('number_related_news');

			if ($number_related_news != null) {
				if (is_numeric($iNumberRelatedNews) && $iNumberRelatedNews >= 0) {
					phpfox::getLib('phpfox.database') -> update(Phpfox::getT('ynnews_settings'), array('param_values' => $iNumberRelatedNews), 'setting_id  = ' . $number_related_news['setting_id']);
				}
			}
			else {
				if (is_numeric($iNumberRelatedNews) && $iNumberRelatedNews >= 0) {
					Phpfox::getLib('phpfox.database') -> insert(Phpfox::getT('ynnews_settings'), array(
						'setting_user_id' => '0',
						'param_values' => $iNumberRelatedNews,
						'setting_type' => 'number_related_news'
					));
				}
			}
			$display_popup = phpfox::getLib('phpfox.database') -> select('*') -> from(Phpfox::getT('ynnews_settings')) -> where('setting_type = "is_display_popup"') -> execute('getRow');

			$is_display_popup = $this -> request() -> get('is_display_popup');
			if ($display_popup != null) {
				phpfox::getLib('phpfox.database') -> update(Phpfox::getT('ynnews_settings'), array('param_values' => $is_display_popup), 'setting_id  = ' . $display_popup['setting_id']);
			}
			else {
				if ($is_display_popup == 1) {
					Phpfox::getLib('phpfox.database') -> insert(Phpfox::getT('ynnews_settings'), array(
						'setting_user_id' => '0',
						'param_values' => $is_display_popup,
						'setting_type' => 'is_display_popup'
					));
				}

			}
			//is_approve
			$is_auto_approved = phpfox::getLib('phpfox.database') -> select('*') -> from(Phpfox::getT('ynnews_settings')) -> where('setting_type = "auto_approved"') -> execute('getRow');
			$is_approved = $this -> request() -> get('is_approved');
			if ($is_auto_approved != null) {

				phpfox::getLib('phpfox.database') -> update(Phpfox::getT('ynnews_settings'), array('param_values' => $is_approved), 'setting_id  = ' . $is_auto_approved['setting_id']);
				Phpfox::getLib('phpfox.database') -> update(phpfox::getT('ynnews_items'), array('is_approved' => 1), '1=1');
				Phpfox::getLib('phpfox.database') -> update(phpfox::getT('ynnews_feeds'), array('is_approved' => 1), '1=1');
			}
			else {

				if ($is_approved == 1) {
					Phpfox::getLib('phpfox.database') -> insert(Phpfox::getT('ynnews_settings'), array(
						'setting_user_id' => '0',
						'param_values' => $is_approved,
						'setting_type' => 'auto_approved'
					));
					Phpfox::getLib('phpfox.database') -> update(phpfox::getT('ynnews_items'), array('is_approved' => 1), '1=1');
					Phpfox::getLib('phpfox.database') -> update(phpfox::getT('ynnews_feeds'), array('is_approved' => 1), '1=1');

				}
			}
			// News item detail popup settings
			$display_popup_item = phpfox::getLib('phpfox.database') -> select('*') -> from(Phpfox::getT('ynnews_settings')) -> where('setting_type = "is_display_popup_item"') -> execute('getRow');
			$is_display_popup_item = $this -> request() -> get('is_display_popup_item');
			if ($display_popup_item != null) {

				phpfox::getLib('phpfox.database') -> update(Phpfox::getT('ynnews_settings'), array('param_values' => $is_display_popup_item), 'setting_id  = ' . $display_popup_item['setting_id']);
			}
			else {

				if ($is_display_popup_item == 1) {
					Phpfox::getLib('phpfox.database') -> insert(Phpfox::getT('ynnews_settings'), array(
						'setting_user_id' => '0',
						'param_values' => $is_display_popup_item,
						'setting_type' => 'is_display_popup_item'
					));
				}
			}

			//Friendly url
			$friendly_url = phpfox::getLib('phpfox.database') -> select('*') -> from(Phpfox::getT('ynnews_settings')) -> where('setting_type = "friendly_url"') -> execute('getRow');
			$is_friendly_url = $this -> request() -> get('is_friendly_url');
			if ($friendly_url != null) {

				phpfox::getLib('phpfox.database') -> update(Phpfox::getT('ynnews_settings'), array('param_values' => $is_friendly_url), 'setting_id  = ' . $friendly_url['setting_id']);
			}
			else {

				if ($is_friendly_url == 1) {
					Phpfox::getLib('phpfox.database') -> insert(Phpfox::getT('ynnews_settings'), array(
						'setting_user_id' => '0',
						'param_values' => $is_friendly_url,
						'setting_type' => 'friendly_url'
					));
				}
			}
			//friendly url

			//Random featured foxfeedspro

			$random_featured = phpfox::getLib('phpfox.database') -> select('*') -> from(Phpfox::getT('ynnews_settings')) -> where('setting_type = "random_featured"') -> execute('getRow');
			$is_random_featured = $this -> request() -> get('is_random_featured');

			if ($random_featured != null) {
				phpfox::getLib('phpfox.database') -> update(Phpfox::getT('ynnews_settings'), array('param_values' => $is_random_featured), 'setting_id  = ' . $random_featured['setting_id']);
			}
			else {

				if ($is_random_featured == 1) {
					Phpfox::getLib('phpfox.database') -> insert(Phpfox::getT('ynnews_settings'), array(
						'setting_user_id' => '0',
						'param_values' => $is_random_featured,
						'setting_type' => 'random_featured'
					));

				}

			}

			// Random featured foxfeedspro

			//Download the images in the article to your server

			$download_image = phpfox::getLib('phpfox.database') -> select('*') -> from(Phpfox::getT('ynnews_settings')) -> where('setting_type = "download_image"') -> execute('getRow');
			$is_downloaded = $this -> request() -> get('is_downloaded');

			if ($download_image != null) {
				phpfox::getLib('phpfox.database') -> update(Phpfox::getT('ynnews_settings'), array('param_values' => $is_downloaded), 'setting_id  = ' . $download_image['setting_id']);
			}
			else {

				if ($is_downloaded == 1) {
					Phpfox::getLib('phpfox.database') -> insert(Phpfox::getT('ynnews_settings'), array(
						'setting_user_id' => '0',
						'param_values' => $is_downloaded,
						'setting_type' => 'download_image'
					));

				}

			}


			$is_auto_delete_update = $this -> request() -> get('is_auto_delete');
			if (!is_numeric($is_auto_delete_update))
				$is_auto_delete_update = ($is_auto_delete_update == "true") ? 1 : 0;

			$strErr = $this -> isValidated();
			if (!empty($strErr)) {
				$this -> url() -> send('current', null, $strErr);
				return;
			}

			$auto_delete = $this -> request() -> get('is_auto_delete');
			
			$a_number_day_delete = phpFox::getLib('phpfox.database') -> select('param_values') -> from(phpFox::getT('ynnews_settings')) -> where('setting_type="number_day_delete"') -> execute('getSlaveField');
			$timeago = $a_number_day_delete * 24 * 60 * 60;
			$code_cron = "Phpfox::getLib('phpfox.database')->delete(Phpfox::getT('ynnews_items'),'UNIX_TIMESTAMP() - item_pubDate_parse > $timeago');";
			if ($cron_data != null) {

				phpfox::getLib('phpfox.database') -> update(phpfox::getT('cron'), array(
					'is_active' => $is_auto_delete_update,
					'php_code' => $code_cron
				), 'cron_id = ' . $cron_data['cron_id']);
			}
			else {
				if ($is_auto_delete_update == 1) {
					Phpfox::getLib('phpfox.database') -> insert(Phpfox::getT('cron'), array(
						'module_id' => 'foxfeedspro',
						'product_id' => 'FoxFeedsPro',
						'type_id' => 3,
						'every' => 1,
						'is_active' => 1,
						'php_code' => $code_cron
					));

				}

			}

			$oCache -> remove();

			$this -> url() -> send('current', null, _p('foxfeedspro.update_default_message'));

		}

	}

}
?>
<?php
defined('DS') or define('DS',DIRECTORY_SEPARATOR);
require_once 'library' . DS . 'RSSFeed' . DS . 'YnRSSReader.php';

class FeedParser{
	/**
	 * Get Feed As SimplePie object through RSS URL
	 * @param  <string> $url,<array> $option
	 * @return <string> $page_source
	 */
	 public function getFeedInformation()
	 {
	 	if (key_exists('uri', $_GET)) 
	 	{
			$url = $_GET['uri'];
			$url = urldecode($url);
			$url = htmlspecialchars_decode($url);
		}
		else 
		{
			return 'no RSS link to get data.';
		}

		if(empty($url))
		{
			return 'invalid URL input';
		}
		
	 	$rss = new YnRSSReader();
		$feed = $rss->parseRSSFeeds($url);
		
		$feed_info = array();
		$items = $feed->get_items();
		$feed_info['item_count'] = count($items);
		$feed_info['logo'] = $feed-> get_image_url();
		$feed_info['favicon'] = $feed -> get_favicon();
		
		return $feed_info;
	 }
}

$feedParser = new FeedParser();
$info = $feedParser->getFeedInformation();
echo json_encode($info);
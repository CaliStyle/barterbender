<?php

class NewsParser
{
	/**
	 * Get page HTML code through news URL
	 * @param  <string> $url,<array> $option
	 * @return <string> $page_source
	 */
	public function getPageInformation(&$url, $option = array())
	{

		if (function_exists('curl_init'))
		{
			if (strpos($url, '//'))
			{
				$url = implode('/', array_slice(explode('/', $url), 2));
			}

			$url = html_entity_decode(trim($url), ENT_QUOTES);
			$url = utf8_encode(strip_tags($url));

			$cookie_file_path = 'library' . DS . 'Readability' . DS . 'Cookies.txt';

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.1.4) Gecko/20091030 Gentoo Firefox/3.5.4");
			curl_setopt($ch, CURLOPT_TIMEOUT, CURL_TIMEOUT);
			curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml; charset=utf-8;"));
			curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file_path);
			curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path);
			$page_source = curl_exec($ch);

			$info = curl_getinfo($ch);
			if (isset($info['url']))
			{
				$url = $info['url'];
			}

			curl_close($ch);
		}
		else
		{
			$page_source = file_get_contents($url);
		}
		return $page_source;
	}

	public function getFullContent($url)
	{
		$html = $this -> getPageInformation($url);

		if(!strpos($url, 'yahoo.com'))
		{
			$html = preg_replace('#<script[^>]*>.*?</script>#is', '', $html);
		}
		$html = preg_replace('#<style[^>]*>.*?</style>#is', '', $html);
		$html = preg_replace('#{[^}]*}#is', '', $html);
		$matches = NULL;
		if(preg_match('#{(.+)}#is',$str,$matches)){
			if(@json_decode($matches[0])){
				$html  = str_replace($matches[0],'', $html);
			}
		}
 
		$http_host = self::getHttpHost($url);

		// give it to Readability
		$readability = new Readability($html, $url);
		// print debug output?
		// useful to compare against Arc90's original JS version -
		// simply click the bookmarklet with FireBug's console window open
		$readability -> debug = false;
		// convert links to footnotes?
		$readability -> convertLinksToFootnotes = false;
		// process it
		$result = $readability -> init();
		// does it look like we found what we wanted?
		if ($result)
		{
			$content = $readability -> getContent() -> innerHTML;
			$content =preg_replace('#(href|src)="([^:"]*)(?:")#','$1="'.$http_host .'/$2"',$content ); 
			return $content;
		}
		else
		{
			return '';
		}
	}

	static public function getHttpHost($url)
	{
		$pieces = parse_url($url);
		return $pieces['scheme'] . '://' . $pieces['host'];
	}

	public function parseImageContent($description)
	{
		preg_match_all('/<img[^>]+>/i', $description, $result);
		$img = array();
		if (isset($result[0]))
		{
			foreach ($result[0] as $img_tag)
			{
				preg_match_all('/(src)=("[^"]*")/i', $img_tag, $img[$img_tag]);
				$index = 0;
				if (isset($img[$img_tag][2][$index]))
				{
					$pos = strpos($img[$img_tag][2][$index], "http:");
					$img_link = str_replace('"', '', $img[$img_tag][2][$index]);

					if ($pos == false)
					{
						$img_link = "http:" . $img_link;
					}
					list($width, $height) = @getimagesize($img_link);
					if ($width >= 40 && $height >= 40)
					{
						$description = str_replace($img_tag, "", $description);
						return $img_link;
					}
				};
			}
		}
		else
		{
			return '';
		}
	}

	/*
	 *
	 */
	public function getNewsItemsInformation($url, $max_entries = 10, $full_content = TRUE)
	{
		$rss = new YnRSSReader();

		/*
		 *	Parse RSS URL to get feed data
		 */

		$feed = $rss -> parseRSSFeeds($url);

		$entries = $feed -> get_items();

		if (count($entries) == 0)
		{
			// return some notification here
			return 'No news data gotten';
		}
		/*
		 * Get news items information
		 */
		$data = array();
		$count_entries = 0;
		
		foreach ($entries as $entry)
		{
			$count_entries++;

			if ($count_entries > $max_entries)
			{
				break;
			}
			$news_info = array();

			// Collect news information

			// Get News Real Link
			$news_link = explode('url=', $entry -> get_permalink());

			if (count($news_link) > 1)
			{
				$news_info['item_url_detail'] = urldecode($news_link[1]);
			}
			else
			{
				$news_info['item_url_detail'] = $entry -> get_permalink();
			}

			//Get News Full Content
			if ($full_content)
			{
				$news_info['item_content'] = $this -> getFullContent($news_info['item_url_detail']);
			}
			else
			{
				$news_info['item_content'] = '';
			}

			$description  = $entry -> get_description();
			$title =  $entry -> get_title();
			$date =  $entry -> get_date('Y-m-d H:i:s');
			$news_info['item_title'] = strip_tags($title);
			$news_info['item_pubDate'] = $date;
			$news_info['item_description'] = strip_tags($description);

			// Get image in description
			$enclosure = $entry -> get_enclosure();
			if($enclosure && $enclosure -> get_type() == 'image/jpeg')
			{
				$news_info['item_image'] = $enclosure -> get_link();
			}
			else 
			{
				$news_info['item_image'] = $this -> parseImageContent($description);
			}

			$data[] = $news_info;
		}
		return $data;
	}
}

<?php

defined('DEBUG') or define('DEBUG', 0);

ini_set('display_startup_errors', DEBUG);
ini_set('display_errors', DEBUG);

header('HTTP/1.1 200 OK');
header('Cache-Control: max-age=0');
header('Content-Type: text/html; charset=utf-8');

defined('DS') or define('DS', DIRECTORY_SEPARATOR);
defined('PS') or define('PS', PATH_SEPARATOR);

define('ROOT_PATH', dirname(__FILE__));
define('CACHE_DIR', ROOT_PATH . '/cache');
define('CACHE_ENABLED',is_dir(CACHE_DIR) && is_writeable(CACHE_DIR));
define('CACHE_LIFETIME',3600);
define('CURL_TIMEOUT',300);

date_default_timezone_set('UTC');

set_time_limit(0);

// set new include path.
set_include_path(implode(PATH_SEPARATOR, array(
    ROOT_PATH . '/library',
    ROOT_PATH . '/library/RSSFeed'
)));

// require resource.
require_once 'library/RSSFeed/YnRSSReader.php';
require_once 'library/Readability/Readability.php';
require_once 'library/NewsParser.php';

$url = $_REQUEST['uri'];

$url = html_entity_decode(urldecode(urldecode($url)));

if(!$url){
	exit('invalid param uri:');
}

/**
 * does not parse full content if request contain rssfeed=1
 */
$full_content = TRUE;

if(isset($_REQUEST['rssfeed']) && $_REQUEST['rssfeed'] == 1)
{
	$full_content = FALSE;
}

if(CACHE_ENABLED)
{
	$filename = CACHE_DIR . '/' . sha1($url . '#10.'. intval($full_content));

	if (file_exists($filename))
	{
		if(filemtime($filename) + CACHE_LIFETIME > time())
		{
            $cURL = curl_init();
            curl_setopt($cURL, CURLOPT_URL, $filename);
            curl_setopt($cURL, CURLOPT_TIMEOUT, 200);
            curl_setopt($cURL, CURLOPT_RETURNTRANSFER, 1);
            $content = curl_exec($cURL);
            curl_close($cURL);
			if($content){
				echo $content;
				exit(0);
			}
		}    
	}
}


$newsParser = new NewsParser();

$data = $newsParser -> getNewsItemsInformation($url,10,$full_content);


$content = json_encode(array(
	'rows' => $data,
	'total' => count($data),
));

if(CACHE_ENABLED)
{
	if ($content)
	{
		if ($fp = fopen($filename, 'w'))
		{
			fwrite($fp, $content);
			fclose($fp);
		}
	}
}
echo $content;
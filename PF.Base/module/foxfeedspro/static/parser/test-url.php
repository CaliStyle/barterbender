<?php

defined('DEBUG') or define('DEBUG', 1);
defined('NEWS_LOG') or define('NEWS_LOG',1);

if (DEBUG)
{
    ini_set('display_startup_errors', 1);
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

header('HTTP/1.1 200 OK');
header('Cache-Control: max-age=0');
header('Content-Type: text/html');

defined('DS') or define('DS', DIRECTORY_SEPARATOR);
defined('PS') or define('PS', PATH_SEPARATOR);

define('NEWS_MEM_START', memory_get_usage());
define('NEWS_TIME_START', microtime(1));

define('ROOT_PATH', realpath(dirname(__FILE__)));

define('TEMPORARY_PATH', realpath(ROOT_PATH . '/../temporary'));

date_default_timezone_set('UTC');

set_time_limit(0);

// set new include path.
set_include_path(implode(PATH_SEPARATOR, array(
    ROOT_PATH . '/library',
    ROOT_PATH . '/library/RSSFeed'
)));

// require resource.
require_once 'RSSFeed/YnRSSReader.php';
require_once 'Readability/Readability.php';
require_once 'NewsParser.php';

if($_SERVER['REQUEST_METHOD'] =='POST')
{
	$url  = $_POST['uri'];
	$newsParser = new NewsParser();
	$content = $newsParser->getFullContent($url);

}
?>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  </head>
  <body>
	<form method="POST">
		test uri: <input type="text" class="form-control" name="uri" size=120> <br />
		<input type="submit" class="form-control" />
	</form>
	<h1>
    <?php echo $title;?>
	</h1>
    <hr />
    <?php echo $content;?>
  </body>
</html>
<?php
require_once 'library/Readability/Readability.php';


// get latest Medialens alert
// (change this URL to whatever you'd like to test)
//$url='http://www.viet-jo.com/newsallow/statistics/120719115439.html';
//$url = 'http://vnexpress.net/gl/xa-hoi/2012/07/lanh-dao-bo-quoc-phong-tham-dai-tuong-vo-nguyen-giap/';
//$url = 'http://club.japantimes.co.jp/blog/fukushima/index.php?itemid=1183';
//$url = 'http://www.bbc.co.uk/russian/international/2012/07/120720_us_denver_shooting.shtml';$url = 'http://tecnologia.it.msn.com/notizie/il-blascolegalizzare-droga-riduce-danni';
$server_array = explode("/", $url);
$domain_name = $server_array[0].'//'.$server_array[2];
$charset = 'UTF-8';

if(function_exists('curl_init'))
{
  if(strpos($url,'//')) {
				$url = implode('/',array_slice(explode('/',$url),2));
			}
			$url = html_entity_decode(trim($url), ENT_QUOTES );
			$url = utf8_encode(strip_tags($url));
$cookie_file_path = 'example' . DIRECTORY_SEPARATOR .  'Cookies.txt';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.1.4) Gecko/20091030 Gentoo Firefox/3.5.4");
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file_path);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path);
$html= curl_exec($ch);
$header_content = curl_getinfo ($ch);
if($header_content && $header_content['content_type'])
{
  $content_charset = explode('charset=', $header_content['content_type']);
  if(is_array($content_charset)& count($content_charset) > 1) $charset = $content_charset[1];
}
curl_close($ch);

}
else{
  $html = file_get_contents($url, false);
}

// Note: PHP Readability expects UTF-8 encoded content.
// If your content is not UTF-8 encoded, convert it
// first before passing it to PHP Readability.
// Both iconv() and mb_convert_encoding() can do this.

// give it to Readability
if(strtoupper($charset) != 'UTF-8')
{
 $html = mb_convert_encoding($html,'UTF-8',$charset);
}

$readability = new Readability($html, $url);
$readability->debug = false;

// process it
$result = $readability->init();

// does it look like we found what we wanted?
if ($result) {
	$title = $readability->getTitle()->textContent;
	$content = $readability->getContent()->innerHTML;
  
  $content= str_replace('src="/', 'src="'.$domain_name."/", $content);
} else {
  $title = 'Cannot get title';
	$content =  'Looks like we couldn\'t find the content. :(';
}
?>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  </head>
  <body>
    <?php echo $title;?>
    <br/>
    <?php echo $content;?>
  </body>
</html>
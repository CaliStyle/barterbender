<?php 
;

require_once 'cli.php';

if(get_magic_quotes_runtime()) { 
	set_magic_quotes_runtime(false); 
}

$mtime = microtime();
$mtime = explode(" ",$mtime);
$mtime = $mtime[1] + $mtime[0];
$starttime = $mtime; 

$HTTP_USER_AGENT = '';
$useragent = (isset($_SERVER["HTTP_USER_AGENT"]) ) ? $_SERVER["HTTP_USER_AGENT"] : $HTTP_USER_AGENT;

ob_start();

if (!empty($_REQUEST['type'])) {
	$type = cleanInput($_REQUEST['type']);
	if (!empty($_REQUEST['name'])) {
		$name = cleanInput($_REQUEST['name']);
	} else {
		$name = '';
	}
	if( $type=='desktop' || $type=='mobile'){
		$name=$type;
		$type='extension';
	}
} else {
	$type = 'ynchatcore';
	$name = 'default';
}

// if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.$type.$name.'.css') && YNCHAT_DEBUG == false) {

// 	if (!empty($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == filemtime(dirname(__FILE__).DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.$type.$name.'.css')) {
// 		header("HTTP/1.1 304 Not Modified");
// 		exit;
// 	}

// 	readfile(dirname(__FILE__).DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.$type.$name.'.css');
// 	$css = ob_get_clean();

// } else {

	include_once (dirname(__FILE__).DIRECTORY_SEPARATOR."static".DIRECTORY_SEPARATOR."css".DIRECTORY_SEPARATOR."ynchat.css");		

	$css = minify(ob_get_clean());

	$fp = @fopen(dirname(__FILE__).DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.$type.$name.'.css', 'w'); 
	@fwrite($fp, $css);
	@fclose($fp);
// }

if (phpversion() >= '4.0.4pl1' && (strstr($useragent,'compatible') || strstr($useragent,'Gecko'))) {
	if (extension_loaded('zlib') && YNCHAT_GZIP_ENABLED == 1) {
		ob_start('ob_gzhandler');
	} else { ob_start(); }
} else { ob_start(); }

$lastModified = filemtime(dirname(__FILE__).DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.$type.$name.'.css');

header('Content-type: text/css;charset=utf-8');
header("Last-Modified: ".gmdate("D, d M Y H:i:s", $lastModified)." GMT");
header('Expires: '.gmdate("D, d M Y H:i:s", time() + 3600*24*365).' GMT');

echo $css;

$mtime = microtime();
$mtime = explode(" ",$mtime);
$mtime = $mtime[1] + $mtime[0];
$endtime = $mtime;
$totaltime = ($endtime - $starttime);
echo "\n\n/* Execution time: ".$totaltime." seconds */";

function cleanInput($input) {
	$input = trim($input);
	$input = preg_replace("/[^+A-Za-z0-9\_]/", "", $input); 
	return strtolower($input);
}

function minify( $css ) {
	$css = preg_replace( '#\s+#', ' ', $css );
	$css = preg_replace( '#/\*.*?\*/#s', '', $css );
	$css = str_replace( '; ', ';', $css );
	$css = str_replace( ': ', ':', $css );
	$css = str_replace( ' {', '{', $css );
	$css = str_replace( '{ ', '{', $css );
	$css = str_replace( ', ', ',', $css );
	$css = str_replace( '} ', '}', $css );
	$css = str_replace( ';}', '}', $css );

	return trim( $css );
}
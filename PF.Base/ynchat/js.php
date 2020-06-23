<?php
;

require_once 'cli.php';

if (phpversion() >= '5') {
	include_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'jsmin.php');
}

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

$type = 'ynchatcore';
$name = 'default';
if (!empty($_REQUEST['type']) && !empty($_REQUEST['name'])) {
	$type = cleanInput($_REQUEST['type']);
	$name = cleanInput($_REQUEST['name']);
} 

$subtype = '';
if(!empty($_REQUEST['subtype'])){
	$subtype = cleanInput($_REQUEST['subtype']);
}

$cbfn = '';
if (!empty($_REQUEST['callbackfn'])) {
	$cbfn = $_REQUEST['callbackfn'];
} 
// if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.$cbfn.$type.$name.'.js') && YNCHAT_DEBUG == false) {
// 	if (!empty($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == filemtime(dirname(__FILE__).DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.$cbfn.$type.$name.'.js')) {
// 		header("HTTP/1.1 304 Not Modified");
// 		exit;
// 	}
// 	readfile(dirname(__FILE__).DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.$cbfn.$type.$name.'.js');
// 	$js = ob_get_clean();

// } else {

	if ((defined('YNCHAT_INCLUDE_JQUERY') && YNCHAT_INCLUDE_JQUERY == 1) && empty($_GET['callbackfn'])) {
		include_once (dirname(__FILE__).DIRECTORY_SEPARATOR."static".DIRECTORY_SEPARATOR."jscript".DIRECTORY_SEPARATOR."jquery.js");
	}

	include_once (dirname(__FILE__).DIRECTORY_SEPARATOR."static".DIRECTORY_SEPARATOR."jscript".DIRECTORY_SEPARATOR."bsn.AutoSuggest_2.1.3.js");
	include_once (dirname(__FILE__).DIRECTORY_SEPARATOR."static".DIRECTORY_SEPARATOR."jscript".DIRECTORY_SEPARATOR."jquery.form.js");
	include_once (dirname(__FILE__).DIRECTORY_SEPARATOR."static".DIRECTORY_SEPARATOR."jscript".DIRECTORY_SEPARATOR."jquery.caret.1.02.js");
	include_once (dirname(__FILE__).DIRECTORY_SEPARATOR."static".DIRECTORY_SEPARATOR."jscript".DIRECTORY_SEPARATOR."jquery.tokeninput.js");
	include_once (dirname(__FILE__).DIRECTORY_SEPARATOR."static".DIRECTORY_SEPARATOR."jscript".DIRECTORY_SEPARATOR."jquery.tinysort.js");
	include_once (dirname(__FILE__).DIRECTORY_SEPARATOR."static".DIRECTORY_SEPARATOR."jscript".DIRECTORY_SEPARATOR."jquery.embedly-3.1.1.min.js");
	include_once (dirname(__FILE__).DIRECTORY_SEPARATOR."static".DIRECTORY_SEPARATOR."jscript".DIRECTORY_SEPARATOR."jquery.liveurl.js");
	// include_once (dirname(__FILE__).DIRECTORY_SEPARATOR."static".DIRECTORY_SEPARATOR."jscript".DIRECTORY_SEPARATOR."jquery.mb.audio.js");
	include_once (dirname(__FILE__).DIRECTORY_SEPARATOR."static".DIRECTORY_SEPARATOR."jscript".DIRECTORY_SEPARATOR."ynhelper.js");
	include_once (dirname(__FILE__).DIRECTORY_SEPARATOR."static".DIRECTORY_SEPARATOR."jscript".DIRECTORY_SEPARATOR."ynchat.js");

	if (phpversion() >= '5') {
		$js = JSMin::minify(ob_get_clean());
    } else {
		$js = ob_get_clean();
	}

	$fp = @fopen(dirname(__FILE__).DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.$cbfn.$type.$name.'.js', 'w'); 
	@fwrite($fp, $js);
	@fclose($fp);

// }

if (phpversion() >= '4.0.4pl1' && (strstr($useragent,'compatible') || strstr($useragent,'Gecko'))) {
	if (extension_loaded('zlib') && YNCHAT_GZIP_ENABLED == 1) {
		ob_start('ob_gzhandler');
	} else { ob_start(); }
} else { ob_start(); }

$lastModified = filemtime(dirname(__FILE__).DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.$cbfn.$type.$name.'.js');

header('Content-type: text/javascript;charset=utf-8');
header("Last-Modified: ".gmdate("D, d M Y H:i:s", $lastModified)." GMT");
header('Expires: '.gmdate("D, d M Y H:i:s", time() + 3600*24*365).' GMT');

echo $js;

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
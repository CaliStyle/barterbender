<?php

$settings = phpfox::getService('gettingstarted.settings')->getSettings(0);
if  (($settings["active_getting_started"]) && isset($_SESSION['check_login']) && ($_SESSION['check_login']))
{
	$corepath=phpfox::getParam("core.path");
	echo "<script src='".$corepath."module/gettingstarted/static/jscript/gettingstarted.js' type='text/javascript'></script>";
	echo "<link href='".$corepath."module/gettingstarted/static/css/default/default/thickbox.css' type='text/css' rel='stylesheet'>";
	Phpfox::getService("gettingstarted.todolist")->showTodoList();
	$_SESSION['check_login'] = false;
}
?>




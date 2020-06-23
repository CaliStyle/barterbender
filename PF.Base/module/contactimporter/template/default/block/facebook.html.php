<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Blog
 * @version 		$Id: top.html.php 1318 2009-12-14 22:34:04Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>     
<div id="js_report_body">
	{_p var='facebook_network_requires_the_authorization_to_access_and_get_contacts_from_your_account'}.
	<div class="main_break" align="center">  
	    <button type="button" value="{phrase var='core.yes'}" class="btn btn-sm btn-primary" onclick="location.href='{$core_path}/module/socialbridge/static/php/facebook.php?callbackUrl={$sCallback}&bredirect=1';">{phrase var='core.yes'}</button>      
	    <button type="button" value="{phrase var='core.no'}" class="btn btn-sm btn-default" onclick="tb_remove();">{phrase var='core.no'}</button>
	</div>
</div>
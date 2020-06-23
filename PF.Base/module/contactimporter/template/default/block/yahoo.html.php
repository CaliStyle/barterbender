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
   {_p var='login_to_access_and_get_yahoo_contacts_from_your_account'}.
	<div align="center" class="main_break">
	  <button type="button" value="{phrase var='core.yes'}" class="btn btn-sm btn-primary" onclick="window.location='{$sCentralizeUrl}?service=yahoo&login=1&security_token={$sSecurityToken}&token_name={$tokenName}&callbackUrl={$sCallback}','name','height=400,width=550';
	        tb_remove();if (window.focus) newwindow.focus();tb_remove();redirect();">{phrase var='core.yes'}</button>    
	  <button type="button" value="{phrase var='core.no'}" class="btn btn-sm btn-default" onclick="tb_remove();">{phrase var='core.no'}</button>
	</div>  
</div>            
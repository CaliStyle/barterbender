<?php
defined('PHPFOX') or exit('NO DICE!');

?>
<div id="js_report_body">
	{_p var='login_to_access_and_get_gmail_contacts_from_your_account'}.
	<div class="main_break" align="center">
		<button type="button" value="{phrase var='core.yes'}" class="btn btn-sm btn-primary" onclick="window.location='{$sCentralizeUrl}?service=gmail&login=1&security_token={$sSecurityToken}&token_name={$tokenName}&callbackUrl={$sCallback}','name','height=400,width=550';if (window.focus) newwindow.focus();tb_remove();redirect();">{phrase var='core.yes'}</button>
		<button type="button" value="{phrase var='core.no'}" class="btn btn-sm btn-default" onclick="tb_remove();">{phrase var='core.no'}</button>
	</div>
</div>
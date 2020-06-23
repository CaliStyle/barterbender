<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         VuDP, TienNPL
 * @package        Module_Resume
 * @version        3.01
 * 
 */
?>


<div>
	{_p var='upgrade_to_premium_account_to_see_all_resume_as_well_as_use_who_s_viewed_me_service'}
	<span style="margin-top: 5px;display:block;">
		<button onclick="tb_remove();$.ajaxCall('resume.upgradeAccount','view=1');return false;" type="button" class="button btn btn-primary btn-sm ynr-button-upgradepremiumaccount">{_p var='upgrade_to_view'}</button>
		<button onclick="tb_remove();return false;"
			   value="" type="button" class="button buton_off btn btn-default btn-sm">{_p var='cancel'}</button>
	</span>
</div>

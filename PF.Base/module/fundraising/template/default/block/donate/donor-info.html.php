<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

?>
<div class="ynfr donor-info">
{if $bIsGuest}
	<p class="extra_info">{phrase var='full_name'}</p>
	<p><input type="text" id="ynfr_donor_fullname" name="val[fullname]" size="60"/></p>
	<p class="extra_info">{phrase var='email'}</p>
	<p><input type="text" id="ynyr_donor_email" class="email" name="val[email]" size="60"/></p>
{/if}

<p class="extra_info">{phrase var='leave_your_message'}</p>
<p><textarea style="width: 100%;" rows="6" name="val[message]"></textarea></p>

<div class="extra_info">
{phrase var='leave_message_extra_info'}
</div>
</div>
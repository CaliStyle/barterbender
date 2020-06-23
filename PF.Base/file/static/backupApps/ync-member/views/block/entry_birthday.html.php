<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="ynmember_block_item_inner">
	<div class="ynmember_birthday_avatar">
		{if $aUser.user_image}
			<a href="{url link=$aUser.user_name}" title="{$aUser.full_name}" style="background-image: url('{img user=$aUser suffix='_200_square' return_url=true}');"></a>
		{else}
			{img user=$aUser suffix='_200_square' return_url=true}
		{/if}
	</div>
	<div class="ynmember_birthday_info">
	    {$aUser|user}
	    <div class="ynmember_birthday_info_bg" style="background-image: url('{param var='core.path_actual'}PF.Site/Apps/ync-member/assets/image/cake.png')"></div>
        {if Phpfox::isUser() && Phpfox::getUserId() != $aUser.user_id}
	    <a href="{url link='ynmember.birthdaywish'}" title="{_p var='Send your wishes'}" class="ynmember_wisher_button popup" data-toggle="modal" data-target="#ynmember_birthday_modal">{_p var='Send your wishes'}</a>
        {/if}
	</div>
</div>
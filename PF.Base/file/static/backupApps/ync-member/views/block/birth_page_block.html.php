<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="ynmember_avatar">
	{if $aUser.user_image}
		<a href="{url link=$aUser.user_name}" title="{$aUser.full_name}" style="background-image: url('{img user=$aUser suffix='_200_square' return_url=true}');"></a>
	{else}
		{img user=$aUser suffix='_200_square' return_url=true}
	{/if}
	{if $aUser.is_featured}
	    <i class="fa fa-diamond ynmember_feature_icon" aria-hidden="true"></i>
	{/if}
</div>
<div class="ynmember_info{if $aUser.is_online} active{/if}">
	<span class="ynicon{if $aUser.is_online} active{/if}"></span>
	{$aUser|user}
	<div class="date">{$aUser.birthdate}</div>
</div>
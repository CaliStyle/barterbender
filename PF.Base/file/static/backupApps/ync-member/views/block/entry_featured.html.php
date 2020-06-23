<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="ms-slide">
	<div class="ynmember_feature_item">
<!--		<a href="javascript:void(0)" class="btn btn-success ynmember_add_friend{if $aUser.is_friend} friended{elseif $aUser.is_friend_request == 2} confirm{elseif $aUser.is_friend_request == 3} waiting{/if}">-->
<!--			<i class="fa fa-user-plus" aria-hidden="true"></i>-->
<!--		</a>-->
		<div class="ynmember_feature_photo">
		    {if $aUser.user_image}
				<a href="{url link=$aUser.user_name}" title="{$aUser.full_name}" style="background-image: url('{img user=$aUser suffix='_200_square' return_url=true}');"></a>
			{else}
				{img user=$aUser suffix='_200_square' return_url=true}
			{/if}
		</div>
		<div class="ynmember_feature_info">
			<div class="ynmember_rating_block">
            	<span class="rating_star">
	            	{$aUser.rating|ynmember_rating}
            	</span>
            </div>
            {$aUser|user}
		</div>
	</div>
</div>
<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="sticky-label-icon sticky-featured-icon">
	<span class="flag-style-arrow"></span>
	<i class="ico ico-diamond"></i>
</div>
<div class="featured-members-title">
    {_p var='follow_some_of_your_favorite_person_now'}
</div>
<ul class="member-listing featured-members landing-feature-member">
{foreach from=$aFeaturedUsers item=aUser name=featured}
	<li class="item-listing">
		<div class="item-outer">
			<div class="item-media" data-toggle="tooltip" data-placement="bottom" title="{$aUser.full_name}">
				{img user=$aUser suffix='_50_square' max_width=50 max_height=50}
			</div>
			
			<div class="item-inner">
				{$aUser|user:'':'':'':12:true}

				<div class="friend-info">
                    {module name='user.friendship' friend_user_id=$aUser.user_id extra_info=true no_button=true mutual_list=true}
                    {module name='user.info' friend_user_id=$aUser.user_id number_of_info=1}
                </div>
			</div>
		</div>
	</li>
{/foreach}
</ul>
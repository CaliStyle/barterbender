<?php

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * @copyright      YouNet Company
 * @author         TienNPL
 * @package        Module_Coupon
 * @version        3.01
 * 
 */
 ?>
{literal}
<script>
/**
 *  Favorite actions
 */
function FavoriteAction(sActionType, iItemId){
	if(sActionType=='favorite')
	{
		$('#js_unfavorite_link_'+iItemId).show(); 
		$('#js_favorite_link_'+iItemId).hide();
		tb_show("{/literal}{phrase var='core.notice'}{literal}", $.ajaxBox('coupon.addFavorite', 'width=' + 400 + '&id=' + iItemId));	
		return false;
	}
	else
	{
		$('#js_favorite_link_' + iItemId).show(); 
		$('#js_unfavorite_link_' + iItemId).hide();
		$.ajaxCall('coupon.deleteFavorite','id='+iItemId,'GET');
	}
}

/**
 *  Follow actions
 */
function FollowAction(sActionType, iItemId){
	if(sActionType=='follow')
	{
		$('#js_unfollow_link_'+iItemId).show(); 
		$('#js_follow_link_'+iItemId).hide();
		tb_show("{/literal}{phrase var='core.notice'}{literal}",$.ajaxBox('coupon.addFollow','width=' + 400 + '&id='+iItemId));
	    return false;
	}
	else
	{
		$('#js_follow_link_' + iItemId).show(); 
		$('#js_unfollow_link_' + iItemId).hide();
		$.ajaxCall('coupon.deleteFollow','id='+iItemId,'GET');
	}
}
</script>
{/literal}
<!-- Suggestion block listing space --> 
<div class="ync-photo-menu">
	<!-- Image content -->
	<div class="ync-detail-image" style="text-align: center;">
        {img server_id=$aCoupon.server_id path='core.url_pic' file=$aCoupon.image_path suffix='_400_square' width=140 height=140}
    </div>
    
    {if $aCoupon.is_approved}
	    <!-- Rating -->
	    <div class="ync-vote">
			<div class="ync-rate-display" style="margin-left:45px;">
				{module name='rate.display'}
			</div>
		</div>
		<div class="clear"></div>
		{if Phpfox::isUser()}
		<!-- Information content -->
		<div>
			<ul>
				<li id="coupon_detail_favorite_link">
                    <a href="javascript:void(0);" onclick="FavoriteAction('favorite',{$aCoupon.coupon_id});return false;"
                       id="js_favorite_link_{$aCoupon.coupon_id}" class="ync_btn ync_btn_favorite"
                       title="{phrase var='add_to_your_favorite'}" {if ($bIsFavorited)}style="display:none;" {/if}>
                    <i class="fa fa-star"></i>&nbsp;{phrase var='favorite'}
					</a>
                    <a href="javascript:void(0);" title="{phrase var='remove_from_your_favorite'}" href="#"
                       onclick="FavoriteAction('unfavorite',{$aCoupon.coupon_id});return false;"
                       class="ync_btn ync_btn_notfavorite" id="js_unfavorite_link_{$aCoupon.coupon_id}" {if
                       (!$bIsFavorited)}style="display:none;" {/if}>
                    <i class="fa fa-star"></i>&nbsp;{phrase var='unfavorite'}
					</a>
				</li>
			</ul>
		</div>
		<div class="sub_section_menu ync_sub_section_menu">
			<ul>
				{if $bCanFollow}
				<li id="coupon_detail_follow_link">
                    <a href="javascript:void(0);" onclick="FollowAction('follow',{$aCoupon.coupon_id});return false;"
                       id="js_follow_link_{$aCoupon.coupon_id}" title="{phrase var='follow_this_coupon'}" {if
                       ($aCoupon.has_followed)}style="display:none;" {/if}>
                    {phrase var='follow'}
					</a>
                    <a href="javascript:voi(0);" title="{phrase var='unfollow_this_coupon'}"
                       onclick="FollowAction('unfollow',{$aCoupon.coupon_id});return false;"
                       id="js_unfollow_link_{$aCoupon.coupon_id}" {if (!$aCoupon.has_followed)}style="display:none;" {/if}>
                    {phrase var='unfollow'}
					</a>
				</li>
				{/if}
				<li>
                    <a href="javascript:void(0);"
                       onclick="$Core.box('coupon.inviteBlock',800,'id={$aCoupon.coupon_id}&url={permalink module='coupon.detail' id=$aCoupon.coupon_id title=$aCoupon.title}';) return false;">
                        {phrase var='invite_friends'}
					</a>
				</li>
			</ul>
		</div>
		{/if}
	{/if}
<div class="clear"></div>
</div>